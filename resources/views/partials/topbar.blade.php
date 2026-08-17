<header class="topbar">
    <div class="topbar__left">
        <button type="button" class="topbar__toggle" id="sidebarToggle" onclick="toggleSidebar(event)" aria-label="Buka menu">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 6H20M4 12H20M4 18H20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
        </button>
        <h1 class="topbar__title">@yield('page-title', 'LetterTide')</h1>
    </div>

    <div class="topbar__right">
        <button type="button" class="topbar__bell" aria-label="Notifikasi">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M13.73 21a2 2 0 01-3.46 0" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            </svg>
            <span class="topbar__bell-dot" aria-hidden="true"></span>
        </button>

        <a href="{{ route('profile.edit') }}" class="topbar__avatar" title="Kelola Profil">{{ Str::of(auth()->user()->name ?? 'U')->explode(' ')->map(fn ($w) => strtoupper($w[0] ?? ''))->take(2)->implode('') }}</a>
    </div>
</header>