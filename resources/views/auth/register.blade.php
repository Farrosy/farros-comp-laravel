@extends('layouts.app', ['title' => 'Buat Akun - Farros Comp'])

@section('content')
@php
    $fieldLabels = [
        'name' => 'Nama lengkap',
        'email' => 'Alamat email',
        'phone' => 'Nomor WhatsApp / HP',
        'address' => 'Alamat pengiriman',
        'password' => 'Kata sandi',
        'password_confirmation' => 'Konfirmasi kata sandi',
        'privacy_notice' => 'Pemberitahuan privasi',
    ];
@endphp

<div class="auth-page auth-register">
    <div class="auth-card">
        <aside class="auth-aside">
            <a class="auth-back-link" href="{{ route('home') }}"><i class="bi bi-arrow-left me-2" aria-hidden="true"></i>Kembali ke katalog</a>
            <div class="auth-aside-content">
                <span class="auth-kicker"><i class="bi bi-laptop me-2" aria-hidden="true"></i>Farros Comp</span>
                <h1 class="auth-hero-title">Temukan laptop yang siap menemani aktivitasmu.</h1>
                <p class="auth-hero-copy">Buat akun untuk menyimpan alamat pengiriman dan melihat perkembangan pesanan di satu tempat.</p>
                <ul class="auth-benefits">
                    <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>Checkout lebih praktis dengan data akunmu.</span></li>
                    <li><i class="bi bi-box-seam" aria-hidden="true"></i><span>Pantau riwayat dan status pesanan.</span></li>
                    <li><i class="bi bi-chat-dots" aria-hidden="true"></i><span>Butuh bantuan? Tim kami siap di WhatsApp.</span></li>
                </ul>
            </div>
            <div class="auth-aside-foot">Laptop second pilihan, siap untuk langkah berikutnya.</div>
        </aside>

        <section class="auth-form-panel" aria-labelledby="register-title">
            <div class="auth-form-heading">
                <span class="auth-step-label">MULAI DALAM BEBERAPA LANGKAH</span>
                <h2 id="register-title">Buat akun baru</h2>
                <p>Lengkapi data agar kami bisa membantu proses pesanan dan pengiriman. Semua kolom wajib diisi.</p>
            </div>

            @if ($errors->any())
                <div id="register-error-summary" class="alert alert-danger auth-error-summary" role="alert" tabindex="-1" aria-labelledby="register-error-title">
                    <div id="register-error-title" class="fw-semibold mb-1"><i class="bi bi-exclamation-circle-fill me-2" aria-hidden="true"></i>Periksa isian berikut sebelum melanjutkan</div>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->getMessages() as $field => $messages)
                            <li><a href="#{{ $field }}">{{ $fieldLabels[$field] ?? 'Formulir' }}: {{ $messages[0] }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="register-form" action="{{ route('register') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Nama lengkap</label>
                    <input id="name" type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" autocomplete="name" maxlength="255" required aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}" @if($errors->has('name')) aria-describedby="name-error" @endif>
                    @error('name')<div id="name-error" class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Alamat email</label>
                    <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" autocomplete="email" inputmode="email" maxlength="255" placeholder="nama@email.com" required aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}" aria-describedby="email-help @if($errors->has('email')) email-error @endif">
                    <div id="email-help" class="form-text">Gunakan email aktif agar kami dapat menghubungi Anda terkait pesanan.</div>
                    @error('email')<div id="email-error" class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Nomor WhatsApp / HP</label>
                    <input id="phone" type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" autocomplete="tel" inputmode="tel" maxlength="20" pattern="[0-9+(). -]{1,20}" placeholder="0812 3456 7890" required aria-invalid="{{ $errors->has('phone') ? 'true' : 'false' }}" aria-describedby="phone-help @if($errors->has('phone')) phone-error @endif">
                    <div id="phone-help" class="form-text">Gunakan 10–13 angka dengan awalan 08 atau +62. Huruf akan diabaikan; spasi dan tanda hubung dirapikan otomatis.</div>
                    @error('phone')<div id="phone-error" class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Alamat pengiriman</label>
                    <textarea id="address" name="address" class="form-control @error('address') is-invalid @enderror" rows="3" autocomplete="street-address" maxlength="2000" placeholder="Contoh: Jl. Melati No. 10, RT 02/RW 04, Kel. Sukamaju, Kec. Cibinong, Bogor, Jawa Barat 16911" required aria-invalid="{{ $errors->has('address') ? 'true' : 'false' }}" aria-describedby="address-help @if($errors->has('address')) address-error @endif">{{ old('address') }}</textarea>
                    <div id="address-help" class="form-text">Cantumkan jalan, nomor rumah, RT/RW, kelurahan, kecamatan, kota, provinsi, dan kode pos agar kurir mudah menemukan alamat.</div>
                    @error('address')<div id="address-error" class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Kata sandi</label>
                    <div class="input-group">
                        <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password" minlength="15" maxlength="72" required aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}" aria-describedby="password-help @if($errors->has('password')) password-error @endif">
                        <button class="btn btn-outline-secondary password-toggle" type="button" data-toggle-password="password" aria-label="Tampilkan kata sandi" aria-pressed="false"><i class="bi bi-eye" aria-hidden="true"></i></button>
                        @error('password')<div id="password-error" class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div id="password-help" class="form-text">Gunakan frasa sandi yang mudah diingat: minimal 15 dan maksimal 72 karakter.</div>
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Konfirmasi kata sandi</label>
                    <div class="input-group">
                        <input id="password_confirmation" type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" autocomplete="new-password" minlength="15" maxlength="72" required aria-invalid="{{ $errors->has('password_confirmation') ? 'true' : 'false' }}" aria-describedby="confirmation-help @if($errors->has('password_confirmation')) confirmation-error @endif">
                        <button class="btn btn-outline-secondary password-toggle" type="button" data-toggle-password="password_confirmation" aria-label="Tampilkan konfirmasi kata sandi" aria-pressed="false"><i class="bi bi-eye" aria-hidden="true"></i></button>
                        @error('password_confirmation')<div id="confirmation-error" class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div id="confirmation-help" class="form-text">Ketik ulang kata sandi yang sama.</div>
                </div>

                <div class="mb-4">
                    <div class="form-check">
                        <input id="privacy_notice" type="checkbox" name="privacy_notice" value="1" class="form-check-input @error('privacy_notice') is-invalid @enderror" @checked(old('privacy_notice')) required aria-invalid="{{ $errors->has('privacy_notice') ? 'true' : 'false' }}" @if($errors->has('privacy_notice')) aria-describedby="privacy-error" @endif>
                        <label for="privacy_notice" class="form-check-label">Saya telah membaca <a href="{{ route('privacy.notice') }}" target="_blank" rel="noopener">Pemberitahuan Privasi</a>.</label>
                        @error('privacy_notice')<div id="privacy-error" class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <button id="register-submit" type="submit" class="btn btn-warning auth-submit w-100 fw-bold py-3">
                    <span class="submit-label">Buat akun</span>
                    <span class="spinner-border spinner-border-sm ms-2 d-none" aria-hidden="true"></span>
                </button>
                <div id="register-submit-status" class="visually-hidden" role="status" aria-live="polite"></div>
            </form>

            <p class="auth-switch-link">Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
            <a class="auth-help-link" href="https://wa.me/6285691050420" target="_blank" rel="noopener">Butuh bantuan mengisi form? Hubungi CS <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
        </section>
    </div>
</div>

<script>
    document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
        button.addEventListener('click', function () {
            const input = document.getElementById(button.dataset.togglePassword);
            const showing = input.type === 'password';
            input.type = showing ? 'text' : 'password';
            button.setAttribute('aria-pressed', String(showing));
            button.setAttribute('aria-label', showing ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
            button.querySelector('i').className = showing ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    });

    const phoneInput = document.getElementById('phone');
    phoneInput.addEventListener('input', function () {
        const value = phoneInput.value;
        const cursor = phoneInput.selectionStart;
        const clean = value.replace(/[^0-9+().\s-]/g, '').replace(/(?!^)\+/g, '');
        const cleanBeforeCursor = value.slice(0, cursor).replace(/[^0-9+().\s-]/g, '').replace(/(?!^)\+/g, '');

        if (clean !== value) {
            phoneInput.value = clean;
            phoneInput.setSelectionRange(cleanBeforeCursor.length, cleanBeforeCursor.length);
        }
    });

    const firstInvalidField = document.querySelector('#register-form .is-invalid');
    if (firstInvalidField) {
        firstInvalidField.focus({ preventScroll: true });
        firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    const registerForm = document.getElementById('register-form');
    registerForm.addEventListener('invalid', function (event) {
        event.target.classList.add('is-invalid');
        event.target.setAttribute('aria-invalid', 'true');
    }, true);
    registerForm.querySelectorAll('input, textarea').forEach(function (field) {
        const clearInvalidState = function () {
            if (field.checkValidity()) {
                field.classList.remove('is-invalid');
                field.setAttribute('aria-invalid', 'false');
            }
        };
        field.addEventListener('input', clearInvalidState);
        field.addEventListener('change', clearInvalidState);
    });

    registerForm.addEventListener('submit', function () {
        const button = document.getElementById('register-submit');
        button.disabled = true;
        button.setAttribute('aria-busy', 'true');
        button.querySelector('.submit-label').textContent = 'Membuat akun…';
        button.querySelector('.spinner-border').classList.remove('d-none');
        document.getElementById('register-submit-status').textContent = 'Form sedang dikirim. Mohon tunggu.';
    });
</script>
@endsection
