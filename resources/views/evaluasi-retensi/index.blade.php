@extends('layouts.app')

@section('page-title', 'Evaluasi Retensi Arsip')

@section('page-css')
    @vite('resources/css/evaluasi-retensi.css')
@endsection

@section('content')

    @php
        $isPrivileged = in_array(auth()->user()->role->nama_role ?? '', ['Super Admin', 'Admin']);
        $jumlahKolom = $isPrivileged ? 7 : 5;
    @endphp

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
    </div>

    <div class="retensi-card-expanded">
        <table class="retensi-table-modern" style="width: 100%; table-layout: fixed;">
            <thead>
                <tr>
                    <th style="width: 20%;">Nomor Surat</th>

                    @if ($isPrivileged)
                        <th style="width: 12%;">Pembuat</th>
                    @endif

                    <th style="width: 11%;">Tanggal</th>
                    <th style="width: 6%; text-align: center;">Akses</th>
                    <th style="width: 33%;">Nilai Guna & Kerahasiaan</th>

                    @if ($isPrivileged)
                        <th style="width: 6%; text-align: center;">Skor</th>
                    @endif

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
                            <span class="text-nomor" style="word-break: break-all;">{{ $item->nomor_surat }}</span>
                        </td>

                        @if ($isPrivileged)
                            <td style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $item->user->name ?? $item->pembuat->name ?? '-' }}
                            </td>
                        @endif

                        <td class="col-tanggal" style="white-space: nowrap;">{{ $item->tanggal_surat->format('d M Y') }}</td>
                        <td style="text-align: center;">
                            <span class="badge-akses-count">{{ $item->jumlah_akses ?? 0 }}x</span>
                        </td>
                        <td>
                            <form action="{{ route('retensi.simpan-penilaian', $item->id) }}" method="POST" class="form-penilaian-inline" style="display: flex; gap: 4px;">
                                <select name="nilai_guna" class="select-futuristic" style="padding: 4px 6px; font-size: 12px; width: 42%;" required>
                                    <option value="" disabled {{ !$eval?->nilai_guna ? 'selected' : '' }}>-- Nilai Guna --</option>
                                    <option value="Hukum" {{ $eval?->nilai_guna === 'Hukum' ? 'selected' : '' }}>Hukum</option>
                                    <option value="Keuangan" {{ $eval?->nilai_guna === 'Keuangan' ? 'selected' : '' }}>Keuangan</option>
                                    <option value="Operasional" {{ $eval?->nilai_guna === 'Operasional' ? 'selected' : '' }}>Operasional</option>
                                    <option value="Pemberitahuan" {{ $eval?->nilai_guna === 'Pemberitahuan' ? 'selected' : '' }}>Pemberitahuan</option>
                                </select>

                                <select name="kerahasiaan" class="select-futuristic" style="padding: 4px 6px; font-size: 12px; width: 42%;" required>
                                    <option value="" disabled {{ !$eval?->kerahasiaan ? 'selected' : '' }}>-- Kerahasiaan --</option>
                                    <option value="Sangat Rahasia" {{ $eval?->kerahasiaan === 'Sangat Rahasia' ? 'selected' : '' }}>Sangat Rahasia</option>
                                    <option value="Rahasia" {{ $eval?->kerahasiaan === 'Rahasia' ? 'selected' : '' }}>Rahasia</option>
                                    <option value="Biasa/Terbuka" {{ $eval?->kerahasiaan === 'Biasa/Terbuka' ? 'selected' : '' }}>Biasa / Terbuka</option>
                                </select>

                                <button type="submit" class="btn-futuristic-save" style="padding: 4px 8px; font-size: 12px;">✓</button>
                            </form>
                        </td>

                        @if ($isPrivileged)
                            <td style="text-align: center;">
                                <span class="skor-retensi-val">
                                    {{ $eval?->skor_total !== null ? number_format($eval->skor_total, 2) : '-' }}
                                </span>
                            </td>
                        @endif

                        <td style="text-align: center;">
                            @if ($isPrivileged)
                                <div class="retensi-status-stack">
                                    <span class="badge-retensi {{ $statusClass }}">
                                        {{ $eval?->status_rekomendasi ?? 'Belum Dievaluasi' }}
                                    </span>

                                    @if ($eval?->status_rekomendasi)
                                        @if ($eval->status_persetujuan === 'Disetujui')
                                            <div class="badge-persetujuan badge-persetujuan--disetujui">✓ Tervalidasi</div>
                                        @else
                                            <div class="badge-persetujuan badge-persetujuan--menunggu">Menunggu Validasi</div>

                                            <form action="{{ route('retensi.validasi', $item->id) }}" method="POST" data-confirm="Validasi status retensi surat ini?" data-confirm-label="Ya, Validasi">
                                                @csrf
                                                <button type="submit" class="btn-validasi-retensi">Validasi</button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            @else
                                @if (!$eval?->status_rekomendasi)
                                    <span class="badge-retensi badge-retensi--pending" style="white-space: nowrap;">Belum Dievaluasi</span>
                                @elseif ($eval->status_persetujuan === 'Disetujui')
                                    <span class="badge-persetujuan badge-persetujuan--disetujui">✓ Tervalidasi</span>
                                @else
                                    <span class="badge-persetujuan badge-persetujuan--menunggu">Menunggu Validasi</span>
                                @endif
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $jumlahKolom }}" class="table-empty-state">
                            Belum ada arsip surat untuk dievaluasi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection
