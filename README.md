<p align="center">
  <a href="https://laravel.com" target="_blank" rel="noopener">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel">
  </a>
</p>

<h1 align="center">Farros Comp</h1>

<p align="center">E-commerce laptop second berkualitas dengan katalog produk, pengelolaan pesanan, dan pembayaran online.</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11-FF2D20?logo=laravel&logoColor=white" alt="Laravel 11">
  <img src="https://img.shields.io/badge/PHP-8.3%2B-777BB4?logo=php&logoColor=white" alt="PHP 8.3 atau lebih baru">
  <img src="https://img.shields.io/badge/Bootstrap-5-7952B3?logo=bootstrap&logoColor=white" alt="Bootstrap 5">
  <img src="https://img.shields.io/badge/Database-TiDB%20Cloud-4479A1" alt="TiDB Cloud">
  <img src="https://img.shields.io/badge/Payments-Midtrans%20Sandbox-00A5CF" alt="Midtrans Sandbox">
  <img src="https://img.shields.io/badge/Hosting-Wasmer%20Edge-654FF0" alt="Wasmer Edge">
</p>

## Tentang Proyek

Farros Comp adalah aplikasi web untuk menampilkan katalog laptop second dan mengelola proses pembelian. Pelanggan dapat membuat akun, melakukan checkout, membayar melalui Midtrans, serta melihat status pesanan. Admin dapat mengelola produk dan memproses pesanan.

## Demo Online

- **Aplikasi:** [https://farros-comp.wasmer.app/](https://farros-comp.wasmer.app/)
- **Database:** TiDB Cloud Serverless, kompatibel dengan MySQL dan menggunakan koneksi SSL.

## Akun Demo

| Peran | Email | Kata sandi |
|---|---|---|
| Admin | `admin@farroscomp.com` | `password123` |
| Pelanggan | `customer@farroscomp.com` | `password123` |

> Akun ini hanya untuk demonstrasi. Kredensial admin tercantum secara publik; jangan gunakan untuk operasional atau data nyata. Ganti atau nonaktifkan akun demo sebelum penggunaan produksi.

## Fitur

- Autentikasi pelanggan dan admin dengan pembatasan akses berdasarkan peran.
- Katalog laptop dengan detail spesifikasi, kondisi unit, harga, dan stok.
- CRUD produk admin, tambah kategori cepat, dan input harga dengan format Rupiah.
- Checkout Midtrans Snap Sandbox dan endpoint notifikasi pembayaran di `/midtrans/callback`.
- Pengelolaan status pesanan: menunggu pembayaran, perlu diproses, dikirim, selesai, dan dibatalkan.
- Informasi nomor resi dan alamat pengiriman untuk pelanggan.
- Panduan kondisi unit dan garansi toko.
- Tautan konsultasi Customer Service melalui WhatsApp.

## Teknologi

- **Backend:** Laravel 11, PHP.
- **Frontend:** Laravel Blade, Bootstrap 5, Bootstrap Icons.
- **Database:** TiDB Cloud Serverless (MySQL compatible).
- **Pembayaran:** Midtrans Snap Sandbox.
- **Hosting:** Wasmer Edge.

Dockerfile proyek menggunakan FrankenPHP dengan PHP 8.3. Manifest Wasmer juga mendefinisikan runtime PHP untuk deployment Edge.

## Menjalankan Secara Lokal

Persyaratan: PHP 8.3+, Composer, dan database MySQL/TiDB atau SQLite.

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Atur koneksi database di `.env`, lalu jalankan migrasi dan data demo:

```bash
php artisan migrate --seed
php artisan serve
```

Aplikasi tersedia di `http://127.0.0.1:8000`.

## Konfigurasi Integrasi

Isi kredensial melalui environment deployment, jangan simpan secret di source code:

- `MIDTRANS_SERVER_KEY`
- `MIDTRANS_CLIENT_KEY`
- `MIDTRANS_IS_PRODUCTION=false` untuk Sandbox
- Variabel `DB_*` untuk koneksi TiDB Cloud

Atur **Payment Notification URL** di dashboard Midtrans ke:

```text
https://<domain-aplikasi>/midtrans/callback
```

Ganti `<domain-aplikasi>` dengan domain Wasmer yang aktif. Untuk uji pembayaran, gunakan key Sandbox dan transaksi yang dibuat dari aplikasi.
