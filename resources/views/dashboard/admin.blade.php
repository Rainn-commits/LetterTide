@extends('layouts.app')

@section('page-title', 'Dashboard - Admin')

@section('page-css')
    @vite('resources/css/dashboard.css')
@endsection

@section('content')
    <div class="dash-pro-grid">
        <div class="dash-pro-main">
            <div class="stat-grid-pro stat-grid-pro--cols-3">
                <a href="{{ Route::has('arsip.index') ? route('arsip.index') : '#' }}" class="glass-card stat-card-pro stat-card-pro--teal">
                    <div class="stat-card-pro__top">
                        <span class="stat-card-pro__label">Total Surat</span>
                        <span class="skeuo-icon skeuo-icon--teal">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </span>
                    </div>
                    <div class="stat-card-pro__value">{{ number_format($stats['total_surat']) }}</div>
                </a>

                <div class="glass-card stat-card-pro stat-card-pro--blue stat-card-pro--static">
                    <div class="stat-card-pro__top">
                        <span class="stat-card-pro__label">Surat Bulan Ini</span>
                        <span class="skeuo-icon skeuo-icon--blue">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        </span>
                    </div>
                    <div class="stat-card-pro__value">{{ number_format($stats['surat_bulan_ini']) }}</div>
                </div>

                <a href="{{ Route::has('pengguna.index') ? route('pengguna.index') : url('/pengguna') }}" class="glass-card stat-card-pro stat-card-pro--red">
                    <div class="stat-card-pro__top">
                        <span class="stat-card-pro__label">Total Pengguna</span>
                        <span class="skeuo-icon skeuo-icon--red">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </span>
                    </div>
                    <div class="stat-card-pro__value">{{ number_format($stats['total_pengguna']) }}</div>
                </a>
            </div>

            <div class="glass-card card-pro">
                <div class="card-pro__header">
                    <div>
                        <h3 class="card-pro__title">Traffic Surat</h3>
                        <p class="card-pro__subtitle">Statistik jumlah pembuatan surat 6 bulan terakhir</p>
                    </div>
                    <span class="glass-pill glass-pill--accent">Realtime Data</span>
                </div>

                @if (!empty($trafficData))
                    @php
                        $monthMap = [
                            'Jan' => 'Jan', 'Feb' => 'Feb', 'Mar' => 'Mar',
                            'Apr' => 'Apr', 'Mei' => 'May', 'May' => 'May',
                            'Jun' => 'Jun', 'Jul' => 'Jul', 'Agt' => 'Aug',
                            'Aug' => 'Aug', 'Sep' => 'Sep', 'Okt' => 'Oct',
                            'Oct' => 'Oct', 'Nov' => 'Nov', 'Des' => 'Dec', 'Dec' => 'Dec'
                        ];
                        $max = max($trafficData) ?: 1;
                    @endphp

                    <div class="traffic-chart-container">
                        <div class="chart-gridlines">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>

                        <div class="traffic-chart-pro">
                            @foreach ($trafficData as $label => $count)
                                @php
                                    $shortMonth = $monthMap[$label] ?? $label;
                                    $heightPercentage = $count > 0 ? max(12, round(($count / $max) * 100)) : 0;
                                @endphp
                                <div class="traffic-chart-pro__col {{ $loop->last ? 'traffic-chart-pro__col--current' : '' }}">
                                    <div class="traffic-chart-pro__bar-wrapper">
                                        @if($count > 0)
                                            <span class="traffic-chart-pro__badge">{{ $count }}</span>
                                        @endif
                                        <div class="skeuo-bar {{ $count == 0 ? 'skeuo-bar--empty' : '' }}" style="--bar-height: {{ $heightPercentage }}%;"></div>
                                    </div>
                                    <span class="traffic-chart-pro__label">{{ $shortMonth }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <p class="traffic-chart__empty">Belum ada traffic surat dalam 6 bulan terakhir.</p>
                @endif
            </div>
        </div>

        <div class="dash-pro-sidebar">
            <div class="glass-card card-pro card-pro--full-height">
                <div class="card-pro__header">
                    <div>
                        <h3 class="card-pro__title">Aktivitas Terbaru</h3>
                    </div>
                </div>

                @if ($aktivitasTerbaru->isNotEmpty())
                    <div class="dash-search-pro skeuo-input">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input type="text" id="activitySearch" placeholder="Cari aktivitas...">
                    </div>

                    <ul class="activity-feed">
                        @foreach ($aktivitasTerbaru as $surat)
                            <li class="activity-feed__item">
                                <div class="activity-feed__icon-wrapper">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                </div>
                                <div class="activity-feed__content">
                                    <div class="activity-feed__title">{{ $surat->template->nama_template ?? 'Template Dihapus' }}</div>
                                    <div class="activity-feed__meta">
                                        <strong class="activity-feed__user">{{ $surat->user->name ?? 'Pengguna' }}</strong>
                                        <span class="activity-feed__time">&bull; {{ $surat->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <p class="activity-search-empty" id="activitySearchEmpty" style="display: none;">Tidak ada data yang cocok.</p>
                @else
                    <p class="activity-empty">Belum ada aktivitas surat.</p>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('page-js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite('resources/js/dashboard-interactions.js')
@endsection
