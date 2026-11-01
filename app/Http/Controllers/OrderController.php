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
use Illuminate\View\View;
use Midtrans\Config;
use Midtrans\Snap;

class OrderController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = (string) config('services.midtrans.server_key');
        Config::$isProduction = (bool) config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
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
                    'status' => 'pending',
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

        return DB::transaction(function () use ($payload): JsonResponse {
            $order = Order::query()
                ->where('order_code', $payload['order_id'])
                ->lockForUpdate()
                ->first();

            if (!$order) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            $grossAmountCents = (int) round((float) $payload['gross_amount'] * 100);
            $orderAmountCents = (int) round((float) $order->total_amount * 100);
            if ($grossAmountCents !== $orderAmountCents) {
                return response()->json(['message' => 'Amount mismatch'], 403);
            }

            $transactionStatus = $payload['transaction_status'];
            $isSuccessfulPayment = $transactionStatus === 'settlement'
                || ($transactionStatus === 'capture' && ($payload['fraud_status'] ?? null) === 'accept');

            if ($order->status === 'pending' && $isSuccessfulPayment) {
                $order->load('orderItems');

                if (!$this->reduceStock($order)) {
                    return response()->json(['message' => 'Insufficient stock'], 409);
                }

                $order->update(['status' => 'paid']);
            } elseif ($order->status === 'pending' && in_array($transactionStatus, ['cancel', 'deny', 'expire'], true)) {
                $order->update(['status' => 'cancelled']);
            }

            return response()->json(['status' => 'success']);
        });
    }

    private function reduceStock(Order $order): bool
    {
        $productsToDecrement = [];

        foreach ($order->orderItems as $item) {
            $product = Product::query()->whereKey($item->product_id)->lockForUpdate()->first();

            if (!$product || $product->stock < $item->quantity) {
                return false;
            }

            $productsToDecrement[] = [$product, $item->quantity];
        }

        foreach ($productsToDecrement as [$product, $quantity]) {
            $product->decrement('stock', $quantity);
        }

        return true;
    }
}
