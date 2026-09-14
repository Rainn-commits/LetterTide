@extends('layouts.app')

@section('page-title', 'Log Aktivitas Sistem')

@section('page-css')
    @vite('resources/css/log-aktivitas.css')
@endsection

@section('content')
<div class="log-page">
    <h2 class="log-page__title">Log Aktivitas Sistem</h2>

    <form method="GET" action="{{ route('log-aktivitas.index') }}" class="log-filter-card">
        <div class="log-filter-grid">

            <div class="log-filter-group log-filter-group--search">
                <label for="log-search">Cari Aktivitas</label>
                <input
                    type="text"
                    id="log-search"
                    name="q"
                    class="log-filter-control"
                    value="{{ request('q') }}"
                    placeholder="Pengguna, aksi, modul, atau keterangan..."
                >
            </div>

            <div class="log-filter-group">
                <label for="log-aksi">Aksi</label>
                <select id="log-aksi" name="aksi" class="log-filter-control">
                    <option value="">Semua Aksi</option>

                    @foreach ($aksiList as $aksi)
                        <option value="{{ $aksi }}" {{ request('aksi') === $aksi ? 'selected' : '' }}>
                            {{ $aksi }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="log-filter-group">
                <label for="log-modul">Modul</label>
                <select id="log-modul" name="modul" class="log-filter-control">
                    <option value="">Semua Modul</option>

                    @foreach ($modulList as $modul)
                        <option value="{{ $modul }}" {{ request('modul') === $modul ? 'selected' : '' }}>
                            {{ $modul }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="log-filter-group">
                <label for="log-tanggal-mulai">Tanggal Mulai</label>
                <input
                    type="date"
                    id="log-tanggal-mulai"
                    name="tanggal_mulai"
                    class="log-filter-control"
                    value="{{ request('tanggal_mulai') }}"
                >
            </div>

            <div class="log-filter-group">
                <label for="log-tanggal-selesai">Tanggal Selesai</label>
                <input
                    type="date"
                    id="log-tanggal-selesai"
                    name="tanggal_selesai"
                    class="log-filter-control"
                    value="{{ request('tanggal_selesai') }}"
                >
            </div>

            <div class="log-filter-group">
                <label for="log-sort">Urutkan</label>
                <select id="log-sort" name="sort" class="log-filter-control">
                    <option value="terbaru" {{ request('sort', 'terbaru') === 'terbaru' ? 'selected' : '' }}>
                        Terbaru
                    </option>
                    <option value="terlama" {{ request('sort') === 'terlama' ? 'selected' : '' }}>
                        Terlama
                    </option>
                </select>
            </div>

        </div>

        <div class="log-filter-actions">
            <button type="submit" class="log-filter-btn log-filter-btn--apply">
                Terapkan Filter
            </button>

            <a href="{{ route('log-aktivitas.index') }}" class="log-filter-btn log-filter-btn--reset">
                Reset
            </a>
        </div>
    </form>

    @if($logs->isEmpty())

        <div class="log-empty">Belum ada aktivitas yang tercatat.</div>
    @else
        <div class="log-table-container">
            <table class="log-table">
                <thead>
                    <tr>
                        <th class="text-left">WAKTU</th>
                        <th class="text-center">PENGGUNA</th>
                        <th class="text-center">AKSI</th>
                        <th class="text-center">MODUL</th>
                        <th class="text-left">KETERANGAN</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        @php
                            $badgeClass = match($log->aksi) {
                            'ACC', 'Acc', 'acc' => 'log-badge--acc',
                            'Mengubah'          => 'log-badge--update',
                            'Membuat'           => 'log-badge--create',
                            'Menghapus',
                            'Menonaktifkan'     => 'log-badge--delete',
                            'Mengaktifkan'      => 'log-badge--activate',
                            default             => 'log-badge--default',
                        };
                        @endphp
                        <tr>
                            <td class="text-left font-medium">{{ $log->created_at->format('d M Y, H:i') }}</td>
                            <td class="text-center font-bold">{{ $log->user->name ?? '(pengguna dihapus)' }}</td>
                            <td class="text-center">
                                <span class="log-badge {{ $badgeClass }}">{{ $log->aksi }}</span>
                            </td>
                            <td class="text-center font-medium">{{ $log->modul }}</td>
                            <td class="text-left">{{ $log->keterangan }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="log-pagination">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection
