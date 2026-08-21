<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('page-title', 'LetterTide')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@500;600;700;800&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite([
        'resources/css/app.css', 
        'resources/css/app-shell.css',
        'resources/js/app.js'
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

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

    @yield('page-js')
    @stack('scripts')
</body>
</html>
