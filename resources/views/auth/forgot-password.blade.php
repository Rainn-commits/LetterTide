<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password | LetterTide</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@500;600;700;800&display=swap" rel="stylesheet">
    @vite('resources/css/forgot-password.css')
</head>
<body>

    @php $requested = session('reset_requested', false); @endphp

    <div class="auth-page auth-page--forgot">

        <a href="{{ route('login') }}" class="auth-page__back" aria-label="Kembali ke login">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5"/>
                <path d="M12 19L5 12L12 5"/>
            </svg>
        </a>

        <div class="auth-card forgot-card">

            <div class="auth-card__brand">
                <span class="auth-card__logo-mark">
                    <img src="{{ asset('images/logo.png') }}" alt="LetterTide">
                </span>
                <span class="auth-card__brand-name">LetterTide</span>
            </div>

            <div class="forgot-card__state {{ session('reset_requested') ? '' : 'is-active' }}" id="state-contact">
            <img src="{{ asset('images/download.svg') }}" alt="Lupa password" class="forgot-card__icon">
                <p class="forgot-card__message">Hubungi Admin untuk reset password</p>

                <form method="POST" action="{{ route('password.request.submit') }}">
                    @csrf

                    <input
                        type="email"
                        name="email"
                        class="forgot-card__input"
                        placeholder="Masukkan email Anda"
                        value="{{ old('email') }}"
                        required
                    >

                    @error('email')
                        <small class="forgot-card__error">{{ $message }}</small>
                    @enderror

                    <button type="submit" class="btn-contact-admin">Contact Admin</button>
                </form>
            </div>

            <div class="forgot-card__state {{ session('reset_requested') ? 'is-active' : '' }}" id="state-processing">
<img src="{{ asset('images/download.svg') }}" alt="Lupa password" class="forgot-card__icon">
                <p class="forgot-card__message">Admin memproses reset password</p>
                <p class="forgot-card__eta">1x24 Jam</p>
            </div>

        </div>
    </div>

</body>
</html>
