@extends('layouts.app', ['title' => 'Detail Pesanan '.$order->order_code])

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <a href="{{ route('admin.orders.index') }}" class="small text-decoration-none"><i class="bi bi-arrow-left"></i> Kembali ke daftar pesanan</a>
        <h3 class="fw-bold mb-0 mt-1">{{ $order->order_code }}</h3>
        <p class="text-muted small mb-0">Dibuat {{ $order->created_at->format('d M Y, H:i') }} WIB</p>
    </div>
    <div class="fs-5">@include('partials.order-status-badge')</div>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0 small">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0">Item Pesanan</h6></div>
            <div class="card-body">
                @foreach($order->orderItems as $item)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom gap-3">
                        <div>
                            <div class="fw-bold">{{ $item->product?->name ?? 'Unit dihapus' }}</div>
                            @if($item->product)
                                <div class="small text-muted">{{ $item->product->processor }} &middot; {{ $item->product->ram_gb }}GB &middot; {{ $item->product->storage }} &middot; Stok saat ini: {{ $item->product->stock }}</div>
                            @endif
                            <div class="small text-muted">{{ $item->quantity }} unit &times; Rp {{ number_format($item->unit_price, 0, ',', '.') }}</div>
                        </div>
                        <div class="fw-bold text-nowrap">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                    </div>
                @endforeach
                <div class="d-flex justify-content-between pt-3">
                    <span class="fw-semibold">Total</span>
                    <span class="fs-5 fw-bold text-primary">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- ===== Panel aksi sesuai status ===== --}}
        <div class="card shadow-sm border-0 mb-4 border-start border-4 border-warning">
            <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0"><i class="bi bi-lightning-charge text-warning me-1"></i> Tindakan Admin</h6></div>
            <div class="card-body">
                @if($order->isPending())
                    <p class="small text-muted">Pesanan belum dibayar. Jika pelanggan mengaku sudah membayar, cek status langsung ke Midtrans (berguna bila notifikasi callback tidak masuk).</p>
                    <form action="{{ route('admin.orders.sync', $order) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-warning fw-semibold text-dark"><i class="bi bi-arrow-repeat me-1"></i> Cek Status Pembayaran</button>
                    </form>
                @elseif($order->canBeShipped())
                    <p class="small text-muted">Pembayaran sudah diterima{{ $order->paid_at ? ' pada '.$order->paid_at->format('d M Y H:i') : '' }}. Siapkan unit lalu input data pengiriman.</p>
                    <form action="{{ route('admin.orders.ship', $order) }}" method="POST" class="row g-2">
                        @csrf
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Kurir</label>
                            <input type="text" name="courier" class="form-control" list="courier-options" placeholder="JNE / J&T / SiCepat" value="{{ old('courier') }}" required>
                            <datalist id="courier-options">
                                <option value="JNE"><option value="J&T Express"><option value="SiCepat"><option value="AnterAja"><option value="Pos Indonesia"><option value="GoSend"><option value="GrabExpress"><option value="Ambil di Toko">
                            </datalist>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold">Nomor Resi</label>
                            <input type="text" name="tracking_number" class="form-control" value="{{ old('tracking_number') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Catatan (opsional)</label>
                            <textarea name="admin_note" rows="2" class="form-control" placeholder="Contoh: bonus tas & mouse">{{ old('admin_note') }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary fw-semibold"><i class="bi bi-truck me-1"></i> Tandai Dikirim</button>
                        </div>
                    </form>
                @elseif($order->canBeCompleted())
                    <p class="small text-muted">Unit sedang dikirim via <strong>{{ $order->courier }}</strong> (resi <strong>{{ $order->tracking_number }}</strong>). Tandai selesai jika unit sudah diterima pelanggan.</p>
                    <form action="{{ route('admin.orders.complete', $order) }}" method="POST" onsubmit="return confirm('Tandai pesanan ini selesai?')">
                        @csrf
                        <button type="submit" class="btn btn-success fw-semibold"><i class="bi bi-patch-check me-1"></i> Tandai Selesai</button>
                    </form>
                @else
                    <p class="small text-muted mb-0">Pesanan sudah {{ strtolower($order->statusLabel()) }}. Tidak ada tindakan lanjutan.</p>
                @endif

                @if($order->canBeCancelled())
                    <hr>
                    <details>
                        <summary class="small text-danger fw-semibold" style="cursor: pointer;">Batalkan pesanan</summary>
                        <form action="{{ route('admin.orders.cancel', $order) }}" method="POST" class="mt-2" onsubmit="return confirm('Yakin membatalkan pesanan ini?')">
                            @csrf
                            @if($order->status === 'paid')
                                <div class="alert alert-warning small py-2">Pesanan ini <strong>sudah dibayar</strong>. Stok akan dikembalikan otomatis, tetapi refund dana harus diproses manual di dashboard Midtrans.</div>
                            @endif
                            <textarea name="reason" rows="2" class="form-control mb-2" placeholder="Alasan pembatalan" required>{{ old('reason') }}</textarea>
                            <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-x-circle me-1"></i> Batalkan Pesanan</button>
                        </form>
                    </details>
                @endif
            </div>
        </div>

        @if($order->admin_note)
            <div class="card shadow-sm border-0">
                <div class="card-body small">
                    <h6 class="fw-bold"><i class="bi bi-journal-text me-1"></i> Catatan</h6>
                    <div class="text-muted" style="white-space: pre-line;">{{ $order->admin_note }}</div>
                </div>
            </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0">Pelanggan</h6></div>
            <div class="card-body small">
                <div class="fw-bold">{{ $order->user?->name ?? '-' }}</div>
                <div class="text-muted">{{ $order->user?->email }}</div>
                @if($order->user?->phone)
                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/\D/', '', $order->user->phone)) }}?text={{ rawurlencode('Halo '.$order->user->name.', terkait pesanan '.$order->order_code.' di Farros Comp...') }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-success mt-2">
                        <i class="bi bi-whatsapp me-1"></i> {{ $order->user->phone }}
                    </a>
                @endif
                <hr>
                <div class="fw-semibold mb-1"><i class="bi bi-geo-alt me-1"></i> Alamat Pengiriman</div>
                <div class="text-muted">{{ $order->shipping_address }}</div>
                @if($order->payment_type)
                    <hr>
                    <div class="fw-semibold mb-1"><i class="bi bi-credit-card me-1"></i> Metode Bayar</div>
                    <div class="text-muted">{{ strtoupper(str_replace('_', ' ', $order->payment_type)) }}</div>
                @endif
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0">Riwayat Status</h6></div>
            <div class="card-body small">
                @include('partials.order-timeline')
            </div>
        </div>
    </div>
</div>
@endsection
