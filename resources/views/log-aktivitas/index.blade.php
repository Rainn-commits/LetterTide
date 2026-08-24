@extends('layouts.app')

@section('page-title', 'Log Aktivitas Sistem')

@section('page-css')
    @vite('resources/css/log-aktivitas.css')
@endsection

@section('content')
<div class="log-page">
    <h2 class="log-page__title">Log Aktivitas Sistem</h2>

    @if($logs->isEmpty())
        <div class="log-empty">Belum ada aktivitas yang tercatat.</div>
    @else
        <table class="log-table">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Pengguna</th>
                    <th>Aksi</th>
                    <th>Modul</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                    @php
                        $badgeClass = match($log->aksi) {
                            'Membuat'          => 'log-badge--create',
                            'Mengubah'         => 'log-badge--update',
                            'Menghapus',
                            'Menonaktifkan'    => 'log-badge--delete',
                            default            => 'log-badge--default',
                        };
                    @endphp
                    <tr>
                        <td>{{ $log->created_at->format('d M Y, H:i') }}</td>
                        <td>{{ $log->user->name ?? '(pengguna dihapus)' }}</td>
                        <td><span class="log-badge {{ $badgeClass }}">{{ $log->aksi }}</span></td>
                        <td>{{ $log->modul }}</td>
                        <td>{{ $log->keterangan }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="log-pagination">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection
