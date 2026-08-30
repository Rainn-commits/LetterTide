@extends('layouts.app')

@section('page-title', 'Dashboard - User')

@section('page-css')
    @vite('resources/css/dashboard.css')
@endsection

@section('content')
    <div class="dash-top-row">
        <div class="quick-cta">
            <div>
                <p class="quick-cta__title">Butuh bikin surat baru?</p>
                <p class="quick-cta__subtitle">Pilih template, isi datanya dan langsung jadi.</p>
            </div>
            <a href="{{ route('surat.create') }}" class="quick-cta__btn">Buat Surat Baru</a>
        </div>

        <div class="stat-grid-pro stat-grid-pro--cols-1">
            <a href="{{ Route::has('surat.riwayat') ? route('surat.riwayat') : '#' }}" class="glass-card stat-card-pro stat-card-pro--teal">
                <div class="stat-card-pro__top">
                    <span class="stat-card-pro__label">Surat Saya</span>
                    <span class="skeuo-icon skeuo-icon--teal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </span>
                </div>
                <div class="stat-card-pro__value">{{ number_format($stats['surat_saya'] ?? 0) }}</div>
            </a>
        </div>
    </div>

    <div class="dash-pro-grid">
        <div class="dash-pro-main">
            <div class="glass-card card-pro">
                <div class="card-pro__header">
                    <div>
                        <h3 class="card-pro__title">Surat Saya per Bulan</h3>
                        <p class="card-pro__subtitle">Statistik jumlah surat yang kamu buat 6 bulan terakhir</p>
                    </div>
                    <span class="glass-pill glass-pill--accent">Realtime Data</span>
                </div>

                @if (isset($trafficData) && is_array($trafficData) && count($trafficData) > 0 && array_sum($trafficData) > 0)
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
                    <p class="traffic-chart__empty">
                        Kamu belum pernah membuat surat. Grafik traffic akan muncul setelah ada surat yang dibuat.
                    </p>
                @endif
            </div>
        </div>

        <div class="dash-pro-sidebar">
            <div class="glass-card card-pro card-pro--full-height">
                <div class="card-pro__header">
                    <div>
                        <h3 class="card-pro__title">Riwayat Terakhir</h3>
                    </div>
                </div>

                @if (isset($riwayatTerakhir) && $riwayatTerakhir->isNotEmpty())
                    <ul class="activity-feed">
                        @foreach ($riwayatTerakhir as $surat)
                            <li class="activity-feed__item">
                                <div class="activity-feed__icon-wrapper">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                </div>
                                <div class="activity-feed__content">
                                    <div class="activity-feed__title">{{ $surat->template->nama_template ?? 'Template dihapus' }}</div>
                                    <div class="activity-feed__meta">
                                        <span class="activity-feed__time">{{ optional($surat->created_at)->diffForHumans() ?? '-' }}</span>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="activity-empty">Kamu belum pernah bikin surat. Yuk mulai dari tombol di atas.</p>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('page-js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite('resources/js/dashboard-interactions.js')
@endsection
