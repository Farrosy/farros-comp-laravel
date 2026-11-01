@extends('layouts.app', ['title' => 'Farros Comp - Spesialis Laptop Second Berkualitas'])

@section('content')
<div class="p-4 p-md-5 mb-4 rounded-3 text-white bg-dark shadow-sm">
    <div class="col-md-8 px-0">
        <span class="badge bg-warning text-dark fw-bold mb-2">UMKM Laptop Bekas Terpercaya</span>
        <h1 class="display-5 fw-bold">Farros Comp</h1>
        <p class="lead my-3">Pusat laptop <em>second-hand</em> bergaransi dan lolos uji teknis menyeluruh. Tersedia ThinkPad, Dell Latitude, hingga MacBook dengan kondisi prima siap kerja.</p>
        <a href="https://wa.me/6285691050420?text=Halo%20Tim%20Farros%20Comp%F0%9F%91%8B%E2%98%BA%EF%B8%8F%2C%20salam%20kenal.%0A%0ASaya%20baru%20lihat%20website%20Farros%20Comp%20%F0%9F%92%BB%20dan%20mau%20konsultasi%20santai%20untuk%20cari%20laptop%20second%20yang%20paling%20pas%20dengan%20kebutuhan%20dan%20budget%20saya.%0A%0A%F0%9F%93%84%20Berikut%20Formulir%20Kebutuhan%20Saya%3A%0A%E2%80%A2%20Nama%3A%20%0A%E2%80%A2%20Domisili%3A%20%0A%E2%80%A2%20Kebutuhan%20Utama%3A%20(Coding%20%2F%20Desain%20%2F%20Office%20Harian)%0A%E2%80%A2%20Software%20yang%20dipakai%3A%20%0A%E2%80%A2%20Preferensi%20Brand%3A%20(ThinkPad%20%2F%20Dell%20%2F%20MacBook)%0A%E2%80%A2%20Budget%20Maksimal%3A%20Rp%20%0A%E2%80%A2%20Status%20Pembelian%3A%20Segera%20%2F%20Survei%0A%0ABoleh%20tolong%20dibantu%20rekomendasi%20unit%20laptop%20yang%20cocok%20ya%20Mas%2FMbak.%20Terima%20kasih!%F0%9F%99%8F%F0%9F%98%8A" class="btn btn-success fw-semibold" target="_blank" rel="noopener noreferrer">
            <i class="bi bi-whatsapp me-2"></i> Konsultasi via WhatsApp
        </a>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Daftar Unit Laptop Tersedia</h4>
    <span class="text-muted small">Total: {{ $products->count() }} unit siap kirim</span>
</div>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
    @forelse($products as $product)
        <div class="col">
            <div class="card h-100 card-laptop shadow-sm border-0">
                <div class="bg-light text-center py-4 rounded-top border-bottom" style="height: 200px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid" style="max-height: 180px; object-fit: contain;" alt="{{ $product->name }}">
                    @else
                        <div class="text-muted">
                            <i class="bi bi-laptop" style="font-size: 4rem;"></i>
                            <div class="small mt-1">Foto unit belum diunggah</div>
                        </div>
                    @endif
                </div>

                <div class="card-body d-flex flex-column">
                    <div class="mb-2">
                        <span class="badge bg-secondary">{{ $product->category->name }}</span>
                        <span class="spec-badge">{{ $product->physical_condition }}</span>
                    </div>

                    <h5 class="card-title fw-bold text-dark mb-1">{{ $product->name }}</h5>
                    <p class="text-primary fw-bold fs-5 mb-3">Rp {{ number_format($product->price, 0, ',', '.') }}</p>

                    <div class="small text-muted mb-3">
                        <div class="mb-1"><i class="bi bi-cpu me-2"></i>{{ $product->processor }}</div>
                        <div class="mb-1"><i class="bi bi-memory me-2"></i>RAM {{ $product->ram_gb }} GB | {{ $product->storage }}</div>
                        <div><i class="bi bi-battery-charging me-2"></i>{{ $product->battery_condition }}</div>
                    </div>

                    <p class="text-muted small flex-grow-1">{{ $product->description ? Str::limit($product->description, 90) : 'Belum ada deskripsi.' }}</p>

                    <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center">
                        <span class="small fw-semibold {{ $product->stock > 0 ? 'text-success' : 'text-danger' }}">
                            <i class="bi {{ $product->stock > 0 ? 'bi-check-circle' : 'bi-x-circle' }}"></i>
                            Stok: {{ $product->stock }} unit
                        </span>

                        @auth
                            @if(Auth::user()->role === 'customer')
                                <form action="{{ route('orders.checkout', $product) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-warning btn-sm fw-semibold text-dark" {{ $product->stock < 1 ? 'disabled' : '' }}>
                                        <i class="bi bi-cart-plus me-1"></i> Beli Sekarang
                                    </button>
                                </form>
                            @else
                                <span class="badge bg-secondary">Mode Admin</span>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-dark btn-sm">Masuk untuk Beli</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info text-center py-4">
                Belum ada unit laptop yang ditambahkan ke katalog.
            </div>
        </div>
    @endforelse
</div>
@endsection
