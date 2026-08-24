@php
    $roleName = auth()->user()->role->nama_role ?? '';

    $dashboardRoute = match (true) {
        str_contains($roleName, 'Super Admin') => 'dashboard.superadmin',
        str_contains($roleName, 'Admin')       => 'dashboard.admin',
        default                                => 'dashboard.user',
    };

    if (!Route::has($dashboardRoute)) {
        $dashboardRoute = 'dashboard';
    }

    $menuItems = [
        [
            'label' => 'Dashboard',
            'route' => $dashboardRoute,
            'active' => request()->routeIs('dashboard*'),
            'roles' => ['Super Admin', 'Admin', 'User'],
            'icon' => 'grid',
        ],
        [
            'label'  => 'Kelola Profil',
            'route'  => 'profile.edit',
            'active' => request()->routeIs('profile.*'),
            'roles'  => ['Super Admin', 'Admin', 'User'], 
            'icon'   => 'user',
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
            'icon' => 'document',
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
            'label' => 'Kelola Role dan Hak Akses',
            'route' => 'role.index',
            'active' => request()->routeIs('role-akses.*'),
            'roles' => ['Super Admin'],
            'icon' => 'shield',
        ],
        [
            'label' => 'Log Aktivitas Sistem',
            'route' => 'log-aktivitas.index',
            'active' => request()->routeIs('log-aktivitas.*'),
            'roles' => ['Super Admin'],
            'icon' => 'log',        
        ],
        [
            'label'  => 'Evaluasi Retensi Arsip',
            'route'  => 'evaluasi-retensi.index',
            'active' => request()->routeIs('evaluasi-retensi.*'),
            'roles'  => ['Super Admin', 'Admin'], 
            'icon'   => 'archive-clock', 
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
            @continue($roleName && !in_array($roleName, $item['roles']))

            <a href="{{ Route::has($item['route']) ? route($item['route']) : url('/dashboard') }}"
               class="sidebar__link {{ $item['active'] ? 'is-active' : '' }}">
                
                @switch($item['icon'])
                    @case('grid')
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="3" width="8" height="8" rx="1.5" stroke="currentColor" stroke-width="1.6"/><rect x="13" y="3" width="8" height="8" rx="1.5" stroke="currentColor" stroke-width="1.6"/><rect x="3" y="13" width="8" height="8" rx="1.5" stroke="currentColor" stroke-width="1.6"/><rect x="13" y="13" width="8" height="8" rx="1.5" stroke="currentColor" stroke-width="1.6"/></svg>
                        @break
                    @case('user')
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    @break
                    @case('mail')
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 6.5C3 5.67 3.67 5 4.5 5H19.5C20.33 5 21 5.67 21 6.5V17.5C21 18.33 20.33 19 19.5 19H4.5C3.67 19 3 18.33 3 17.5V6.5Z" stroke="currentColor" stroke-width="1.6"/><path d="M4 6.5L12 13L20 6.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        @break
                    @case('document')
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 3.5h7l4 4V19.5a1.5 1.5 0 01-1.5 1.5h-9.5a1.5 1.5 0 01-1.5-1.5V5A1.5 1.5 0 017 3.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M14 3.5V7a1 1 0 001 1h3.3" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M8.5 13H15.5M8.5 16.2H15.5M8.5 9.8H11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                        @break
                    @case('archive')
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="4" width="18" height="5" rx="1.2" stroke="currentColor" stroke-width="1.6"/><path d="M5 9V18C5 18.5523 5.44772 19 6 19H18C18.5523 19 19 18.5523 19 18V9" stroke="currentColor" stroke-width="1.6"/><path d="M10 13H14" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                        @break
                    @case('settings')
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.6"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09a1.65 1.65 0 00-1.08-1.51 1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09a1.65 1.65 0 001.51-1.08 1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z" stroke="currentColor" stroke-width="1.3"/></svg>
                        @break
                    @case('users')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        @break
                    @case('shield')
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                        @break
                    @case('log')
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 21V14M4 10V3M12 21V12M12 8V3M20 21V16M20 12V3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            <circle cx="4" cy="12" r="2" stroke="currentColor" stroke-width="1.6"/>
                            <circle cx="12" cy="10" r="2" stroke="currentColor" stroke-width="1.6"/>
                            <circle cx="20" cy="14" r="2" stroke="currentColor" stroke-width="1.6"/>
                        </svg>
                        @break
                    @case('archive-clock')
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="20" height="20">
                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 10v4l2 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        @break
                    @default
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="8" r="3.5" stroke="currentColor" stroke-width="1.6"/><path d="M5 20c0-3.3 3.1-6 7-6s7 2.7 7 6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                @endswitch
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="sidebar__footer">
        <a href="{{ Route::has('profile.edit') ? route('profile.edit') : '#' }}" class="sidebar__profile-link" title="Kelola Profil">
            <span class="sidebar__avatar">{{ Str::of(auth()->user()->name ?? 'U')->explode(' ')->map(fn ($w) => strtoupper($w[0] ?? ''))->take(2)->implode('') }}</span>
            <div class="sidebar__user">
                <div class="sidebar__user-name">{{ auth()->user()->name ?? 'Pengguna' }}</div>
            </div>
        </a>
        <form method="POST" action="{{ route('logout') }}" class="sidebar__logout-form">
            @csrf
            <button type="submit" class="sidebar__logout" title="Log Out">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M16 17l5-5-5-5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 12H9" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Log Out
            </button>
        </form>
    </div>
</aside>
