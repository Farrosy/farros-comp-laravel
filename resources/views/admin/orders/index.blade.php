@extends('layouts.app', ['title' => 'Panel Admin - Kelola Pesanan'])

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h3 class="fw-bold mb-0">Manajemen Pesanan</h3>
        <p class="text-muted small mb-0">Verifikasi pembayaran, kirim unit, dan selesaikan pesanan pelanggan</p>
    </div>
</div>

@php($activeStatus = request('status'))
<ul class="nav nav-pills gap-1 mb-3 flex-nowrap overflow-auto">
    <li class="nav-item">
        <a class="nav-link text-nowrap {{ !$activeStatus ? 'active bg-dark' : 'text-dark' }}" href="{{ route('admin.orders.index', request()->except('status', 'page')) }}">
            Semua <span class="badge bg-secondary ms-1">{{ $statusCounts->sum() }}</span>
        </a>
    </li>
    @foreach(\App\Models\Order::STATUS_META as $key => $meta)
        <li class="nav-item">
            <a class="nav-link text-nowrap {{ $activeStatus === $key ? 'active '.$meta['badge'] : 'text-dark' }}" href="{{ route('admin.orders.index', array_merge(request()->except('page'), ['status' => $key])) }}" @if($activeStatus === $key) aria-current="page" @endif>
                <i class="bi {{ $meta['icon'] }} me-1"></i>{{ $meta['label'] }}
                <span class="badge {{ $meta['badge'] }} ms-1">{{ $statusCounts[$key] ?? 0 }}</span>
            </a>
        </li>
    @endforeach
</ul>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-2 align-items-center">
            @if($activeStatus)
                <input type="hidden" name="status" value="{{ $activeStatus }}">
            @endif
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari kode pesanan, nama / email pelanggan, nomor resi..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-4 d-flex gap-1">
                <button type="submit" class="btn btn-dark w-100 fw-semibold">Cari</button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary" title="Reset" aria-label="Reset filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Kode Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Unit</th>
                        <th class="text-nowrap">Total</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold">{{ $order->order_code }}</div>
                                <small class="text-muted">{{ $order->created_at->format('d M Y, H:i') }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $order->user?->name ?? '-' }}</div>
                                <small class="text-muted">{{ $order->user?->phone ?? $order->user?->email }}</small>
                            </td>
                            <td class="small">
                                {{ $order->orderItems->map(fn ($item) => $item->product?->name ?? 'Unit dihapus')->join(', ') }}
                            </td>
                            <td class="fw-bold text-primary text-nowrap">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            <td>
                                @include('partials.order-status-badge')
                                @if($order->tracking_number)
                                    <div class="small text-muted mt-1">{{ $order->courier }} &middot; {{ $order->tracking_number }}</div>
                                @endif
                            </td>
                            <td class="text-end pe-3 text-nowrap">
                                @if($order->isPending())
                                    <form action="{{ route('admin.orders.sync', $order) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-warning text-dark me-1" title="Cek status pembayaran ke Midtrans">
                                            <i class="bi bi-arrow-repeat"></i> Cek Bayar
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm {{ $order->canBeShipped() ? 'btn-primary' : 'btn-outline-secondary' }}">
                                    @if($order->canBeShipped())
                                        <i class="bi bi-truck"></i> Proses
                                    @else
                                        <i class="bi bi-eye"></i> Detail
                                    @endif
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox mb-2" style="font-size: 2rem;"></i>
                                <p class="mb-0">Tidak ada pesanan yang sesuai filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($orders->hasPages())
        <div class="card-footer bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="text-muted small">Menampilkan {{ $orders->firstItem() }} - {{ $orders->lastItem() }} dari {{ $orders->total() }} pesanan</span>
            <div>{{ $orders->links() }}</div>
        </div>
    @endif
</div>
@endsection
