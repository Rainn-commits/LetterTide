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

        <div style="position: relative; display: inline-block;">
            <button type="button" class="topbar__avatar" id="profileDropdownBtn" aria-label="Menu Profil" style="cursor: pointer; border: none; outline: none;">
                {{ Str::of(auth()->user()->name ?? 'U')->explode(' ')->map(fn ($w) => strtoupper($w[0] ?? ''))->take(2)->implode('') }}
            </button>

            <div id="profileDropdownMenu" style="display: none; position: absolute; right: 0; top: calc(100% + 8px); width: 170px; background: #ffffff; border-radius: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.12); border: 1px solid #eaeaea; z-index: 1000; overflow: hidden;">
                <a href="{{ route('profile.edit') }}" style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; text-decoration: none; color: #333; font-size: 13px; font-weight: 500;" onmouseover="this.style.background='#f5f5f5'" onmouseout="this.style.background='transparent'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    Profil Saya
                </a>

                <div style="height: 1px; background: #eee; margin: 2px 0;"></div>

                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; width: 100%; border: none; background: transparent; color: #dc3545; font-size: 13px; font-weight: 500; cursor: pointer; text-align: left;" onmouseover="this.style.background='#fff0f0'" onmouseout="this.style.background='transparent'">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        Log Out
                    </button>
                </form>
            </div>
        </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const notifBtn = document.getElementById('notifDropdownBtn');
        const notifMenu = document.getElementById('notifDropdownMenu');
        const profileBtn = document.getElementById('profileDropdownBtn');
        const profileMenu = document.getElementById('profileDropdownMenu');

        if (notifBtn && notifMenu) {
            notifBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                if (profileMenu) profileMenu.style.display = 'none';
                notifMenu.classList.toggle('is-active');
            });
        }

        if (profileBtn && profileMenu) {
            profileBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                if (notifMenu) notifMenu.classList.remove('is-active');
                profileMenu.style.display = (profileMenu.style.display === 'block') ? 'none' : 'block';
            });
        }

        document.addEventListener('click', function (e) {
            if (notifMenu && !notifMenu.contains(e.target) && notifBtn && !notifBtn.contains(e.target)) {
                notifMenu.classList.remove('is-active');
            }
            if (profileMenu && !profileMenu.contains(e.target) && profileBtn && !profileBtn.contains(e.target)) {
                profileMenu.style.display = 'none';
            }
        });
    });
</script>
