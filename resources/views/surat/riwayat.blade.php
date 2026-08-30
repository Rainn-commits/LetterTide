@extends('layouts.app')

@section('page-title', 'Riwayat Surat Saya')

@section('page-css')
    @vite('resources/css/surat.css')
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
                                <a href="{{ route('surat.show', $surat) }}" class="btn-modern btn-modern--detail">Lihat</a>
                                <a href="{{ route('surat.edit', $surat) }}" class="btn-modern btn-modern--upload">Edit</a>
                                <a href="{{ route('surat.cetak', $surat) }}" target="_blank" class="btn-modern btn-modern--cetak">Cetak</a>

                                <form action="{{ route('surat.destroy', $surat) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus surat ini? Data yang dihapus tidak bisa dikembalikan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-modern btn-modern--delete" title="Hapus Surat">Hapus</button>
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
