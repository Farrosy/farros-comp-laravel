@extends('layouts.app', ['title' => 'Panel Admin - Kelola Produk'])

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h3 class="fw-bold mb-0">Manajemen Inventaris Laptop</h3>
        <p class="text-muted small mb-0">Kelola katalog stok unit laptop second Farros Comp</p>
    </div>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary fw-semibold">
        <i class="bi bi-plus-circle me-1"></i> Tambah Unit Baru
    </a>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.products.index') }}" method="GET" class="row g-2 align-items-center">
            <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
            <input type="hidden" name="sort_order" value="{{ request('sort_order', 'desc') }}">

            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama, prosesor, spek, kondisi..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-md-3">
                <select name="category_id" class="form-select">
                    <option value="">-- Semua Brand --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <select class="form-select" aria-label="Urutkan produk" onchange="applySort(this.value)">
                    <option value="created_at,desc" @selected(request('sort_by', 'created_at') === 'created_at' && request('sort_order', 'desc') === 'desc')>Terbaru</option>
                    <option value="name,asc" @selected(request('sort_by') === 'name' && request('sort_order') === 'asc')>Nama A - Z</option>
                    <option value="name,desc" @selected(request('sort_by') === 'name' && request('sort_order') === 'desc')>Nama Z - A</option>
                    <option value="price,asc" @selected(request('sort_by') === 'price' && request('sort_order') === 'asc')>Harga Terendah</option>
                    <option value="price,desc" @selected(request('sort_by') === 'price' && request('sort_order') === 'desc')>Harga Tertinggi</option>
                    <option value="stock,desc" @selected(request('sort_by') === 'stock' && request('sort_order') === 'desc')>Stok Terbanyak</option>
                    <option value="stock,asc" @selected(request('sort_by') === 'stock' && request('sort_order') === 'asc')>Stok Tersedikit</option>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-dark w-100 fw-semibold">Filter</button>
                @if(request()->hasAny(['search', 'category_id', 'sort_by']))
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary" title="Reset pencarian" aria-label="Reset pencarian">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

@php
    $sortLink = function (string $column, string $title) {
        $currentSort = request('sort_by', 'created_at');
        $currentOrder = request('sort_order', 'desc');
        $nextOrder = ($currentSort === $column && $currentOrder === 'asc') ? 'desc' : 'asc';
        $icon = '';

        if ($currentSort === $column) {
            $icon = $currentOrder === 'asc'
                ? '<i class="bi bi-arrow-up-short"></i>'
                : '<i class="bi bi-arrow-down-short"></i>';
        }

        $params = array_merge(request()->except('page'), [
            'sort_by' => $column,
            'sort_order' => $nextOrder,
        ]);

        return new \Illuminate\Support\HtmlString(
            '<a href="'.e(route('admin.products.index', $params)).'" class="text-decoration-none text-dark fw-bold">'.e($title).' '.$icon.'</a>'
        );
    };
@endphp

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">{!! $sortLink('name', 'Unit Laptop') !!}</th>
                        <th>{!! $sortLink('category', 'Kategori') !!}</th>
                        <th>Spesifikasi Pokok</th>
                        <th>{!! $sortLink('physical_condition', 'Kondisi Fisik') !!}</th>
                        <th class="text-nowrap">{!! $sortLink('price', 'Harga Satuan') !!}</th>
                        <th>{!! $sortLink('stock', 'Stok') !!}</th>
                        <th class="text-end pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold">{{ $product->name }}</div>
                                <small class="text-muted">{{ $product->battery_condition }}</small>
                            </td>
                            <td><span class="badge bg-dark">{{ $product->category->name }}</span></td>
                            <td>
                                <div class="small fw-semibold">{{ $product->processor }}</div>
                                <div class="small text-muted">{{ $product->ram_gb }}GB RAM | {{ $product->storage }}</div>
                            </td>
                            <td><span class="spec-badge">{{ $product->physical_condition }}</span></td>
                            <td class="fw-bold text-primary text-nowrap">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge {{ $product->stock > 0 ? 'bg-success' : 'bg-danger' }}">{{ $product->stock }} unit</span>
                            </td>
                            <td class="text-end pe-3 text-nowrap">
                                <a href="{{ route('admin.products.show', $product) }}" class="btn btn-sm btn-outline-secondary me-1" aria-label="Lihat {{ $product->name }}">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                @if($product->order_items_count > 0)
                                    <button type="button" class="btn btn-sm btn-outline-secondary" disabled title="Produk tercatat dalam pesanan">
                                        <i class="bi bi-lock"></i> Terkait Pesanan
                                    </button>
                                @else
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus unit laptop ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-search mb-2" style="font-size: 2rem;"></i>
                                    <p class="mb-0">Tidak ada unit laptop yang sesuai dengan kriteria filter/pencarian.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($products->hasPages())
        <div class="card-footer bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="text-muted small">
                Menampilkan {{ $products->firstItem() }} - {{ $products->lastItem() }} dari {{ $products->total() }} unit
            </span>
            <div>{{ $products->links() }}</div>
        </div>
    @endif
</div>

<script>
function applySort(value) {
    const [sortBy, sortOrder] = value.split(',');
    const url = new URL(window.location.href);
    url.searchParams.set('sort_by', sortBy);
    url.searchParams.set('sort_order', sortOrder);
    url.searchParams.delete('page');
    window.location.href = url.toString();
}
</script>
@endsection
