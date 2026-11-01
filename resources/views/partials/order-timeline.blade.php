<ul class="order-timeline">
    <li class="done">
        <div class="fw-semibold">Pesanan dibuat</div>
        <div class="text-muted">{{ $order->created_at->format('d M Y H:i') }}</div>
    </li>
    <li class="{{ $order->paid_at ? 'done' : '' }}">
        <div class="fw-semibold">Pembayaran diterima</div>
        <div class="text-muted">{{ $order->paid_at?->format('d M Y H:i') ?? '-' }}</div>
    </li>
    <li class="{{ $order->shipped_at ? 'done' : '' }}">
        <div class="fw-semibold">Dikirim</div>
        <div class="text-muted">
            {{ $order->shipped_at?->format('d M Y H:i') ?? '-' }}
            @if($order->tracking_number)
                <br>{{ $order->courier }} &middot; Resi {{ $order->tracking_number }}
            @endif
        </div>
    </li>
    <li class="{{ $order->completed_at ? 'done' : '' }}">
        <div class="fw-semibold">Selesai</div>
        <div class="text-muted">{{ $order->completed_at?->format('d M Y H:i') ?? '-' }}</div>
    </li>
    @if($order->cancelled_at || $order->status === 'cancelled')
        <li class="done cancelled">
            <div class="fw-semibold text-danger">Dibatalkan</div>
            <div class="text-muted">{{ $order->cancelled_at?->format('d M Y H:i') ?? '-' }}</div>
        </li>
    @endif
</ul>
