<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In - LetterTide</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@500;600;700;800&display=swap" rel="stylesheet">
    @vite([
        'resources/css/variables.css',
        'resources/css/auth-base.css',
        'resources/css/login.css',
        'resources/js/password-toggle.js'
    ])
</head>
<body>

    <div class="auth-page auth-page--login">

        <a href="{{ url('/') }}" class="auth-page__back" aria-label="Kembali ke beranda">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19 12H5M5 12L12 19M5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>

        <div class="auth-split">

            <div class="auth-split__panel">
                <div class="auth-split__scene" aria-hidden="true">
                    <div class="auth-split__cloud auth-split__cloud--1"></div>
                    <div class="auth-split__cloud auth-split__cloud--2"></div>
                    <div class="auth-split__cloud auth-split__cloud--3"></div>

                    <div class="auth-split__mote" style="left:20%; top:70%; animation-duration:6s; animation-delay:0s;"></div>
                    <div class="auth-split__mote" style="left:65%; top:75%; animation-duration:7.5s; animation-delay:1.2s;"></div>
                    <div class="auth-split__mote" style="left:40%; top:80%; animation-duration:6.8s; animation-delay:2.4s;"></div>
                    <div class="auth-split__mote" style="left:80%; top:65%; animation-duration:8s; animation-delay:0.6s;"></div>
                    <div class="auth-split__mote" style="left:15%; top:55%; animation-duration:7s; animation-delay:3s;"></div>
                    <div class="auth-split__mote" style="left:55%; top:60%; animation-duration:6.4s; animation-delay:1.8s;"></div>
                </div>

                <div>
                    <svg class="auth-split__panel-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 6.5C3 5.67157 3.67157 5 4.5 5H19.5C20.3284 5 21 5.67157 21 6.5V17.5C21 18.3284 20.3284 19 19.5 19H4.5C3.67157 19 3 18.3284 3 17.5V6.5Z" stroke="white" stroke-width="1.3" stroke-linejoin="round"/>
                        <path d="M4 6.5L12 13L20 6.5" stroke="white" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div>
                    <h2 class="auth-split__panel-title">Satu pintu masuk untuk semua peran</h2>
                    <p class="auth-split__panel-copy">
                        Super Admin, Admin dan User masuk lewat form yang sama |
                        LetterTide akan mengarahkan Anda ke dashboard sesuai hak akses setelah login.
                    </p>
                </div>
            </div>

            <div class="auth-split__form">
                <div class="auth-card__brand">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo LetterTide" class="auth-card__logo-img">
                    <span class="auth-card__brand-name">LetterTide</span>
                </div>

                <form method="POST" action="{{ route('login') }}" novalidate>
                    @csrf

                    <div class="auth-field @error('email') has-error @enderror">
                        <label for="email">Email:</label>
                        <div class="auth-field__control">
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
                        </div>
                        @error('email')
                            <span class="auth-field__error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="auth-field @error('password') has-error @enderror">
                        <label for="password">Password:</label>
                        <div class="auth-field__control">
                            <input type="password" id="password" name="password" required>
                            <button type="button" class="auth-field__toggle" data-toggle-password="password" aria-label="Tampilkan password">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2 12C2 12 5.5 5 12 5C18.5 5 22 12 22 12C22 12 18.5 19 12 19C5.5 19 2 12 2 12Z" stroke="currentColor" stroke-width="1.6"/>
                                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.6"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <span class="auth-field__error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="auth-remember">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Ingat saya</label>
                    </div>

                    <button type="submit" class="btn-block">Log In</button>
                </form>

                <a href="{{ route('password.request') }}" class="auth-card__forgot">forgot password?</a>
            </div>

        </div>
    </div>

</body>
</html>