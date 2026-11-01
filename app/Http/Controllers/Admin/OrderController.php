<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\OrderController as CustomerOrderController;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orders)
    {
    }

    public function index(Request $request): View
    {
        $query = Order::with(['user', 'orderItems.product'])->latest();

        $status = $request->input('status');
        if ($status && array_key_exists($status, Order::STATUS_META)) {
            $query->where('status', $status);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                    ->orWhere('tracking_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $orders = $query->paginate(10)->withQueryString();

        $statusCounts = Order::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.orders.index', compact('orders', 'statusCounts'));
    }

    public function show(Order $order): View
    {
        $order->load(['user', 'orderItems.product']);

        return view('admin.orders.show', compact('order'));
    }

    public function syncPayment(Order $order): RedirectResponse
    {
        return back()->with(...CustomerOrderController::syncMessage($this->orders->syncPaymentFromMidtrans($order)));
    }

    public function ship(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'courier' => ['required', 'string', 'max:100'],
            'tracking_number' => ['required', 'string', 'max:100'],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ], [
            'courier.required' => 'Nama kurir wajib diisi.',
            'tracking_number.required' => 'Nomor resi wajib diisi.',
        ]);

        if (!$this->orders->ship($order, $validated['courier'], $validated['tracking_number'], $validated['admin_note'] ?? null)) {
            return back()->with('error', 'Hanya pesanan berstatus "Dibayar" yang bisa dikirim.');
        }

        return back()->with('success', 'Pesanan '.$order->order_code.' ditandai dalam pengiriman.');
    }

    public function complete(Order $order): RedirectResponse
    {
        if (!$this->orders->complete($order)) {
            return back()->with('error', 'Hanya pesanan yang sedang dikirim yang bisa diselesaikan.');
        }

        return back()->with('success', 'Pesanan '.$order->order_code.' ditandai selesai.');
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:2000'],
        ], [
            'reason.required' => 'Alasan pembatalan wajib diisi.',
        ]);

        $wasPaid = $order->status === Order::STATUS_PAID;

        if (!$this->orders->cancel($order, 'Dibatalkan admin: '.$validated['reason'])) {
            return back()->with('error', 'Pesanan yang sudah dikirim / selesai tidak dapat dibatalkan.');
        }

        return back()->with('success', $wasPaid
            ? 'Pesanan dibatalkan & stok dikembalikan. Jangan lupa proses refund di dashboard Midtrans.'
            : 'Pesanan berhasil dibatalkan.');
    }
}
