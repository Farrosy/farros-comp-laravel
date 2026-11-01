@extends('layouts.app', ['title' => 'Masuk - Farros Comp'])

@section('content')
<div class="auth-page auth-login">
    <div class="auth-card">
        <aside class="auth-aside">
            <a class="auth-back-link" href="{{ route('home') }}"><i class="bi bi-arrow-left me-2" aria-hidden="true"></i>Kembali ke katalog</a>
            <div class="auth-aside-content">
                <span class="auth-kicker"><i class="bi bi-laptop me-2" aria-hidden="true"></i>Farros Comp</span>
                <h1 class="auth-hero-title">Senang melihatmu kembali.</h1>
                <p class="auth-hero-copy">Masuk untuk melihat pesanan dan melanjutkan belanja laptop pilihanmu.</p>
                <ul class="auth-benefits">
                    <li><i class="bi bi-bag-check" aria-hidden="true"></i><span>Lihat riwayat dan status pesanan.</span></li>
                    <li><i class="bi bi-geo-alt" aria-hidden="true"></i><span>Gunakan data pengiriman akunmu.</span></li>
                    <li><i class="bi bi-chat-dots" aria-hidden="true"></i><span>Tim kami siap membantu melalui WhatsApp.</span></li>
                </ul>
            </div>
            <div class="auth-aside-foot">Farros Comp · Spesialis laptop second</div>
        </aside>

        <section class="auth-form-panel auth-login-panel" aria-labelledby="login-title">
            <div class="auth-form-heading">
                <span class="auth-step-label">AKUN FARROS COMP</span>
                <h2 id="login-title">Masuk ke akun</h2>
                <p>Masukkan email dan kata sandi yang terdaftar.</p>
            </div>

            @if ($errors->has('login'))
                <div class="alert alert-danger auth-error-summary" role="alert" tabindex="-1" id="login-error-summary">
                    <i class="bi bi-exclamation-circle-fill me-2" aria-hidden="true"></i>{{ $errors->first('login') }}
                </div>
            @elseif ($errors->any())
                <div class="alert alert-danger auth-error-summary" role="alert" tabindex="-1" id="login-error-summary">
                    <div class="fw-semibold mb-1">Periksa kembali data berikut:</div>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="login-form" action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Alamat email</label>
                    <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror @error('login') is-invalid @enderror" value="{{ old('email') }}" autocomplete="email" inputmode="email" maxlength="255" placeholder="nama@email.com" required aria-invalid="{{ $errors->has('email') || $errors->has('login') ? 'true' : 'false' }}" @if($errors->has('email')) aria-describedby="email-error" @elseif($errors->has('login')) aria-describedby="login-error-summary" @endif>
                    @error('email')<div id="email-error" class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Kata sandi</label>
                    <div class="input-group">
                        <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror @error('login') is-invalid @enderror" autocomplete="current-password" maxlength="72" required aria-invalid="{{ $errors->has('password') || $errors->has('login') ? 'true' : 'false' }}" @if($errors->has('password')) aria-describedby="password-error" @elseif($errors->has('login')) aria-describedby="login-error-summary" @endif>
                        <button class="btn btn-outline-secondary password-toggle" type="button" data-toggle-password="password" aria-label="Tampilkan kata sandi" aria-pressed="false"><i class="bi bi-eye" aria-hidden="true"></i></button>
                        @error('password')<div id="password-error" class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-4 form-check">
                    <input type="checkbox" name="remember" class="form-check-input" id="remember" value="1" @checked(old('remember'))>
                    <label class="form-check-label" for="remember">Ingat saya di perangkat ini</label>
                </div>

                <button id="login-submit" type="submit" class="btn btn-warning auth-submit w-100 fw-bold py-3">
                    <span class="submit-label">Masuk</span>
                    <span class="spinner-border spinner-border-sm ms-2 d-none" aria-hidden="true"></span>
                </button>
                <div id="login-submit-status" class="visually-hidden" role="status" aria-live="polite"></div>
            </form>

            <p class="auth-switch-link">Belum punya akun? <a href="{{ route('register') }}">Buat akun</a></p>
            <a class="auth-help-link" href="https://wa.me/6285691050420" target="_blank" rel="noopener">Perlu bantuan masuk? Hubungi CS <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
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

    const loginForm = document.getElementById('login-form');
    loginForm.addEventListener('invalid', function (event) {
        event.target.classList.add('is-invalid');
        event.target.setAttribute('aria-invalid', 'true');
    }, true);
    loginForm.querySelectorAll('input').forEach(function (field) {
        field.addEventListener('input', function () {
            if (field.checkValidity()) {
                field.classList.remove('is-invalid');
                field.setAttribute('aria-invalid', 'false');
            }

            @if($errors->has('login'))
                const loginErrorSummary = document.getElementById('login-error-summary');
                if (loginErrorSummary) {
                    loginErrorSummary.remove();
                    loginForm.querySelectorAll('.is-invalid').forEach(function (invalidField) {
                        invalidField.classList.remove('is-invalid');
                        invalidField.setAttribute('aria-invalid', 'false');
                        invalidField.removeAttribute('aria-describedby');
                    });
                }
            @endif
        });
    });

    const firstInvalidField = document.querySelector('#login-form .is-invalid');
    if (firstInvalidField) {
        firstInvalidField.focus({ preventScroll: true });
        firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
    } else if (document.getElementById('login-error-summary')) {
        document.getElementById('login-error-summary').focus();
    }

    loginForm.addEventListener('submit', function () {
        const button = document.getElementById('login-submit');
        button.disabled = true;
        button.setAttribute('aria-busy', 'true');
        button.querySelector('.submit-label').textContent = 'Memeriksa akun…';
        button.querySelector('.spinner-border').classList.remove('d-none');
        document.getElementById('login-submit-status').textContent = 'Sedang masuk. Mohon tunggu.';
    });
</script>
@endsection
