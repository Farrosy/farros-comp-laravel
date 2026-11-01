<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Transaction;

/**
 * Satu tempat untuk semua perubahan status pesanan, supaya callback Midtrans,
 * sinkronisasi manual, dan aksi admin memakai aturan yang sama.
 */
class OrderService
{
    public function __construct()
    {
        Config::$serverKey = (string) config('services.midtrans.server_key');
        Config::$isProduction = (bool) config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Terapkan status transaksi Midtrans ke pesanan.
     * Hasil: 'paid', 'cancelled', 'unchanged', 'insufficient_stock', 'amount_mismatch'.
     */
    public function applyPaymentStatus(
        Order $order,
        string $transactionStatus,
        ?string $fraudStatus,
        float|int|string $grossAmount,
        ?string $paymentType = null
    ): string {
        return DB::transaction(function () use ($order, $transactionStatus, $fraudStatus, $grossAmount, $paymentType) {
            $order = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();

            $grossAmountCents = (int) round((float) $grossAmount * 100);
            $orderAmountCents = (int) round((float) $order->total_amount * 100);
            if ($grossAmountCents !== $orderAmountCents) {
                return 'amount_mismatch';
            }

            if (!$order->isPending()) {
                return 'unchanged';
            }

            $isSuccessfulPayment = $transactionStatus === 'settlement'
                || ($transactionStatus === 'capture' && $fraudStatus === 'accept');

            if ($isSuccessfulPayment) {
                $order->load('orderItems');

                if (!$this->reduceStock($order)) {
                    $order->update([
                        'admin_note' => trim(($order->admin_note ? $order->admin_note."\n" : '')
                            .'['.now()->format('d/m/Y H:i').'] Pembayaran diterima tetapi stok tidak cukup. Perlu refund / konfirmasi manual.'),
                    ]);

                    return 'insufficient_stock';
                }

                $order->update([
                    'status' => Order::STATUS_PAID,
                    'paid_at' => now(),
                    'payment_type' => $paymentType ?? $order->payment_type,
                ]);

                return 'paid';
            }

            if (in_array($transactionStatus, ['cancel', 'deny', 'expire'], true)) {
                $order->update([
                    'status' => Order::STATUS_CANCELLED,
                    'cancelled_at' => now(),
                    'payment_type' => $paymentType ?? $order->payment_type,
                ]);

                return 'cancelled';
            }

            return 'unchanged';
        });
    }

    /**
     * Tanya status langsung ke Midtrans. Dipakai bila callback tidak sampai
     * (misal aplikasi berjalan di localhost) sehingga pesanan tertahan "pending".
     * Hasil: sama seperti applyPaymentStatus(), ditambah 'not_found' dan 'error'.
     */
    public function syncPaymentFromMidtrans(Order $order): string
    {
        if (!config('services.midtrans.server_key')) {
            return 'error';
        }

        try {
            $response = Transaction::status($order->order_code);
        } catch (\Throwable $exception) {
            // 404 = pelanggan belum memilih metode pembayaran di Snap.
            if ((int) $exception->getCode() === 404) {
                return 'not_found';
            }

            report($exception);

            return 'error';
        }

        return $this->applyPaymentStatus(
            $order,
            (string) ($response->transaction_status ?? ''),
            $response->fraud_status ?? null,
            $response->gross_amount ?? 0,
            $response->payment_type ?? null
        );
    }

    public function ship(Order $order, string $courier, string $trackingNumber, ?string $note = null): bool
    {
        return DB::transaction(function () use ($order, $courier, $trackingNumber, $note) {
            $order = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();

            if (!$order->canBeShipped()) {
                return false;
            }

            $order->update([
                'status' => Order::STATUS_SHIPPED,
                'courier' => $courier,
                'tracking_number' => $trackingNumber,
                'shipped_at' => now(),
                'admin_note' => $note !== null && $note !== '' ? $note : $order->admin_note,
            ]);

            return true;
        });
    }

    public function complete(Order $order): bool
    {
        return DB::transaction(function () use ($order) {
            $order = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();

            if (!$order->canBeCompleted()) {
                return false;
            }

            $order->update([
                'status' => Order::STATUS_COMPLETED,
                'completed_at' => now(),
            ]);

            return true;
        });
    }

    /**
     * Batalkan pesanan. Jika sudah dibayar, stok dikembalikan
     * (refund dana dilakukan manual lewat dashboard Midtrans).
     */
    public function cancel(Order $order, ?string $reason = null): bool
    {
        return DB::transaction(function () use ($order, $reason) {
            $order = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();

            if (!$order->canBeCancelled()) {
                return false;
            }

            if ($order->status === Order::STATUS_PAID) {
                $order->load('orderItems');
                foreach ($order->orderItems as $item) {
                    Product::query()->whereKey($item->product_id)->increment('stock', $item->quantity);
                }
            } elseif (config('services.midtrans.server_key')) {
                // Tutup transaksi di Midtrans agar pelanggan tidak bisa membayar pesanan yang sudah batal.
                try {
                    Transaction::cancel($order->order_code);
                } catch (\Throwable) {
                    // Transaksi belum dibuat / sudah kedaluwarsa di Midtrans: aman diabaikan.
                }
            }

            $order->update([
                'status' => Order::STATUS_CANCELLED,
                'cancelled_at' => now(),
                'admin_note' => $reason !== null && $reason !== '' ? $reason : $order->admin_note,
            ]);

            return true;
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
