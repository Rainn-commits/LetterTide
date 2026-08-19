<header class="topbar">
    <div class="topbar__left">
        <button type="button" class="topbar__toggle" id="sidebarToggle" onclick="toggleSidebar(event)" aria-label="Buka menu">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 6H20M4 12H20M4 18H20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
        </button>

        @php    
            $roleName = auth()->user()->role->nama_role ?? 'User';
        @endphp

        <div class="topbar__greeting">
            <h1 class="topbar__title">Halo, {{ $roleName }}!</h1>
        </div>
    </div>

    <div class="topbar__right">
        @auth
            @php
                $unreadNotifications = auth()->user()->unreadNotifications;
            @endphp

            <div class="notification-wrapper">
                <button type="button" class="topbar__bell" id="notifDropdownBtn" aria-label="Notifikasi">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M13.73 21a2 2 0 01-3.46 0" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                    </svg>

                    @if($unreadNotifications->count() > 0)
                        <span class="notification-badge">{{ $unreadNotifications->count() }}</span>
                    @endif
                </button>

                <div class="notification-menu" id="notifDropdownMenu">
                    <div class="notif-header">
                        <h3>Notifikasi</h3>
                        @if($unreadNotifications->count() > 0)
                            <span class="notif-count-text">{{ $unreadNotifications->count() }} Baru</span>
                        @endif
                    </div>

                    <div class="notif-body">
                        @forelse($unreadNotifications as $notification)
                           <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST"> 
                                @csrf
                                <button type="submit" class="notif-item" style="background: none; border: none; padding: 0; width: 100%; text-align: left; cursor: pointer;">
                                    <div class="notif-icon-box">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                        </svg>
                                    </div>
                                    <div class="notif-content">
                                        <p class="notif-title">{{ $notification->data['title'] ?? 'Permintaan Reset' }}</p>
                                        <p class="notif-desc">{{ $notification->data['message'] ?? '' }}</p>
                                        <span class="notif-time">{{ $notification->created_at->diffForHumans() }}</span>
                                    </div>
                                </button>
                            </form>
                        @empty
                            <div class="notif-empty">
                                <p>Tidak ada notifikasi baru</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endauth

        <a href="{{ route('profile.edit') }}" class="topbar__avatar" title="Kelola Profil">
            {{ Str::of(auth()->user()->name ?? 'U')->explode(' ')->map(fn ($w) => strtoupper($w[0] ?? ''))->take(2)->implode('') }}
        </a>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.getElementById('notifDropdownBtn');
        const menu = document.getElementById('notifDropdownMenu');

        if (btn && menu) {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                menu.classList.toggle('is-active');
            });

            document.addEventListener('click', function (e) {
                if (!menu.contains(e.target) && !btn.contains(e.target)) {
                    menu.classList.remove('is-active');
                }
            });
        }
    });
</script>
