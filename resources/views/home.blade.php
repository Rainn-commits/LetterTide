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
        'resources/css/home.css',
        'resources/js/hero-parallax.js'
    ])

    <style>
        .hero {
            background-image: url("{{ asset('images/background.png') }}");
        }
    </style>
</head>
<body>

    <header class="navbar">
        <div class="navbar__brand">
            <img src="{{ asset('images/logo.png') }}" alt="Logo LetterTide" class="navbar__logo-img">
            <span class="navbar__brand-name">LetterTide</span>
        </div>

        <nav class="navbar__links" id="navbarLinks">
            <a href="#">Buat Surat</a>
            <a href="#">Template Surat</a>
            <a href="#">Pengaturan Kop surat</a>
            <a href="#">Arsip Digital</a>
            <a href="#">Evaluasi Retensi Arsip</a>
        </nav>

        <div class="navbar__actions">
            <a href="{{ route('login') }}" class="btn-signin">Log In</a>
        </div>

        <button type="button" class="navbar__toggle" id="navbarToggle" aria-label="Buka menu" aria-expanded="false" aria-controls="navbarLinks">
            <svg class="navbar__toggle-icon navbar__toggle-icon--open" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 6H20M4 12H20M4 18H20" stroke="#101820" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <svg class="navbar__toggle-icon navbar__toggle-icon--close" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:none;">
                <path d="M6 6L18 18M6 18L18 6" stroke="#101820" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>
    </header>

    <script>
        (function () {
            var toggle = document.getElementById('navbarToggle');
            var links = document.getElementById('navbarLinks');
            if (!toggle || !links) return;

            toggle.addEventListener('click', function () {
                var isOpen = links.classList.toggle('is-open');
                toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                toggle.querySelector('.navbar__toggle-icon--open').style.display = isOpen ? 'none' : 'block';
                toggle.querySelector('.navbar__toggle-icon--close').style.display = isOpen ? 'block' : 'none';
            });

            links.querySelectorAll('a').forEach(function (a) {
                a.addEventListener('click', function () {
                    links.classList.remove('is-open');
                    toggle.setAttribute('aria-expanded', 'false');
                    toggle.querySelector('.navbar__toggle-icon--open').style.display = 'block';
                    toggle.querySelector('.navbar__toggle-icon--close').style.display = 'none';
                });
            });
        })();
    </script>

    <section class="hero">
        <div class="hero__content">
            <h1 class="hero__title">
                KELOLA SURAT PERUSAHAAN<br>
                LEBIH RAPI DAN EFISIEN
            </h1>
            <p class="hero__subtitle">
                Sistem manajemen administrasi persuratan untuk<br>
                PT Inovindo Digital Media
            </p>
        </div>

        <div class="hero__scene" aria-hidden="true">

            <div class="hero__glow hero__glow--1"></div>
            <div class="hero__glow hero__glow--2"></div>
            <div class="hero__dotgrid"></div>

            <div class="hero__card hero__card--main">
                <div class="hero__card-float" data-depth="0.03">
                    <div class="hero__card-bar">
                        <span class="hero__card-control hero__card-control--min" aria-hidden="true">&minus;</span>
                        <span class="hero__card-control hero__card-control--close" aria-hidden="true">&times;</span>
                    </div>
                    <div class="hero__card-body">
                        <div class="hero__line hero__line--title"></div>
                        <div class="hero__line"></div>
                        <div class="hero__line"></div>
                        <div class="hero__line hero__line--short"></div>
                        <div class="hero__line"></div>
                        <div class="hero__line hero__line--short"></div>
                        <span class="hero__cursor"></span>
                    </div>
                </div>
            </div>

            <div class="hero__card hero__card--stat">
                <div class="hero__card-float" data-depth="0.07">
                    <span class="hero__stat-icon">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 13l4 4L19 7" stroke="#FFFFFF" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="hero__stat-text">
                        <strong>Surat Dibuat</strong>
                        <span>128 Surat</span>
                    </span>
                </div>
            </div>

            <div class="hero__card hero__card--progress">
                <div class="hero__card-float" data-depth="0.06">
                    <svg viewBox="0 0 64 64" class="hero__ring">
                        <circle cx="32" cy="32" r="26" fill="none" stroke="rgba(11,18,32,0.12)" stroke-width="6"/>
                        <circle cx="32" cy="32" r="26" fill="none" stroke="#0EA1AB" stroke-width="6" stroke-dasharray="163.4" stroke-dashoffset="41" stroke-linecap="round" transform="rotate(-90 32 32)"/>
                        <text x="32" y="37" text-anchor="middle" font-family="Inter, sans-serif" font-size="14" font-weight="700" fill="#0B1220">75%</text>
                    </svg>
                    <span>Progres Draft</span>
                </div>
            </div>

            <div class="hero__card hero__card--envelope">
                <div class="hero__card-float" data-depth="0.09">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 6.5C3 5.67 3.67 5 4.5 5H19.5C20.33 5 21 5.67 21 6.5V17.5C21 18.33 20.33 19 19.5 19H4.5C3.67 19 3 18.33 3 17.5V6.5Z" stroke="#6FE0E8" stroke-width="1.8"/>
                        <path d="M4 6.5L12 13L20 6.5" stroke="#6FE0E8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>

            <div class="hero__card hero__card--approval">
                <div class="hero__card-float" data-depth="0.05">
                    <span class="hero__approval-icon">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="9" stroke="#FFFFFF" stroke-width="1.8"/>
                            <path d="M12 7.5V12L15 14" stroke="#FFFFFF" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="hero__stat-text">
                        <strong>Menunggu Approval</strong>
                        <span>17 Surat</span>
                    </span>
                </div>
            </div>

        </div>

        <div class="hero__flying-container" aria-hidden="true">
            <div class="hero__flying-object">
                <svg class="flying-svg flying-svg--envelope" viewBox="0 0 40 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="40" height="28" rx="4" fill="#0B1220"/>
                    <path d="M2 3L20 17L38 3" stroke="#6FE0E8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M2 25L14 14" stroke="rgba(111,224,232,0.4)" stroke-width="2"/>
                    <path d="M38 25L26 14" stroke="rgba(111,224,232,0.4)" stroke-width="2"/>
                </svg>

                <svg class="flying-svg flying-svg--plane" viewBox="0 0 44 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2 14L42 2L28 14L42 26Z" fill="#0B1220"/>
                    <path d="M28 14L16 18L2 14L28 14Z" fill="#0EA1AB"/>
                    <path d="M28 14L42 2L36 26Z" fill="#233240" opacity="0.9"/>
                </svg>

                <div class="hero__sparkles">
                    <span class="sparkle sp-1"></span>
                    <span class="sparkle sp-2"></span>
                    <span class="sparkle sp-3"></span>
                    <span class="sparkle sp-4"></span>
                </div>
            </div>
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