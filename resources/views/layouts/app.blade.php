<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Farros Comp - E-Commerce Laptop Second' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>
<body class="bg-light">
    <header class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
        <div class="container-fluid px-lg-4">
            <button class="btn btn-outline-secondary d-md-none me-2 text-white border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-label="Buka menu navigasi">
                <i class="bi bi-list fs-3"></i>
            </button>

            <a class="navbar-brand d-flex align-items-center text-decoration-none me-4" href="{{ route('home') }}">
                <div class="brand-icon-box me-2">
                    <i class="bi bi-laptop"></i>
                </div>
                <div>
                    <div class="brand-title">Farros Comp</div>
                    <div class="brand-subtitle">Spesialis Laptop Second</div>
                </div>
            </a>

            <div class="d-none d-md-block flex-grow-1 mx-lg-5" style="max-width: 450px;">
                <form action="{{ route('home') }}" method="GET" class="position-relative">
                    <i class="bi bi-search nav-search-icon"></i>
                    <input type="text" name="search" class="form-control nav-search-input py-2" placeholder="Cari ThinkPad, MacBook, Core i7, RAM 16GB..." value="{{ request('search') }}">
                </form>
            </div>

            <ul class="navbar-nav ms-auto align-items-center flex-row gap-2">
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white d-flex align-items-center bg-dark bg-opacity-50 px-3 py-2 rounded-pill border border-secondary border-opacity-25" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="rounded-circle bg-warning text-dark fw-bold d-flex align-items-center justify-content-center me-2" style="width: 28px; height: 28px; font-size: 0.8rem;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span class="fw-semibold me-1">{{ Auth::user()->name }}</span>
                            <span class="badge bg-secondary ms-1" style="font-size: 0.7rem;">{{ ucfirst(Auth::user()->role) }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <li class="px-3 py-2 text-muted small border-bottom">
                                Login sebagai:<br>
                                <strong class="text-dark">{{ Auth::user()->email }}</strong>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('orders.index') }}">
                                    <i class="bi bi-receipt me-2"></i> Pesanan Saya
                                </a>
                            </li>
                            @if(Auth::user()->role === 'admin')
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('admin.products.index') }}">
                                        <i class="bi bi-shield-lock me-2 text-warning"></i> Dashboard Admin
                                    </a>
                                </li>
                            @endif
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger py-2">
                                        <i class="bi bi-box-arrow-right me-2"></i> Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link text-white fw-semibold px-3 py-2" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-warning btn-sm px-3 py-2 fw-bold text-dark rounded-pill shadow-sm" href="{{ route('register') }}">
                            <i class="bi bi-person-plus me-1"></i> Daftar Akun
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </header>

    <div class="container-fluid">
        <div class="row">
            <div class="sidebar border-end col-md-3 col-lg-2 p-0 bg-white offcanvas-md offcanvas-start" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
                <div class="offcanvas-header border-bottom d-md-none p-3">
                    <h5 class="offcanvas-title fw-bold" id="sidebarMenuLabel">
                        <i class="bi bi-laptop text-warning me-1"></i> Farros Comp
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu" aria-label="Tutup"></button>
                </div>

                <div class="offcanvas-body d-md-flex flex-column p-3 pt-md-4 overflow-y-auto">
                    <div class="sidebar-heading">Menu Utama</div>
                    <nav class="nav flex-column gap-1 mb-3">
                        <a href="{{ route('home') }}" class="sidebar-link {{ request()->routeIs('home') || request()->is('/') ? 'active' : '' }}">
                            <i class="bi bi-grid-fill me-2"></i>
                            <span>Katalog Laptop</span>
                        </a>
                        @auth
                            <a href="{{ route('orders.index') }}" class="sidebar-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                                <i class="bi bi-bag-check-fill me-2"></i>
                                <span>Pesanan Saya</span>
                            </a>
                        @endauth
                        <a href="{{ route('condition.guide') }}" class="sidebar-link {{ request()->routeIs('condition.guide') ? 'active' : '' }}">
                            <i class="bi bi-patch-question-fill me-2"></i>
                            <span>Panduan & Garansi</span>
                        </a>
                    </nav>

                    <div class="sidebar-heading">Layanan & Konsultasi</div>
                    <nav class="nav flex-column gap-1 mb-3">
                        <a href="https://wa.me/6285691050420?text=Halo%20Tim%20Farros%20Comp%F0%9F%91%8B%E2%98%BA%EF%B8%8F%2C%20salam%20kenal.%0A%0ASaya%20baru%20lihat%20website%20Farros%20Comp%20%F0%9F%92%BB%20dan%20mau%20konsultasi%20santai%20untuk%20cari%20laptop%20second%20yang%20paling%20pas%20dengan%20kebutuhan%20dan%20budget%20saya.%0A%0A%F0%9F%93%84%20Berikut%20Formulir%20Kebutuhan%20Saya%3A%0A%E2%80%A2%20Nama%3A%20%0A%E2%80%A2%20Domisili%3A%20%0A%E2%80%A2%20Kebutuhan%20Utama%3A%20(Coding%20%2F%20Desain%20%2F%20Office%20Harian)%0A%E2%80%A2%20Software%20yang%20dipakai%3A%20%0A%E2%80%A2%20Preferensi%20Brand%3A%20(ThinkPad%20%2F%20Dell%20%2F%20MacBook)%0A%E2%80%A2%20Budget%20Maksimal%3A%20Rp%20%0A%E2%80%A2%20Status%20Pembelian%3A%20Segera%20%2F%20Survei%0A%0ABoleh%20tolong%20dibantu%20rekomendasi%20unit%20laptop%20yang%20cocok%20ya%20Mas%2FMbak.%20Terima%20kasih!%F0%9F%99%8F%F0%9F%98%8A" target="_blank" rel="noopener noreferrer" class="sidebar-link text-success">
                            <i class="bi bi-whatsapp text-success me-2"></i>
                            <span>Konsultasi WA CS</span>
                        </a>
                    </nav>

                    @if(Auth::check() && Auth::user()->role === 'admin')
                        <div class="sidebar-heading">Administrasi</div>
                        <nav class="nav flex-column gap-1">
                            <a href="{{ route('admin.products.index') }}" class="sidebar-link {{ request()->is('admin*') ? 'active' : '' }}">
                                <i class="bi bi-shield-lock-fill me-2"></i>
                                <span>Panel Admin</span>
                            </a>
                        </nav>
                    @endif
                </div>
            </div>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4 min-vh-100">
                @yield('content')
            </main>
        </div>
    </div>

    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1090;">
        @if(session('success'))
            <div class="toast align-items-center text-bg-success border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3500">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center">
                        <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="toast align-items-center text-bg-danger border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4000">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
                </div>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.toast').forEach(function (toastElement) {
                bootstrap.Toast.getOrCreateInstance(toastElement).show();
            });
        });
    </script>
</body>
</html>
