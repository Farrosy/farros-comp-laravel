@extends('layouts.app', ['title' => 'Panduan Kondisi Unit & Garansi - Farros Comp'])

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="bg-dark text-white p-4 p-md-5 rounded-4 shadow-sm mb-4">
            <span class="badge bg-warning text-dark fw-bold mb-2">Standar Kualitas Farros Comp</span>
            <h2 class="fw-bold">Panduan Grading Kondisi Unit & SOP Garansi</h2>
            <p class="text-light opacity-75 mb-0">Setiap laptop bekas yang masuk telah melewati 21 titik uji kelayakan (Quality Control) sebelum dipajang di etalase toko.</p>
        </div>

        <h4 class="fw-bold mb-3"><i class="bi bi-patch-check-fill text-warning me-2"></i>Standar Fisik Unit</h4>
        <div class="row g-3 mb-5">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-3">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-success me-2 px-3 py-2">Grade A+ (97% - 99%)</span>
                    </div>
                    <h6 class="fw-bold text-dark">Kondisi Istimewa / Like New</h6>
                    <p class="small text-muted mb-0">Hampir tidak ada bekas pemakaian. Bodi mulus bebas dent/penyok, cat utuh, layar jernih tanpa cacat (no white spot / dead pixel), keyboard doff tidak mengkilap.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-3">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-primary me-2 px-3 py-2">Grade A (92% - 96%)</span>
                    </div>
                    <h6 class="fw-bold text-dark">Mulus Pemakaian Kantor Wajar</h6>
                    <p class="small text-muted mb-0">Terdapat baret rambut (<em>micro scratches</em>) halus khas pemakaian tas laptop, tanpa retak, port USB/HDMI berfungsi penuh, engsel kokoh 100%.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-3">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-secondary me-2 px-3 py-2">Grade B (85% - 90%)</span>
                    </div>
                    <h6 class="fw-bold text-dark">Fungsional Prima, Kosmetik Standar</h6>
                    <p class="small text-muted mb-0">Terdapat lecet sudut atau rubber palmrest aus wajar, namun seluruh hardware utama (motherboard, prosesor, SSD, kipas) diuji stres 100% lulus benchmark.</p>
                </div>
            </div>
        </div>

        <h4 class="fw-bold mb-3"><i class="bi bi-battery-charging text-warning me-2"></i>Standar Baterai & Uji Perangkat</h4>
        <div class="card border-0 shadow-sm p-4 mb-5">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="d-flex">
                        <i class="bi bi-check2-circle text-success fs-3 me-3"></i>
                        <div>
                            <h6 class="fw-bold mb-1">Battery Health Minimal 80%</h6>
                            <p class="small text-muted mb-0">Farros Comp tidak menjual unit dengan baterai kembung (<em>bloated</em>) atau drop di bawah 75%. Ketahanan nyata berkisar antara 3 hingga 6 jam tergantung seri laptop.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex">
                        <i class="bi bi-check2-circle text-success fs-3 me-3"></i>
                        <div>
                            <h6 class="fw-bold mb-1">Thermal Paste Baru & Bebas Debu</h6>
                            <p class="small text-muted mb-0">Sebelum dikirim, heatsink dibersihkan dan thermal paste diganti dengan Arctic MX-4 / sekelasnya agar suhu adem saat compile koding atau rendering.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h4 class="fw-bold mb-3"><i class="bi bi-shield-check text-warning me-2"></i>Ketentuan Garansi Farros Comp</h4>
        <div class="card border-0 shadow-sm p-4">
            <ul class="mb-0 text-muted small ps-3">
                <li class="mb-2"><strong>Garansi Tukar Unit:</strong> Berlaku 7 hari sejak barang diterima jika ditemukan kerusakan fungsional bawaan (<em>dead on arrival</em>).</li>
                <li class="mb-2"><strong>Garansi Sparepart & Servis:</strong> Berlaku 30 hari meliputi motherboard, RAM, storage SSD, dan layar LCD.</li>
                <li class="mb-2"><strong>Syarat Klaim:</strong> Segel toko utuh, bukan akibat kelalaian pembeli (jatuh, terkena cairan, atau korsleting listrik rumah).</li>
                <li><strong>Bonus Pembelian:</strong> Setiap unit menyertakan charger adaptor original / OEM grade A, kabel power, serta bonus tas laptop slempang.</li>
            </ul>
        </div>
    </div>
</div>
@endsection
