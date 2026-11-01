@extends('layouts.app', ['title' => $product->name . ' - Farros Comp'])

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Detail Produk</h1>
        <p class="text-muted mb-0">Informasi lengkap unit laptop.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning fw-semibold text-dark">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="row g-0">
        <div class="col-md-5 bg-light d-flex align-items-center justify-content-center p-4" style="min-height: 300px;">
            @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid" style="max-height: 280px; object-fit: contain;">
            @else
                <div class="text-center text-muted">
                    <i class="bi bi-laptop" style="font-size: 5rem;"></i>
                    <div>Foto unit belum diunggah</div>
                </div>
            @endif
        </div>
        <div class="col-md-7">
            <div class="card-body p-4">
                <span class="badge bg-secondary mb-2">{{ $product->category->name }}</span>
                <h2 class="h4 fw-bold">{{ $product->name }}</h2>
                <p class="text-primary fs-4 fw-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</p>

                <dl class="row mb-3">
                    <dt class="col-sm-5">Prosesor</dt>
                    <dd class="col-sm-7">{{ $product->processor }}</dd>
                    <dt class="col-sm-5">RAM</dt>
                    <dd class="col-sm-7">{{ $product->ram_gb }} GB</dd>
                    <dt class="col-sm-5">Penyimpanan</dt>
                    <dd class="col-sm-7">{{ $product->storage }}</dd>
                    <dt class="col-sm-5">Kondisi baterai</dt>
                    <dd class="col-sm-7">{{ $product->battery_condition }}</dd>
                    <dt class="col-sm-5">Kondisi fisik</dt>
                    <dd class="col-sm-7">{{ $product->physical_condition }}</dd>
                    <dt class="col-sm-5">Stok</dt>
                    <dd class="col-sm-7">{{ $product->stock }} unit</dd>
                </dl>

                <h3 class="h6 fw-bold">Deskripsi</h3>
                <p class="text-muted mb-0">{{ $product->description ?: 'Belum ada deskripsi.' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
