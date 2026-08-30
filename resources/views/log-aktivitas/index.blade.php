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
