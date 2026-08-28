@extends('layouts.app')

@section('page-title', 'Evaluasi Retensi Arsip')

@section('page-css')
    @vite('resources/css/evaluasi-retensi.css')
@endsection

@section('content')

    @if (session('success'))
        <div class="surat-alert surat-alert--success">{{ session('success') }}</div>
    @endif

    @if ($errors->has('evaluasi'))
        <div class="surat-alert surat-alert--danger">{{ $errors->first('evaluasi') }}</div>
    @endif

    <div class="retensi-header-wrapper">
        <div>
            <h2 class="retensi-header-title">Evaluasi Retensi Arsip</h2>
            <p class="retensi-header-sub">Penilaian nilai guna, kerahasiaan dan kalkulasi otomatis masa simpan arsip.</p>
        </div>

        <form action="{{ route('retensi.jalankan') }}" method="POST">
            @csrf
            <button type="submit" class="btn-futuristic-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                Jalankan Evaluasi
            </button>
        </form>
    </div>

    <div class="retensi-card-expanded">
        <table class="retensi-table-modern">
            <thead>
                <tr>
                    <th style="width: 18%;">Nomor Surat</th>
                    <th style="width: 14%;">Tanggal</th>
                    <th style="width: 10%; text-align: center;">Akses</th>
                    <th style="width: 38%;">Penilaian Nilai Guna & Kerahasiaan</th>
                    <th style="width: 8%; text-align: center;">Skor</th>
                    <th style="width: 12%; text-align: center;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($arsip as $item)
                    @php
                        $eval = $item->evaluasiRetensi;
                        $statusClass = match($eval?->status_rekomendasi) {
                            'Permanen'     => 'badge-retensi--permanen',
                            'Simpan Lokal' => 'badge-retensi--simpan',
                            'Musnah'       => 'badge-retensi--musnah',
                            default        => 'badge-retensi--pending',
                        };
                    @endphp
                    <tr>
                        <td class="col-nomor">
                            <span class="text-nomor">{{ $item->nomor_surat }}</span>
                        </td>
                        <td class="col-tanggal">{{ $item->tanggal_surat->format('d M Y') }}</td>
                        <td style="text-align: center;">
                            <span class="badge-akses-count">{{ $item->jumlah_akses ?? 0 }}x</span>
                        </td>
                        <td>
                            <form action="{{ route('retensi.simpan-penilaian', $item->id) }}" method="POST" class="form-penilaian-inline">
                                @csrf
                                <select name="nilai_guna" class="select-futuristic" required>
                                    <option value="" disabled {{ !$eval?->nilai_guna ? 'selected' : '' }}>-- Nilai Guna --</option>
                                    <option value="Hukum" {{ $eval?->nilai_guna === 'Hukum' ? 'selected' : '' }}>Hukum</option>
                                    <option value="Keuangan" {{ $eval?->nilai_guna === 'Keuangan' ? 'selected' : '' }}>Keuangan</option>
                                    <option value="Operasional" {{ $eval?->nilai_guna === 'Operasional' ? 'selected' : '' }}>Operasional</option>
                                    <option value="Pemberitahuan" {{ $eval?->nilai_guna === 'Pemberitahuan' ? 'selected' : '' }}>Pemberitahuan</option>
                                </select>

                                <select name="kerahasiaan" class="select-futuristic" required>
                                    <option value="" disabled {{ !$eval?->kerahasiaan ? 'selected' : '' }}>-- Kerahasiaan --</option>
                                    <option value="Sangat Rahasia" {{ $eval?->kerahasiaan === 'Sangat Rahasia' ? 'selected' : '' }}>Sangat Rahasia</option>
                                    <option value="Rahasia" {{ $eval?->kerahasiaan === 'Rahasia' ? 'selected' : '' }}>Rahasia</option>
                                    <option value="Biasa/Terbuka" {{ $eval?->kerahasiaan === 'Biasa/Terbuka' ? 'selected' : '' }}>Biasa / Terbuka</option>
                                </select>

                                <button type="submit" class="btn-futuristic-save">Simpan</button>
                            </form>
                        </td>
                        <td style="text-align: center;">
                            <span class="skor-retensi-val">
                                {{ $eval?->skor_total !== null ? number_format($eval->skor_total, 2) : '-' }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge-retensi {{ $statusClass }}">
                                {{ $eval?->status_rekomendasi ?? 'Belum Dievaluasi' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="table-empty-state">
                            Belum ada arsip surat untuk dievaluasi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection
