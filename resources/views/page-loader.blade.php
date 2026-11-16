@vite(['resources/css/page-loader.css', 'resources/js/page-loader.js'])

<div class="page-loader" id="pageLoader" role="status" aria-live="polite" hidden>
    <div class="page-loader__card">
        <div class="page-loader__mark">
            <span class="page-loader__ring"></span>
            <span class="page-loader__logo">
                @include('partials.logo-icon')
            </span>
        </div>

        <p class="page-loader__text" id="pageLoaderText">Memuat halaman</p>

        <div class="page-loader__tide" aria-hidden="true">
            <span></span><span></span><span></span>
        </div>
    </div>
</div>
