@extends('layouts.app')

@section('page-title', 'Riwayat Surat Saya')

@section('page-css')
    @vite('resources/css/surat-riwayat.css')
@endsection

@section('content')

    <div class="surat-tabs">
        <a href="{{ route('surat.create') }}" class="surat-tabs__link">+ Buat Surat Baru</a>
        <a href="{{ route('surat.riwayat') }}" class="surat-tabs__link is-active">Riwayat Surat Saya</a>
    </div>

    @if (session('success'))
        <div class="surat-alert surat-alert--success">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('surat.riwayat') }}" class="card filter-card">
        <div class="filter-grid">
            <div class="form-group-center">
                <label>Pencarian</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Nomor Surat / Template..." class="form-control">
            </div>

            <div class="form-group-center">
                <label>Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="form-control">
            </div>

            <div class="form-group-center">
                <label>Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}" class="form-control">
            </div>
        </div>

        <div class="filter-actions">
            <a href="{{ route('surat.riwayat') }}" class="btn-ghost">Reset</a>
            <button type="submit" class="btn-dark">Cari</button>
        </div>
    </form>

    <div class="card card--table-expanded">
        <table class="template-table-modern">
            <thead>
                <tr>
                    <th style="width: 16%;">Nomor Surat</th>
                    <th style="width: 22%;">Template</th>
                    <th style="width: 10%;">Tanggal</th>
                    <th style="width: 11%;">Status</th>
                    <th style="width: 11%;">File Arsip</th>
                    <th style="width: 30%; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($suratList as $surat)
                    <tr>
                        <td class="col-nomor">
                            <strong>{{ $surat->nomor_surat }}</strong>
                        </td>
                        <td class="col-template">
                            {{ $surat->template->nama_template ?? 'Template Dihapus' }}
                        </td>
                        <td>{{ $surat->tanggal_surat->format('d/m/Y') }}</td>
                        <td>
                            @if (strtolower($surat->status ?? '') === 'disetujui')
                                <span class="badge-status badge-status--approved">Disetujui</span>
                            @else
                                <span class="badge-status badge-status--pending">Menunggu TTD</span>
                            @endif
                        </td>
                        <td>
                            @if ($surat->file_arsip)
                                <a href="{{ asset('storage/' . $surat->file_arsip) }}" target="_blank" class="file-badge-link">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/></svg>
                                    Lihat PDF
                                </a>
                            @else
                                <span class="badge-empty">Belum ada</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons-group-expanded">
                                <a href="{{ route('surat.show', $surat) }}" class="btn-modern btn-modern--detail btn-modern--icon-only" title="Lihat Detail">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                                <a href="{{ route('surat.edit', $surat) }}" class="btn-modern btn-modern--upload btn-modern--icon-only" title="Edit Isi Surat">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </a>
                                <a href="{{ route('surat.cetak', $surat) }}" target="_blank" class="btn-modern btn-modern--cetak btn-modern--icon-only" title="Cetak Surat">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                                </a>

                                <form action="{{ route('surat.destroy', $surat) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus surat ini? Data yang dihapus tidak bisa dikembalikan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-modern btn-modern--delete btn-modern--icon-only" title="Hapus Surat">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="table-empty-state">
                            Kamu belum pernah membuat surat. Klik "Buat Surat Baru" di atas untuk mulai.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($suratList->hasPages())
        <div class="pagination-wrapper">
            {{ $suratList->links() }}
        </div>
    @endif

@endsection

@section('page-js')
    @vite('resources/js/surat-wizard.js')
@endsection
