<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\OrderService;
use Illuminate\View\View;
use Midtrans\Snap;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orders)
    {
        // OrderService sudah mengatur konfigurasi Midtrans (server key, mode, 3DS).
    }

    public function index(): View
    {
        $orders = Order::with(['orderItems.product'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    public function checkout(Request $request, Product $product): RedirectResponse
    {
        if (!config('services.midtrans.server_key')) {
            return back()->with('error', 'Konfigurasi pembayaran belum tersedia. Silakan hubungi Customer Service.');
        }

        $user = $request->user();

        try {
            $order = DB::transaction(function () use ($product, $user) {
                $currentProduct = Product::query()->whereKey($product->id)->lockForUpdate()->firstOrFail();

                if ($currentProduct->stock < 1) {
                    throw new \RuntimeException('OUT_OF_STOCK');
                }

                $order = Order::create([
                    'user_id' => $user->id,
                    'order_code' => 'FC-'.now()->format('YmdHis').'-'.Str::upper(Str::random(8)),
                    'total_amount' => (int) $currentProduct->price,
                    'shipping_address' => $user->address ?: 'Alamat belum diatur, silakan konfirmasi via CS.',
                    'status' => Order::STATUS_PENDING,
                ]);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $currentProduct->id,
                    'quantity' => 1,
                    'unit_price' => $currentProduct->price,
                    'subtotal' => $currentProduct->price,
                ]);

                $params = [
                    'transaction_details' => [
                        'order_id' => $order->order_code,
                        'gross_amount' => (int) $currentProduct->price,
                    ],
                    'customer_details' => [
                        'first_name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone ?: '08123456789',
                        'billing_address' => [
                            'address' => $order->shipping_address,
                        ],
                    ],
                    'item_details' => [[
                        'id' => (string) $currentProduct->id,
                        'price' => (int) $currentProduct->price,
                        'quantity' => 1,
                        'name' => substr($currentProduct->name, 0, 45),
                    ]],
                ];

                $order->update(['snap_token' => Snap::getSnapToken($params)]);

                return $order;
            });
        } catch (\RuntimeException $exception) {
            if ($exception->getMessage() === 'OUT_OF_STOCK') {
                return back()->with('error', 'Maaf, stok unit laptop ini sudah habis.');
            }

            report($exception);

            return back()->with('error', 'Gagal membuat pesanan. Silakan coba kembali atau hubungi Customer Service.');
        } catch (\Throwable $exception) {
            report($exception);

            return back()->with('error', 'Gagal memproses pembayaran. Silakan coba kembali atau hubungi Customer Service.');
        }

        return redirect()->route('orders.index')->with('success', 'Pesanan berhasil dibuat. Silakan lanjutkan pembayaran.');
    }

    public function callback(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'order_id' => ['required', 'string'],
            'status_code' => ['required', 'string'],
            'gross_amount' => ['required', 'numeric'],
            'signature_key' => ['required', 'string'],
            'transaction_status' => ['required', 'string'],
            'fraud_status' => ['nullable', 'string'],
        ]);

        $serverKey = (string) config('services.midtrans.server_key');
        if ($serverKey === '') {
            return response()->json(['message' => 'Payment configuration unavailable'], 503);
        }

        $expectedSignature = hash(
            'sha512',
            $payload['order_id'].$payload['status_code'].$payload['gross_amount'].$serverKey
        );

        if (!hash_equals($expectedSignature, $payload['signature_key'])) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // Midtrans's dashboard test notification uses a synthetic order ID.
        // Acknowledge it after signature verification without touching orders or stock.
        if (str_starts_with($payload['order_id'], 'payment_notif_test_')) {
            return response()->json(['status' => 'success', 'message' => 'Test notification received']);
        }

        $order = Order::query()->where('order_code', $payload['order_id'])->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $result = $this->orders->applyPaymentStatus(
            $order,
            $payload['transaction_status'],
            $payload['fraud_status'] ?? null,
            $payload['gross_amount'],
            $request->input('payment_type')
        );

        return match ($result) {
            'amount_mismatch' => response()->json(['message' => 'Amount mismatch'], 403),
            // Dicatat di admin_note untuk ditindaklanjuti admin; balas 200 agar Midtrans tidak mengirim ulang terus.
            'insufficient_stock' => response()->json(['status' => 'success', 'message' => 'Paid but insufficient stock, flagged for admin']),
            default => response()->json(['status' => 'success']),
        };
    }

    /**
     * Cek status pembayaran langsung ke Midtrans (cadangan bila callback tidak masuk).
     */
    public function syncPayment(Request $request, Order $order): RedirectResponse
    {
        abort_unless((int) $order->user_id === (int) $request->user()->id, 403);

        return back()->with(...$this->syncMessage($this->orders->syncPaymentFromMidtrans($order)));
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        abort_unless((int) $order->user_id === (int) $request->user()->id, 403);

        if (!$order->isPending()) {
            return back()->with('error', 'Pesanan yang sudah dibayar hanya dapat dibatalkan oleh admin. Silakan hubungi Customer Service.');
        }

        // Pastikan pelanggan belum membayar sebelum membatalkan.
        if ($this->orders->syncPaymentFromMidtrans($order) === 'error') {
            return back()->with('error', 'Tidak dapat memverifikasi status pembayaran saat ini. Coba lagi beberapa saat atau hubungi Customer Service.');
        }

        if (!$order->refresh()->isPending()) {
            return back()->with('error', 'Status pesanan sudah berubah ('.$order->statusLabel().'), pembatalan tidak dilakukan.');
        }

        $this->orders->cancel($order, 'Dibatalkan oleh pelanggan.');

        return back()->with('success', 'Pesanan '.$order->order_code.' berhasil dibatalkan.');
    }

    public function confirmReceived(Request $request, Order $order): RedirectResponse
    {
        abort_unless((int) $order->user_id === (int) $request->user()->id, 403);

        if (!$this->orders->complete($order)) {
            return back()->with('error', 'Pesanan ini belum dikirim sehingga belum bisa dikonfirmasi diterima.');
        }

        return back()->with('success', 'Terima kasih! Pesanan telah dikonfirmasi diterima.');
    }

    /**
     * @return array{0: string, 1: string}
     */
    public static function syncMessage(string $result): array
    {
        return match ($result) {
            'paid' => ['success', 'Pembayaran terkonfirmasi. Pesanan masuk antrean proses.'],
            'cancelled' => ['error', 'Transaksi dibatalkan / kedaluwarsa di Midtrans. Pesanan ditandai batal.'],
            'not_found' => ['error', 'Belum ada transaksi di Midtrans. Pelanggan belum memilih metode pembayaran.'],
            'insufficient_stock' => ['error', 'Pembayaran diterima tetapi stok tidak cukup. Perlu konfirmasi / refund manual.'],
            'amount_mismatch' => ['error', 'Nominal pembayaran di Midtrans tidak sesuai dengan total pesanan.'],
            'error' => ['error', 'Gagal menghubungi Midtrans. Coba beberapa saat lagi.'],
            default => ['success', 'Status pembayaran belum berubah (masih menunggu pembayaran).'],
        };
    }
}
