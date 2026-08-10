<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LetterTide — Kelola Surat Perusahaan Lebih Rapi dan Efisien</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    @vite([
        'resources/css/variables.css',
        'resources/css/home.css',
        'resources/css/pages/login.css',
        'resources/js/password-toggle.js',
    ])

</head>
<body>

    <header class="navbar">
        <a href="index.html" class="navbar__brand">
            <span class="navbar__logo-mark">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M3 6.5C3 5.67157 3.67157 5 4.5 5H19.5C20.3284 5 21 5.67157 21 6.5V17.5C21 18.3284 20.3284 19 19.5 19H4.5C3.67157 19 3 18.3284 3 17.5V6.5Z" stroke="white" stroke-width="1.6" stroke-linejoin="round"/>
                    <path d="M4 6.5L12 13L20 6.5" stroke="white" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <span class="navbar__brand-name">LetterTide</span>
        </a>

        <nav class="navbar__links">
            <a href="#">Buat Surat</a>
            <a href="#">Template Surat</a>
            <a href="#">Arsip Digital</a>
        </nav>

        <div class="navbar__actions">
            <a href="login.html" class="navbar__login">Log In</a>
            <a href="register.html" class="btn-signin">Sign In</a>
        </div>
    </header>

    <section class="hero">
        <div class="hero__skyline" aria-hidden="true"></div>

        <div class="hero__content">
            <h1 class="hero__title">Kelola Surat Perusahaan<br>Lebih Rapi dan Efisien</h1>
            <p class="hero__subtitle">
                Sistem manajemen administrasi persuratan untuk PT Inovindo Digital Media
            </p>
            <div class="hero__cta">
                <a href="register.html" class="btn-primary">Mulai Sekarang</a>
                <a href="login.html" class="btn-outline">Log In</a>
            </div>
        </div>

        <div class="hero__illustration" aria-hidden="true">
            <svg viewBox="0 0 320 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="10" y="30" width="70" height="220" rx="6" fill="#0FA9B4"/>
                <rect x="18" y="46" width="54" height="10" rx="3" fill="#E6FBFC"/>
                <rect x="18" y="70" width="54" height="10" rx="3" fill="#E6FBFC"/>
                <rect x="70" y="60" width="70" height="220" rx="6" fill="#101820"/>
                <rect x="78" y="76" width="54" height="10" rx="3" fill="#2FC4D0"/>
                <rect x="78" y="100" width="54" height="10" rx="3" fill="#2FC4D0"/>
                <g transform="translate(90,10) rotate(-6 90 150)">
                    <rect x="20" y="20" width="150" height="230" rx="8" fill="#FFFFFF" stroke="#101820" stroke-width="3"/>
                    <rect x="40" y="46" width="40" height="40" rx="4" fill="#101820"/>
                    <rect x="90" y="50" width="60" height="8" rx="3" fill="#101820"/>
                    <rect x="90" y="66" width="45" height="8" rx="3" fill="#101820"/>
                    <rect x="40" y="104" width="110" height="6" rx="3" fill="#CFD8DB"/>
                    <rect x="40" y="120" width="110" height="6" rx="3" fill="#CFD8DB"/>
                    <rect x="40" y="136" width="110" height="6" rx="3" fill="#CFD8DB"/>
                    <rect x="40" y="152" width="80" height="6" rx="3" fill="#CFD8DB"/>
                </g>
            </svg>
        </div>

        <div class="hero__gallery" aria-hidden="true">
            <svg viewBox="0 0 320 260" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="0" y="20" width="150" height="200" rx="10" fill="#101820" transform="rotate(-8 75 120)"/>
                <rect x="40" y="10" width="150" height="200" rx="10" fill="#FFFFFF" stroke="#101820" stroke-width="3" transform="rotate(-3 115 110)"/>
                <rect x="90" y="30" width="180" height="220" rx="10" fill="#2FC4D0"/>
                <circle cx="140" cy="90" r="22" fill="#101820"/>
                <path d="M120 190 L170 130 L210 170 L250 120 L270 190 Z" fill="#0B1F2A"/>
                <rect x="105" y="45" width="150" height="180" rx="6" fill="none" stroke="#FFFFFF" stroke-width="2"/>
            </svg>
        </div>

        <div class="hero__archive" aria-hidden="true">
            <svg viewBox="0 0 150 220" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="10" y="10" width="130" height="60" rx="8" fill="#FFFFFF" stroke="#101820" stroke-width="3"/>
                <rect x="10" y="80" width="130" height="60" rx="8" fill="#FFFFFF" stroke="#101820" stroke-width="3"/>
                <rect x="10" y="150" width="130" height="60" rx="8" fill="#FFFFFF" stroke="#101820" stroke-width="3"/>
                <rect x="55" y="30" width="40" height="10" rx="3" fill="#2FC4D0"/>
                <rect x="55" y="100" width="40" height="10" rx="3" fill="#2FC4D0"/>
                <rect x="55" y="170" width="40" height="10" rx="3" fill="#2FC4D0"/>
            </svg>
        </div>
        <div class="hero__walker" aria-hidden="true">
            <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <g class="walker__body">
                    <circle cx="50" cy="18" r="9" fill="#101820"/>
                    <rect x="41" y="27" width="18" height="32" rx="8" fill="#0FA9B4"/>
                    <line x1="42" y1="33" x2="30" y2="48" stroke="#101820" stroke-width="5" stroke-linecap="round" class="walker__arm-back"/>
                    <line x1="58" y1="33" x2="70" y2="44" stroke="#101820" stroke-width="5" stroke-linecap="round"/>
                    <g transform="rotate(15 74 43)">
                        <rect x="66" y="38" width="16" height="11" rx="1.5" fill="#FFFFFF" stroke="#101820" stroke-width="2"/>
                        <path d="M67 39 L74 44 L81 39" stroke="#101820" stroke-width="1.5" fill="none"/>
                    </g>
                    <line x1="45" y1="58" x2="38" y2="80" stroke="#101820" stroke-width="6" stroke-linecap="round" class="walker__leg-back"/>
                    <line x1="55" y1="58" x2="62" y2="80" stroke="#101820" stroke-width="6" stroke-linecap="round" class="walker__leg-front"/>
                </g>
            </svg>
        </div>
        <div class="hero__plane hero__plane--1" aria-hidden="true">
            <svg viewBox="0 0 40 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M2 12L36 2L24 12L36 22Z" fill="#0FA9B4"/>
                <path d="M24 12L14 15L2 12L24 12Z" fill="#0B8A93"/>
            </svg>
        </div>
        <div class="hero__plane hero__plane--2" aria-hidden="true">
            <svg viewBox="0 0 40 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M2 12L36 2L24 12L36 22Z" fill="#FFFFFF"/>
                <path d="M24 12L14 15L2 12L24 12Z" fill="#CFEFF1"/>
            </svg>
        </div>
        <div class="hero__plane hero__plane--3" aria-hidden="true">
            <svg viewBox="0 0 40 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M2 12L36 2L24 12L36 22Z" fill="#101820"/>
                <path d="M24 12L14 15L2 12L24 12Z" fill="#0B1F2A"/>
            </svg>
        </div>
    </section>

</body>
</html>
