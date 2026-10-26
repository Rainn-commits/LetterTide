@extends('layouts.app')

@section('page-title', 'Evaluasi Retensi Arsip')

@section('page-css')
    @vite('resources/css/evaluasi-retensi.css')
@endsection

@section('content')

    @if (in_array(auth()->user()->role?->nama_role ?? '', ['Super Admin', 'Admin']))
        <div class="retensi-tabs">
            <a href="{{ route('retensi-kriteria.index') }}" class="retensi-tabs__link">
                Kelola Kriteria Retensi
            </a>

            <a href="{{ route('evaluasi-retensi.index') }}" class="retensi-tabs__link is-active">
                Evaluasi Retensi Arsip
            </a>
        </div>
    @endif

    @php
        $isPrivileged = in_array(auth()->user()->role->nama_role ?? '', ['Super Admin', 'Admin']);
        $jumlahKolom = $isPrivileged ? 8 : 6;
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

    <form action="{{ route('evaluasi-retensi.index') }}" method="GET" class="retensi-filter-card">
        <div class="retensi-filter-grid">

            <div class="retensi-filter-group retensi-filter-group--search">
                <label for="q">Cari Surat</label>
                <input
                    type="text"
                    id="q"
                    name="q"
                    value="{{ request('q') }}"
                    class="retensi-filter-control"
                    placeholder="Nomor surat, template, atau pembuat..."
                >
            </div>

            <div class="retensi-filter-group">
                <label for="template_id">Jenis Surat</label>
                <select id="template_id" name="template_id" class="retensi-filter-control">
                    <option value="">Semua Template</option>
                    @foreach ($templates as $template)
                        <option value="{{ $template->id }}"
                            {{ (string) request('template_id') === (string) $template->id ? 'selected' : '' }}>
                            {{ $template->nama_template }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="retensi-filter-group">
                <label for="tanggal_mulai">Tanggal Mulai</label>
                <input
                    type="date"
                    id="tanggal_mulai"
                    name="tanggal_mulai"
                    value="{{ request('tanggal_mulai') }}"
                    class="retensi-filter-control"
                >
            </div>

            <div class="retensi-filter-group">
                <label for="tanggal_selesai">Tanggal Selesai</label>
                <input
                    type="date"
                    id="tanggal_selesai"
                    name="tanggal_selesai"
                    value="{{ request('tanggal_selesai') }}"
                    class="retensi-filter-control"
                >
            </div>

            @if ($isPrivileged)
                <div class="retensi-filter-group">
                    <label for="status_evaluasi">Status Evaluasi</label>
                    <select id="status_evaluasi" name="status_evaluasi" class="retensi-filter-control">
                        <option value="">Semua Status</option>
                        <option value="belum" {{ request('status_evaluasi') === 'belum' ? 'selected' : '' }}>
                            Belum Dievaluasi
                        </option>
                        <option value="menunggu" {{ request('status_evaluasi') === 'menunggu' ? 'selected' : '' }}>
                            Menunggu Validasi
                        </option>
                        <option value="tervalidasi" {{ request('status_evaluasi') === 'tervalidasi' ? 'selected' : '' }}>
                            Tervalidasi
                        </option>
                    </select>
                </div>
            @endif

            <div class="retensi-filter-group">
                <label for="sort">Urutkan</label>
                <select id="sort" name="sort" class="retensi-filter-control">
                    <option value="tanggal_terbaru" {{ $sort === 'tanggal_terbaru' ? 'selected' : '' }}>
                        Tanggal Terbaru
                    </option>
                    <option value="tanggal_terlama" {{ $sort === 'tanggal_terlama' ? 'selected' : '' }}>
                        Tanggal Terlama
                    </option>

                    @if ($isPrivileged)
                        <option value="skor_tertinggi" {{ $sort === 'skor_tertinggi' ? 'selected' : '' }}>
                            Skor Tertinggi
                        </option>
                        <option value="skor_terendah" {{ $sort === 'skor_terendah' ? 'selected' : '' }}>
                            Skor Terendah
                        </option>
                    @endif

                    <option value="akses_terbanyak" {{ $sort === 'akses_terbanyak' ? 'selected' : '' }}>
                        Akses Terbanyak
                    </option>
                </select>
            </div>

        </div>

        <div class="retensi-filter-actions">
            <button type="submit" class="retensi-filter-btn retensi-filter-btn--apply">
                Terapkan Filter
            </button>

            <a href="{{ route('evaluasi-retensi.index') }}" class="retensi-filter-btn retensi-filter-btn--reset">
                Reset
            </a>
        </div>
    </form>

    <div class="retensi-card-expanded">
        <table class="retensi-table-modern {{ $isPrivileged ? 'retensi-table--admin' : 'retensi-table--user' }}">
            <thead>
                <tr>
                    <th>Nomor Surat</th>
                    @if ($isPrivileged)
                        <th>Pembuat</th>
                    @endif
                    <th>Tanggal</th>
                    <th>Akses</th>
                    <th>File Surat</th>
                    <th>Nilai Guna &amp; Kerahasiaan</th>
                    @if ($isPrivileged)
                        <th>Skor</th>
                    @endif
                    <th>Status</th>
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

                        <td class="col-tanggal">{{ $item->tanggal_surat->format('d M Y') }}</td>

                        <td style="text-align: center;">
                            <span class="badge-akses-count">{{ $item->jumlah_akses ?? 0 }}x</span>
                        </td>

                        <td class="retensi-file-cell">
                            @if ($item->file_arsip)
                            <a href="{{ asset('storage/' . $item->file_arsip) }}"
                                target="_blank"
                                class="file-badge-link">
                                    <svg width="14" height="14" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                        <line x1="16" y1="13" x2="8" y2="13"/>
                                        <line x1="16" y1="17" x2="8" y2="17"/>
                                        <line x1="10" y1="9" x2="8" y2="9"/>
                                    </svg>
                                    Lihat PDF
                                </a>
                            @else
                                <span class="badge-empty">Belum ada</span>
                            @endif
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

                                <button type="submit" class="btn-futuristic-save" aria-label="Simpan penilaian">✓</button>
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
        @if ($arsip->hasPages())
        <div class="retensi-pagination-wrapper">
            {{ $arsip->links() }}
        </div>
    @endif

@endsection
