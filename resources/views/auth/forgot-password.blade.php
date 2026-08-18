<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password | LetterTide</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@500;600;700;800&display=swap" rel="stylesheet">
    @vite('resources/css/pages/forgot-password.css')
</head>
<body>

    @php $requested = session('reset_requested', false); @endphp

    <div class="auth-page auth-page--forgot">

        <a href="{{ route('login') }}" class="auth-page__back">&larr; Kembali ke login</a>

        <div class="auth-card forgot-card">

            <div class="auth-card__brand">
                <span class="auth-card__logo-mark">
                    @include('partials.logo-icon')
                </span>
                <span class="auth-card__brand-name">LetterTide</span>
            </div>

            <div class="forgot-card__state {{ session('reset_requested') ? '' : 'is-active' }}" id="state-contact">
                <svg class="forgot-card__icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 9C9 6.79086 10.7909 5 13 5C15.2091 5 17 6.79086 17 9C17 10.6193 16.0328 12.0129 14.6459 12.6435C13.8112 13.0233 13 13.6716 13 14.5V15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <circle cx="13" cy="19" r="1" fill="currentColor"/>
                    <path d="M4 20L8 16M8 16L4 12M8 16H4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <p class="forgot-card__message">Hubungi Admin untuk reset password</p>

                <form method="POST" action="{{ route('password.request.submit') }}">
                    @csrf
                    <button type="submit" class="btn-contact-admin">Contact Admin</button>
                </form>
            </div>

            <div class="forgot-card__state {{ session('reset_requested') ? 'is-active' : '' }}" id="state-processing">
                <svg class="forgot-card__icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 9C9 6.79086 10.7909 5 13 5C15.2091 5 17 6.79086 17 9C17 10.6193 16.0328 12.0129 14.6459 12.6435C13.8112 13.0233 13 13.6716 13 14.5V15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <circle cx="13" cy="19" r="1" fill="currentColor"/>
                    <path d="M4 20L8 16M8 16L4 12M8 16H4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <p class="forgot-card__message">Admin memproses reset password</p>
                <p class="forgot-card__eta">1x24 Jam</p>
            </div>

        </div>
    </div>

</body>
</html>
