@extends('layouts.app', ['title' => 'Dashboard Admin - Farros Comp'])

@section('content')
<div class="mb-4">
    <h3 class="fw-bold mb-0">Dashboard Admin</h3>
    <p class="text-muted small mb-0">Ringkasan operasional toko per {{ now()->format('d M Y, H:i') }} WIB</p>
</div>

<div class="row g-3 mb-4">
    @foreach([
        ['label' => 'Perlu Diproses', 'value' => $stats['to_process'], 'icon' => 'bi-box-seam', 'color' => '#2563eb', 'url' => route('admin.orders.index', ['status' => 'paid'])],
        ['label' => 'Menunggu Pembayaran', 'value' => $stats['pending_payment'], 'icon' => 'bi-clock-history', 'color' => '#d97706', 'url' => route('admin.orders.index', ['status' => 'pending'])],
        ['label' => 'Dalam Pengiriman', 'value' => $stats['shipped'], 'icon' => 'bi-truck', 'color' => '#0891b2', 'url' => route('admin.orders.index', ['status' => 'shipped'])],
        ['label' => 'Omzet Bulan Ini', 'value' => 'Rp '.number_format($stats['revenue_month'], 0, ',', '.'), 'icon' => 'bi-cash-stack', 'color' => '#16a34a', 'url' => route('admin.orders.index')],
    ] as $card)
        <div class="col-sm-6 col-xl-3">
            <a href="{{ $card['url'] }}" class="text-decoration-none">
                <div class="card stat-card shadow-sm h-100" style="--stat-color: {{ $card['color'] }}">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon"><i class="bi {{ $card['icon'] }}"></i></div>
                        <div>
                            <div class="text-muted small">{{ $card['label'] }}</div>
                            <div class="fs-4 fw-bold text-dark">{{ $card['value'] }}</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>

<div class="row g-4">
    <div class="col-xl-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-box-seam text-primary me-1"></i> Antrean Pesanan Siap Dikirim</h6>
                <a href="{{ route('admin.orders.index', ['status' => 'paid']) }}" class="small">Lihat semua</a>
            </div>
            <div class="list-group list-group-flush">
                @forelse($ordersToProcess as $order)
                    <a href="{{ route('admin.orders.show', $order) }}" class="list-group-item list-group-item-action py-3 d-flex justify-content-between align-items-center gap-3">
                        <div>
                            <div class="fw-bold">{{ $order->order_code }}</div>
                            <div class="small text-muted">
                                {{ $order->user?->name ?? 'Pelanggan' }} &middot;
                                {{ $order->orderItems->map(fn ($item) => $item->product?->name)->filter()->join(', ') }}
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold text-primary text-nowrap">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                            <div class="small text-muted">Dibayar {{ $order->paid_at?->diffForHumans() ?? '-' }}</div>
                        </div>
                    </a>
                @empty
                    <div class="list-group-item py-4 text-center text-muted small">
                        <i class="bi bi-check2-all fs-3 d-block mb-1"></i>
                        Tidak ada pesanan yang menunggu diproses.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-receipt me-1"></i> Pesanan Terbaru</h6>
                <a href="{{ route('admin.orders.index') }}" class="small">Lihat semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Kode</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th class="pe-3">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($latestOrders as $order)
                            <tr onclick="window.location='{{ route('admin.orders.show', $order) }}'" style="cursor: pointer;">
                                <td class="ps-3 fw-semibold">{{ $order->order_code }}</td>
                                <td>{{ $order->user?->name ?? '-' }}</td>
                                <td class="text-nowrap">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                <td>@include('partials.order-status-badge')</td>
                                <td class="pe-3 text-nowrap">{{ $order->created_at->format('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">Belum ada pesanan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-laptop me-1"></i> Inventaris</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Total unit terdaftar</span>
                    <span class="fw-bold">{{ $stats['products'] }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted small">Stok habis</span>
                    <span class="fw-bold {{ $stats['out_of_stock'] > 0 ? 'text-danger' : '' }}">{{ $stats['out_of_stock'] }}</span>
                </div>

                <div class="small fw-semibold mb-2">Stok menipis (&le; 1 unit)</div>
                @forelse($lowStockProducts as $product)
                    <a href="{{ route('admin.products.edit', $product) }}" class="d-flex justify-content-between align-items-center py-2 border-top text-decoration-none text-dark small">
                        <span class="text-truncate me-2">{{ $product->name }}</span>
                        <span class="badge {{ $product->stock > 0 ? 'bg-warning text-dark' : 'bg-danger' }}">{{ $product->stock }} unit</span>
                    </a>
                @empty
                    <div class="text-muted small">Semua stok aman.</div>
                @endforelse

                <div class="d-grid gap-2 mt-3">
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm fw-semibold"><i class="bi bi-plus-circle me-1"></i> Tambah Unit Baru</a>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-dark btn-sm">Kelola Produk</a>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body small">
                <h6 class="fw-bold"><i class="bi bi-diagram-3 me-1"></i> Alur Pesanan</h6>
                <ol class="mb-0 ps-3 text-muted">
                    <li><strong class="text-dark">Menunggu Pembayaran</strong> &ndash; pelanggan checkout via Midtrans. Jika status tidak berubah padahal sudah bayar, klik <em>Cek Status Pembayaran</em>.</li>
                    <li><strong class="text-dark">Dibayar</strong> &ndash; siapkan unit, lalu input kurir &amp; nomor resi.</li>
                    <li><strong class="text-dark">Dalam Pengiriman</strong> &ndash; pelanggan mengonfirmasi diterima, atau admin menandai selesai.</li>
                    <li><strong class="text-dark">Selesai</strong>.</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection
