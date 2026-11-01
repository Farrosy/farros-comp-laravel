<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'to_process' => Order::where('status', Order::STATUS_PAID)->count(),
            'pending_payment' => Order::where('status', Order::STATUS_PENDING)->count(),
            'shipped' => Order::where('status', Order::STATUS_SHIPPED)->count(),
            'revenue_month' => (int) Order::whereIn('status', [Order::STATUS_PAID, Order::STATUS_SHIPPED, Order::STATUS_COMPLETED])
                ->where('paid_at', '>=', now()->startOfMonth())
                ->sum('total_amount'),
            'products' => Product::count(),
            'out_of_stock' => Product::where('stock', '<=', 0)->count(),
        ];

        $ordersToProcess = Order::with(['user', 'orderItems.product'])
            ->where('status', Order::STATUS_PAID)
            ->oldest('paid_at')
            ->limit(5)
            ->get();

        $latestOrders = Order::with('user')->latest()->limit(8)->get();

        $lowStockProducts = Product::where('stock', '<=', 1)->orderBy('stock')->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'ordersToProcess', 'latestOrders', 'lowStockProducts'));
    }
}
