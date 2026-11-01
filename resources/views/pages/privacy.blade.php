@extends('layouts.app', ['title' => 'Pemberitahuan Privasi - Farros Comp'])

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <article class="card shadow-sm border-0">
            <div class="card-body p-4 p-md-5">
                <h1 class="h3 fw-bold mb-2">Pemberitahuan Privasi</h1>
                <p class="text-muted">Informasi tentang data yang diminta saat Anda membuat akun dan melakukan pemesanan.</p>

                <h2 class="h5 fw-semibold mt-4">Data yang kami gunakan</h2>
                <p>Data nama, email, nomor WhatsApp/HP, alamat pengiriman, dan informasi pesanan digunakan untuk mengelola akun, menghubungi Anda terkait pesanan, serta menyiapkan pengiriman.</p>

                <h2 class="h5 fw-semibold mt-4">Pembayaran</h2>
                <p>Saat checkout, informasi transaksi dan data pelanggan yang diperlukan dikirim ke Midtrans untuk memproses pembayaran. Pemrosesan pembayaran juga mengikuti kebijakan privasi Midtrans.</p>

                <h2 class="h5 fw-semibold mt-4">Keamanan akun</h2>
                <p>Kata sandi disimpan dalam bentuk hash dan tidak ditampilkan sebagai teks biasa. Email dan nomor WhatsApp/HP dapat digunakan untuk menghubungi Anda terkait akun atau pesanan.</p>

                <h2 class="h5 fw-semibold mt-4">Permintaan dan bantuan</h2>
                <p>Untuk meminta bantuan terkait data akun, memperbarui data, atau mengajukan penghapusan akun, hubungi tim Farros Comp melalui <a href="https://wa.me/6285691050420" target="_blank" rel="noopener">WhatsApp</a>. Kami akan memeriksa permintaan dan kewajiban transaksi yang masih berjalan.</p>

                <p class="small text-muted mt-4 mb-0">Dengan mendaftar, Anda menyatakan telah membaca pemberitahuan ini. Pemberitahuan ini dapat diperbarui jika cara penggunaan data berubah.</p>
            </div>
        </article>
    </div>
</div>
@endsection
