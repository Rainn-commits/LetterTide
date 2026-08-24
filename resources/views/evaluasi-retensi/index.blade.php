@extends('layouts.app')

@section('page-title', 'Evaluasi Retensi Arsip')

@section('page-css')
    @vite('resources/css/evaluasi-retensi.css')
@endsection

@section('content')
<div class="er-page">
    <div class="er-page__header">
        <h2 class="er-page__title">Evaluasi Retensi Arsip</h2>
        <form method="POST" action="{{ route('evaluasi-retensi.jalankan') }}">
            @csrf
            <button type="submit" class="er-btn-jalankan">Jalankan Evaluasi</button>
        </form>
    </div>

    @if(session('success'))
        <div class="er-alert er-alert--success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="er-alert er-alert--error">{{ $errors->first() }}</div>
    @endif

    <table class="er-table">
        <thead>
            <tr>
                <th>Nomor Surat</th>
                <th>Tanggal</th>
                <th>Akses</th>
                <th>Nilai Guna</th>
                <th>Kerahasiaan</th>
                <th>Skor</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($arsip as $surat)
                @php $evaluasi = $surat->evaluasiRetensi; @endphp
                <tr>
                    <td>{{ $surat->nomor_surat }}</td>
                    <td>{{ \Carbon\Carbon::parse($surat->tanggal_surat)->format('d M Y') }}</td>
                    <td>{{ $surat->jumlah_akses }}x</td>

                    <td colspan="2">
                        <form class="er-mini-form" method="POST" action="{{ route('evaluasi-retensi.nilai', $surat) }}">
                            @csrf
                            <select name="nilai_guna" required>
                                <option value="">Nilai Guna</option>
                                @foreach(['Hukum','Keuangan','Operasional','Pemberitahuan'] as $opsi)
                                    <option value="{{ $opsi }}" {{ $evaluasi?->nilai_guna === $opsi ? 'selected' : '' }}>{{ $opsi }}</option>
                                @endforeach
                            </select>
                            <select name="kerahasiaan" required>
                                <option value="">Kerahasiaan</option>
                                @foreach(['Sangat Rahasia','Rahasia','Biasa/Terbuka'] as $opsi)
                                    <option value="{{ $opsi }}" {{ $evaluasi?->kerahasiaan === $opsi ? 'selected' : '' }}>{{ $opsi }}</option>
                                @endforeach
                            </select>
                            <button type="submit">Simpan</button>
                        </form>
                    </td>

                    <td>{{ $evaluasi?->skor_total ? round($evaluasi->skor_total, 3) : '-' }}</td>

                    <td>
                        @php
                            $status = $evaluasi?->status_rekomendasi;
                            $statusClass = $status ? str_replace(' ', '.', $status) : 'belum';
                        @endphp
                        <span class="er-status er-status--{{ $statusClass }}">
                            {{ $status ?? 'Belum dievaluasi' }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
