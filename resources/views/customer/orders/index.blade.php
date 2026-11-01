@extends('layouts.app', ['title' => 'Riwayat Pesanan Saya - Farros Comp'])

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <div>
                <h3 class="fw-bold mb-0">Pesanan Saya</h3>
                <p class="text-muted small mb-0">Pantau status transaksi dan bayar unit laptop pesanan Anda</p>
            </div>
            <a href="{{ route('home') }}" class="btn btn-outline-dark btn-sm">
                <i class="bi bi-cart-plus me-1"></i> Belanja Laptop Lain
            </a>
        </div>

        @forelse($orders as $order)
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <span class="fw-bold text-dark me-2">{{ $order->order_code }}</span>
                        <span class="text-muted small">{{ $order->created_at->format('d M Y, H:i') }} WIB</span>
                    </div>
                    <div>
                        @include('partials.order-status-badge')
                    </div>
                </div>
                <div class="card-body">
                    @foreach($order->orderItems as $item)
                        <div class="d-flex align-items-center justify-content-between py-2 border-bottom gap-3">
                            <div>
                                <h6 class="fw-bold mb-1">{{ $item->product?->name ?? 'Unit Laptop' }}</h6>
                                <div class="text-muted small">{{ $item->quantity }} unit × Rp {{ number_format($item->unit_price, 0, ',', '.') }}</div>
                            </div>
                            <div class="fw-bold text-primary text-nowrap">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                        </div>
                    @endforeach

                    @if($order->tracking_number)
                        <div class="alert alert-info small py-2 mt-3 mb-0 d-flex align-items-center">
                            <i class="bi bi-truck fs-5 me-2"></i>
                            <div>
                                Dikirim via <strong>{{ $order->courier }}</strong> &middot; No. Resi <strong>{{ $order->tracking_number }}</strong>
                                @if($order->shipped_at)
                                    <span class="text-muted">({{ $order->shipped_at->format('d M Y H:i') }})</span>
                                @endif
                            </div>
                        </div>
                    @elseif($order->status === 'paid')
                        <div class="alert alert-primary small py-2 mt-3 mb-0">
                            <i class="bi bi-box-seam me-1"></i> Pembayaran diterima. Unit sedang disiapkan tim gudang kami.
                        </div>
                    @endif

                    <div class="d-flex flex-wrap justify-content-between align-items-center mt-3 pt-2 gap-3">
                        <div class="small text-muted">
                            <i class="bi bi-geo-alt me-1"></i> Alamat Pengiriman: <strong>{{ $order->shipping_address }}</strong>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-3">
                            <div>
                                <span class="text-muted small">Total Tagihan:</span>
                                <h5 class="fw-bold text-dark mb-0">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</h5>
                            </div>

                            @if($order->isPending())
                                <form action="{{ route('orders.sync', $order) }}" method="POST" id="sync-form-{{ $order->id }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-secondary btn-sm" title="Sudah bayar tapi status belum berubah?">
                                        <i class="bi bi-arrow-repeat me-1"></i> Cek Status
                                    </button>
                                </form>
                                <form action="{{ route('orders.cancel', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('Batalkan pesanan ini?')">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-x-circle me-1"></i> Batalkan
                                    </button>
                                </form>
                                @if($order->snap_token)
                                    <button type="button" class="btn btn-warning fw-bold text-dark px-4" data-snap-token="{{ $order->snap_token }}" data-sync-form="sync-form-{{ $order->id }}" onclick="payWithMidtrans(this)">
                                        <i class="bi bi-credit-card me-1"></i> Bayar Sekarang
                                    </button>
                                @endif
                            @elseif($order->canBeCompleted())
                                <form action="{{ route('orders.received', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('Pastikan unit sudah Anda terima dalam kondisi baik. Lanjutkan?')">
                                    @csrf
                                    <button type="submit" class="btn btn-success fw-bold px-4">
                                        <i class="bi bi-box2-heart me-1"></i> Pesanan Diterima
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="card border-0 shadow-sm text-center py-5">
                <div class="card-body">
                    <i class="bi bi-bag-x text-muted" style="font-size: 3rem;"></i>
                    <h5 class="fw-bold mt-3">Belum Ada Transaksi</h5>
                    <p class="text-muted small mb-3">Anda belum pernah melakukan pemesanan unit laptop di Farros Comp.</p>
                    <a href="{{ route('home') }}" class="btn btn-primary fw-semibold px-4">Lihat Katalog Laptop</a>
                </div>
            </div>
        @endforelse

        @if($orders->hasPages())
            <div class="mt-4">{{ $orders->links() }}</div>
        @endif
    </div>
</div>

<script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
function payWithMidtrans(button) {
    // Setelah popup Snap selesai, cek status langsung ke Midtrans
    // supaya status pesanan ikut berubah walau callback belum/tidak masuk.
    const syncForm = document.getElementById(button.dataset.syncForm);
    const syncStatus = function () {
        if (syncForm) {
            syncForm.submit();
        } else {
            window.location.reload();
        }
    };

    window.snap.pay(button.dataset.snapToken, {
        onSuccess: syncStatus,
        onPending: syncStatus,
        onError: syncStatus,
        onClose: function () {}
    });
}
</script>
@endsection
