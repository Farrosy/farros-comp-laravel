<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\OrderController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

// Katalog Depan (Publik)
Route::get('/', function () {
    $products = Product::with('category')->latest()->get();
    return view('shop.index', compact('products'));
})->name('home');

// Rute Tamu (Hanya yang belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Rute Keluar (Harus sudah login)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/pesanan-saya', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/checkout/{product}', [OrderController::class, 'checkout'])->name('orders.checkout');
});

Route::post('/midtrans/callback', [OrderController::class, 'callback'])->name('midtrans.callback');

// Panduan Kondisi Unit & Garansi (Publik)
Route::get('/panduan-kondisi', function () {
    return view('pages.condition-guide');
})->name('condition.guide');

// Rute Khusus Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('products', AdminProductController::class);
    Route::post('/categories/quick-store', [AdminProductController::class, 'quickStoreCategory'])->name('categories.quick-store');
});
