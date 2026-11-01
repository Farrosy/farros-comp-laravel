<?php

namespace App\Providers;

use App\Models\Order;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Jumlah pesanan yang sudah dibayar & menunggu diproses, untuk badge di sidebar admin.
        View::composer('layouts.app', function ($view) {
            $view->with('adminOrdersToProcess', Auth::user()?->role === 'admin'
                ? Order::where('status', Order::STATUS_PAID)->count()
                : 0);
        });
    }
}
