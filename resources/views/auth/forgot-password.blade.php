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

        <a href="{{ route('login') }}" class="auth-page__back">&larr; Kembali ke login</a>

        <div class="auth-card forgot-card">

            <div class="auth-card__brand">
                <span class="auth-card__logo-mark">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 8L10.8906 13.2604C11.5624 13.7083 12.4376 13.7083 13.1094 13.2604L21 8M5 19H19C20.1046 19 21 18.1046 21 17V7C21 5.89543 20.1046 5 19 5H5C3.89543 5 3 5.89543 3 7V17C3 18.1046 3.89543 19 5 19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="auth-card__brand-name">LetterTide</span>
            </div>

            <div class="forgot-card__state {{ session('reset_requested') ? '' : 'is-active' }}" id="state-contact">
<img src="{{ asset('images/download.svg') }}" alt="Lupa password" class="forgot-card__icon">
                <p class="forgot-card__message">Hubungi Admin untuk reset password</p>

                <form method="POST" action="{{ route('password.request.submit') }}">
                    @csrf
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
