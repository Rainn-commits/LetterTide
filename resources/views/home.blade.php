<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LetterTide</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    @vite([
        'resources/css/variables.css',
        'resources/css/home.css'
    ])

    <style>
        .hero {
            background-image: url("{{ asset('images/background.png') }}");
        }
    </style>
</head>
<body>

    <header class="navbar">
          <a href="{{ route('home') }}" class="navbar__brand">
            <img src="{{ asset('images/logo.png') }}" alt="Logo LetterTide" class="navbar__logo-img">
            <span class="navbar__brand-name">LetterTide</span>
        </a>

        <nav class="navbar__links">
            <a href="#">Buat Surat</a>
            <a href="#">Template Surat</a>
            <a href="#">Arsip Digital</a>
        </nav>

        <div class="navbar__actions">
            <a href="{{ route('login') }}" class="btn-signin">Log In</a>
        </div>
    </header>

    <section class="hero">
        <div class="hero__content">
            <h1 class="hero__title">
                KELOLA SURAT PERUSAHAAN<br>
                LEBIH RAPI DAN EFISIEN
            </h1>
            <p class="hero__subtitle">
                Sistem manajemen administrasi persuratan untuk PT<br>
                Inovindo Digital Media
            </p>
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

    <div id="idleCutscene" class="idle-cutscene">
        <video id="cutsceneVideo" loop muted playsinline>
            <source src="https://assets.mixkit.co/videos/preview/mixkit-top-view-of-a-modern-city-building-41556-large.mp4" type="video/mp4">
        </video>
        <div class="idle-overlay-text">
            <p>🎥 DEMO MODE - Tekan tombol/gerakkan mouse untuk kembali</p>
        </div>
    </div>

    <style>
    .idle-cutscene {
        position: fixed;
        top: 0; left: 0; width: 100vw; height: 100vh;
        background: #000;
        z-index: 9999;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.8s ease-in-out;
    }
    .idle-cutscene.active {
        opacity: 1;
        pointer-events: auto;
    }
    .idle-cutscene video {
        width: 100%; height: 100%; object-fit: cover;
    }
    .idle-overlay-text {
        position: absolute; bottom: 30px; left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.7);
        color: #fff; padding: 10px 24px; border-radius: 30px;
        font-family: sans-serif; font-size: 14px; letter-spacing: 1px;
    }
    </style>

    <script>
    let idleTime = 0;
    const idleLimit = 10;
    const cutscene = document.getElementById('idleCutscene');
    const video = document.getElementById('cutsceneVideo');

    function resetTimer() {
        idleTime = 0;
        if (cutscene.classList.contains('active')) {
            cutscene.classList.remove('active');
            video.pause();
        }
    }

    window.onload = resetTimer;
    window.onmousemove = resetTimer;
    window.onmousedown = resetTimer;
    window.onclick = resetTimer;
    window.onkeypress = resetTimer;
    window.addEventListener('scroll', resetTimer, true);

    setInterval(() => {
        idleTime++;
        if (idleTime >= idleLimit) {
            if (!cutscene.classList.contains('active')) {
                cutscene.classList.add('active');
                video.play();
            }
        }
    }, 1000);
    </script>
</html>