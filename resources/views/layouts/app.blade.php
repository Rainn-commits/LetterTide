<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('page-title', 'LetterTide')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@500;600;700;800&display=swap" rel="stylesheet">

    @vite([
        'resources/css/app.css', 
        'resources/css/app-shell.css',
        'resources/js/app.js',
        'resources/js/confirm-modal.js'
    ])
    
    @yield('page-css')
    @stack('styles')
</head>
<body>

    <div class="dash">
        @include('partials.sidebar')

        <div class="dash__main">
            @include('partials.topbar')

            <main class="dash__content">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');

        sidebarToggle?.addEventListener('click', function (e) {
            e.stopPropagation(); 
            sidebar?.classList.toggle('is-open');
        });

        document.addEventListener('click', function (e) {
            if (sidebar?.classList.contains('is-open') && 
                !sidebar.contains(e.target) && 
                !sidebarToggle.contains(e.target)) {
                
                sidebar.classList.remove('is-open');
            }
        });
    </script>

    <div class="confirm-modal-overlay" id="themedConfirmOverlay" aria-hidden="true">
        <div class="confirm-modal" role="alertdialog" aria-modal="true" aria-labelledby="confirmModalTitle" aria-describedby="confirmModalMessage">
            <div class="confirm-modal__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
            <h3 class="confirm-modal__title" id="confirmModalTitle">Konfirmasi</h3>
            <p class="confirm-modal__message" id="confirmModalMessage"></p>
            <div class="confirm-modal__actions">
                <button type="button" class="confirm-modal__btn confirm-modal__btn--cancel" id="confirmModalCancel">Batal</button>
                <button type="button" class="confirm-modal__btn confirm-modal__btn--confirm" id="confirmModalConfirm">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>

    @yield('page-js')
    @stack('scripts')
</body>
</html>
