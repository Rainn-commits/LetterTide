@php
    $currentRole = auth()->user()->role->nama_role ?? null;

    $dashboardRoute = match ($currentRole) {
        'Super Admin' => 'dashboard.superadmin',
        'Admin'       => 'dashboard.admin',
        'User'        => 'dashboard.user',
        default       => 'dashboard.user',
    };

    $menuItems = [
        [
            'label' => 'Dashboard - ' . ($currentRole ?? ''),
            'route' => $dashboardRoute,
            'active' => request()->routeIs('dashboard.*'),
            'roles' => ['Super Admin', 'Admin', 'User'],
            'icon' => 'grid',
        ],
        [
            'label' => 'Buat Surat',
            'route' => 'surat.create',
            'active' => request()->routeIs('surat.*'),
            'roles' => ['Super Admin', 'Admin', 'User'],
            'icon' => 'mail',
        ],
        [
            'label' => 'Template Surat',
            'route' => 'template-surat.index',
            'active' => request()->routeIs('template-surat.*'),
            'roles' => ['Super Admin', 'Admin', 'User'],
            'icon' => 'mail',
        ],
        [
            'label' => 'Arsip Digital',
            'route' => 'arsip.index',
            'active' => request()->routeIs('arsip.*'),
            'roles' => ['Super Admin', 'Admin', 'User'],
            'icon' => 'archive',
        ],
        [
            'label' => 'Pengaturan Kop Surat',
            'route' => 'kop-surat.index',
            'active' => request()->routeIs('kop-surat.*'),
            'roles' => ['Super Admin', 'Admin'],
            'icon' => 'settings',
        ],
        [
            'label' => 'Kelola Pengguna',
            'route' => 'pengguna.index',
            'active' => request()->routeIs('pengguna.*'),
            'roles' => ['Super Admin', 'Admin'],
            'icon' => 'users',
        ],
        [
            'label' => 'Kelola Role & Hak Akses',
            'route' => 'role-akses.index',
            'active' => request()->routeIs('role-akses.*'),
            'roles' => ['Super Admin'],
            'icon' => 'user',
        ],
    ];
@endphp

<aside class="sidebar" id="sidebar">
    <div class="sidebar__brand" style="cursor: default;">
        <span class="sidebar__logo-mark">
            <img src="{{ asset('images/logo.png') }}" alt="Logo LetterTide" class="navbar__logo-img">
        </span>
        <span class="sidebar__brand-name">LetterTide</span>
    </div>

    <nav class="sidebar__nav">
        @foreach ($menuItems as $item)
            @continue($currentRole && !in_array($currentRole, $item['roles']))
            <a href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
               class="sidebar__link {{ $item['active'] ? 'is-active' : '' }}">
                @switch($item['icon'])
                    @case('grid')
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="3" width="8" height="8" rx="1.5" stroke="currentColor" stroke-width="1.6"/><rect x="13" y="3" width="8" height="8" rx="1.5" stroke="currentColor" stroke-width="1.6"/><rect x="3" y="13" width="8" height="8" rx="1.5" stroke="currentColor" stroke-width="1.6"/><rect x="13" y="13" width="8" height="8" rx="1.5" stroke="currentColor" stroke-width="1.6"/></svg>
                        @break
                    @case('mail')
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 6.5C3 5.67 3.67 5 4.5 5H19.5C20.33 5 21 5.67 21 6.5V17.5C21 18.33 20.33 19 19.5 19H4.5C3.67 19 3 18.33 3 17.5V6.5Z" stroke="currentColor" stroke-width="1.6"/><path d="M4 6.5L12 13L20 6.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        @break
                    @case('archive')
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="4" width="18" height="5" rx="1.2" stroke="currentColor" stroke-width="1.6"/><path d="M5 9V18C5 18.5523 5.44772 19 6 19H18C18.5523 19 19 18.5523 19 18V9" stroke="currentColor" stroke-width="1.6"/><path d="M10 13H14" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                        @break
                    @case('settings')
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.6"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09a1.65 1.65 0 00-1.08-1.51 1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09a1.65 1.65 0 001.51-1.08 1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z" stroke="currentColor" stroke-width="1.3"/></svg>
                        @break
                    @case('users')
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17 20v-1.5a3.5 3.5 0 00-3.5-3.5h-5A3.5 3.5 0 005 18.5V20" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><circle cx="9.5" cy="8" r="3" stroke="currentColor" stroke-width="1.6"/><path d="M16 8.5a2.5 2.5 0 010 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><path d="M19 20v-1.5a3.3 3.3 0 00-2-3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                        @break
                    @default
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="8" r="3.5" stroke="currentColor" stroke-width="1.6"/><path d="M5 20c0-3.3 3.1-6 7-6s7 2.7 7 6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                @endswitch
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="sidebar__footer">
        <a href="{{ route('profile.edit') }}" class="sidebar__profile-link" title="Kelola Profil">
            <span class="sidebar__avatar">{{ Str::of(auth()->user()->name ?? 'U')->explode(' ')->map(fn ($w) => strtoupper($w[0] ?? ''))->take(2)->implode('') }}</span>
            <div class="sidebar__user">
                <div class="sidebar__user-name">{{ auth()->user()->name ?? 'Pengguna' }}</div>
            </div>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar__logout" title="Log Out">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M16 17l5-5-5-5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 12H9" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Log Out
            </button>
        </form>
    </div>
</aside>