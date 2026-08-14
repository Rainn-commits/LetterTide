@extends('layouts.app')

@section('page-title', 'Dashboard - User')

@section('page-css')
    @vite('resources/css/dashboard.css')
@endsection

@section('content')

    <div class="dash-welcome">
        <h2 class="dash-welcome__title">Halo, {{ auth()->user()->name }} 👋</h2>
        <p class="dash-welcome__subtitle">Ini ringkasan surat kamu di LetterTide.</p>
    </div>

    <div class="quick-cta">
        <div>
            <p class="quick-cta__title">Butuh bikin surat baru?</p>
            <p class="quick-cta__subtitle">Pilih template, isi datanya, langsung jadi.</p>
        </div>
        <a href="{{ route('surat.create') }}" class="quick-cta__btn">Buat Surat Baru</a>
    </div>

    <div class="stat-grid">
        <div class="stat-card">
            <span class="stat-card__icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 6.5C3 5.67 3.67 5 4.5 5H19.5C20.33 5 21 5.67 21 6.5V17.5C21 18.33 20.33 19 19.5 19H4.5C3.67 19 3 18.33 3 17.5V6.5Z" stroke="currentColor" stroke-width="1.6"/><path d="M4 6.5L12 13L20 6.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <div>
                <div class="stat-card__value">{{ number_format($stats['surat_saya']) }}</div>
                <div class="stat-card__label">Surat Saya</div>
            </div>
        </div>
    </div>

    <div class="dash-columns">
        <div class="card">
            <div class="card__header">
                <h3 class="card__title">Surat Saya per Bulan</h3>
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
                    di <code>DashboardController::user()</code> untuk menampilkan grafik ini.
                </p>
            @endif
        </div>

        <div class="card">
            <div class="card__header">
                <h3 class="card__title">Riwayat Terakhir</h3>
            </div>

            @if ($riwayatTerakhir->isNotEmpty())
                <ul class="activity-list">
                    @foreach ($riwayatTerakhir as $surat)
                        <li class="activity-item">
                            <span class="activity-item__icon">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 6.5C3 5.67 3.67 5 4.5 5H19.5C20.33 5 21 5.67 21 6.5V17.5C21 18.33 20.33 19 19.5 19H4.5C3.67 19 3 18.33 3 17.5V6.5Z" stroke="currentColor" stroke-width="1.6"/><path d="M4 6.5L12 13L20 6.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                            <div class="activity-item__body">
                                <div class="activity-item__title">{{ $surat->template->nama_template ?? 'Template dihapus' }}</div>
                                <div class="activity-item__meta">{{ $surat->created_at->diffForHumans() }}</div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="activity-empty">Kamu belum pernah bikin surat. Yuk mulai dari tombol di atas.</p>
            @endif
        </div>
    </div>

@endsection
