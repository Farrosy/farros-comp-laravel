<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Label, warna badge, dan ikon untuk tiap status.
     */
    public const STATUS_META = [
        self::STATUS_PENDING => ['label' => 'Menunggu Pembayaran', 'badge' => 'bg-warning text-dark', 'icon' => 'bi-clock-history'],
        self::STATUS_PAID => ['label' => 'Dibayar - Perlu Diproses', 'badge' => 'bg-primary', 'icon' => 'bi-box-seam'],
        self::STATUS_SHIPPED => ['label' => 'Dalam Pengiriman', 'badge' => 'bg-info text-dark', 'icon' => 'bi-truck'],
        self::STATUS_COMPLETED => ['label' => 'Selesai', 'badge' => 'bg-success', 'icon' => 'bi-patch-check'],
        self::STATUS_CANCELLED => ['label' => 'Dibatalkan', 'badge' => 'bg-danger', 'icon' => 'bi-x-circle'],
    ];

    protected $fillable = [
        'user_id',
        'order_code',
        'total_amount',
        'shipping_address',
        'status',
        'payment_receipt',
        'snap_token',
        'payment_type',
        'courier',
        'tracking_number',
        'admin_note',
        'paid_at',
        'shipped_at',
        'completed_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
            'shipped_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusLabel(): string
    {
        return self::STATUS_META[$this->status]['label'] ?? ucfirst((string) $this->status);
    }

    public function statusBadge(): string
    {
        return self::STATUS_META[$this->status]['badge'] ?? 'bg-secondary';
    }

    public function statusIcon(): string
    {
        return self::STATUS_META[$this->status]['icon'] ?? 'bi-question-circle';
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function canBeShipped(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function canBeCompleted(): bool
    {
        return $this->status === self::STATUS_SHIPPED;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_PAID], true);
    }
}
