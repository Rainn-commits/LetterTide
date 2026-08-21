@extends('layouts.app')

@section('page-title', 'Dashboard - Super Admin')

@section('page-css')
    @vite('resources/css/dashboard.css')
@endsection

@section('content')
    <div class="stat-grid">
        <a href="{{ Route::has('arsip.index') ? route('arsip.index') : '#' }}" class="stat-card">
            <span class="stat-card__icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 6.5C3 5.67 3.67 5 4.5 5H19.5C20.33 5 21 5.67 21 6.5V17.5C21 18.33 20.33 19 19.5 19H4.5C3.67 19 3 18.33 3 17.5V6.5Z" stroke="currentColor" stroke-width="1.6"/><path d="M4 6.5L12 13L20 6.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <div>
                <div class="stat-card__value">{{ number_format($stats['total_surat']) }}</div>
                <div class="stat-card__label">Total Surat</div>
            </div>
        </a>

        <a href="{{ Route::has('pengguna.index') ? route('pengguna.index') : url('/pengguna') }}" class="stat-card">
            <span class="stat-card__icon stat-card__icon--dark">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </span>
            <div>
                <div class="stat-card__value">{{ number_format($stats['total_pengguna']) }}</div>
                <div class="stat-card__label">Total Pengguna</div>
            </div>
        </a>

        <a href="{{ Route::has('role-akses.index') ? route('role-akses.index') : '#' }}" class="stat-card">
            <span class="stat-card__icon stat-card__icon--accent">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="8" r="3.5" stroke="currentColor" stroke-width="1.6"/><path d="M5 20c0-3.3 3.1-6 7-6s7 2.7 7 6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
            </span>
            <div>
                <div class="stat-card__value">{{ number_format($stats['total_role']) }}</div>
                <div class="stat-card__label">Total Role</div>
            </div>
        </a>

        <div class="stat-card stat-card--static">
            <span class="stat-card__icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.6"/><path d="M3 10H21" stroke="currentColor" stroke-width="1.6"/><path d="M8 3V6M16 3V6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
            </span>
            <div>
                <div class="stat-card__value">{{ number_format($stats['surat_bulan_ini']) }}</div>
                <div class="stat-card__label">Surat Bulan Ini</div>
            </div>
        </div>
    </div>

    <div class="dash-columns">
        <div class="card">
            <div class="card__header">
                <h3 class="card__title">Traffic Surat</h3>
                <span class="card__hint">6 bulan terakhir</span>
            </div>

            @if (!empty($trafficData) && array_sum($trafficData) > 0)
                <div class="traffic-chart">
                    @php $max = max($trafficData) ?: 1; @endphp
                    @foreach ($trafficData as $label => $count)
                        @php $heightPercentage = max(6, round(($count / $max) * 100)); @endphp
                        <div class="traffic-chart__col {{ $loop->last ? 'traffic-chart__col--current' : '' }}" data-label="{{ $label }}" data-count="{{ $count }}">
                            <div class="traffic-chart__bar" style="--bar-height: {{ $heightPercentage }}%;"></div>
                            <span class="traffic-chart__label">{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
                <p class="traffic-chart__detail" id="trafficDetail">Klik salah satu bar buat lihat detail jumlahnya.</p>
            @else
                <p class="traffic-chart__empty">
                    Belum ada traffic surat dalam 6 bulan terakhir.
                </p>
            @endif
        </div>

        <div class="card">
            <div class="card__header">
                <h3 class="card__title">Aktivitas Terbaru</h3>
            </div>

            @if ($aktivitasTerbaru->isNotEmpty())
                <div class="dash-search">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/><path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    <input type="text" id="activitySearch" placeholder="Cari template atau nama pengguna...">
                </div>

                <ul class="activity-list">
                    @foreach ($aktivitasTerbaru as $surat)
                        <li class="activity-item">
                            <span class="activity-item__icon">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 6.5C3 5.67 3.67 5 4.5 5H19.5C20.33 5 21 5.67 21 6.5V17.5C21 18.33 20.33 19 19.5 19H4.5C3.67 19 3 18.33 3 17.5V6.5Z" stroke="currentColor" stroke-width="1.6"/><path d="M4 6.5L12 13L20 6.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                            <div class="activity-item__body">
                                <div class="activity-item__title">{{ $surat->template->nama_template ?? 'Template dihapus' }}</div>
                                <div class="activity-item__meta">{{ $surat->user->name ?? 'Pengguna dihapus' }} &middot; {{ $surat->created_at->diffForHumans() }}</div>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <p class="activity-search-empty" id="activitySearchEmpty">Nggak ada yang cocok dengan pencarian.</p>
            @else
                <p class="activity-empty">Belum ada aktivitas surat.</p>
            @endif
        </div>
    </div>

@endsection

@section('page-js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite('resources/js/dashboard-interactions.js')
@endsection
