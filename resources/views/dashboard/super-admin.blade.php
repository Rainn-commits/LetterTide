@extends('layouts.app')

@section('page-title', 'Dashboard - Super Admin')

@section('page-css')
    @vite('resources/css/dashboard.css')
@endsection

@section('content')

    <div class="dash-welcome">
        <h2 class="dash-welcome__title">Halo, {{ auth()->user()->name }} 👋</h2>
        <p class="dash-welcome__subtitle">Ringkasan seluruh aktivitas persuratan di LetterTide.</p>
    </div>

    <div class="stat-grid">
        <div class="stat-card">
            <span class="stat-card__icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 6.5C3 5.67 3.67 5 4.5 5H19.5C20.33 5 21 5.67 21 6.5V17.5C21 18.33 20.33 19 19.5 19H4.5C3.67 19 3 18.33 3 17.5V6.5Z" stroke="currentColor" stroke-width="1.6"/><path d="M4 6.5L12 13L20 6.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <div>
                <div class="stat-card__value">{{ number_format($stats['total_surat']) }}</div>
                <div class="stat-card__label">Total Surat</div>
            </div>
        </div>

        <div class="stat-card">
            <span class="stat-card__icon stat-card__icon--dark">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17 20v-1.5a3.5 3.5 0 00-3.5-3.5h-5A3.5 3.5 0 005 18.5V20" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><circle cx="9.5" cy="8" r="3" stroke="currentColor" stroke-width="1.6"/><path d="M16 8.5a2.5 2.5 0 010 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><path d="M19 20v-1.5a3.3 3.3 0 00-2-3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
            </span>
            <div>
                <div class="stat-card__value">{{ number_format($stats['total_pengguna']) }}</div>
                <div class="stat-card__label">Total Pengguna</div>
            </div>
        </div>

        <div class="stat-card">
            <span class="stat-card__icon stat-card__icon--accent">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="8" r="3.5" stroke="currentColor" stroke-width="1.6"/><path d="M5 20c0-3.3 3.1-6 7-6s7 2.7 7 6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
            </span>
            <div>
                <div class="stat-card__value">{{ number_format($stats['total_role']) }}</div>
                <div class="stat-card__label">Total Role</div>
            </div>
        </div>

        <div class="stat-card">
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

            @if (!empty($trafficData))
                <div class="traffic-chart">
                    @php $max = max($trafficData) ?: 1; @endphp
                    @foreach ($trafficData as $label => $count)
                        @php $heightPercentage = max(6, round(($count / $max) * 100)); @endphp
                        <div class="traffic-chart__col {{ $loop->last ? 'traffic-chart__col--current' : '' }}">
                            <div class="traffic-chart__bar" style="--bar-height: {{ $heightPercentage }}%;"></div>
                            <span class="traffic-chart__label">{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="traffic-chart__empty">
                    Data traffic bulanan belum tersedia. Tambahkan <code>$trafficData</code>
                    di <code>DashboardController::superAdmin()</code> untuk menampilkan grafik ini.
                </p>
            @endif
        </div>

        <div class="card">
            <div class="card__header">
                <h3 class="card__title">Aktivitas Terbaru</h3>
            </div>

            @if ($aktivitasTerbaru->isNotEmpty())
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
            @else
                <p class="activity-empty">Belum ada aktivitas surat.</p>
            @endif
        </div>
    </div>

@endsection
