@extends('layouts.app')

@section('page-title', 'Arsip Surat')

@section('page-css')
    @vite('resources/css/surat.css')
@endsection

@section('content')
    @if (session('success'))
        <div class="surat-alert surat-alert--success">{{ session('success') }}</div>
    @endif

    <div class="arsip-header-wrapper">
        <h2 class="arsip-header-title">Arsip Surat</h2>
        <form action="#" method="POST" enctype="multipart/form-data" id="formUploadSuratMasuk">
            @csrf
            <input type="file" name="file_surat_masuk" accept=".pdf,.doc,.docx" id="inputSuratMasuk" class="hide-file-input" onchange="alert('File terpilih: ' + this.files[0].name)">
            <button type="button" class="btn-modern-upload-masuk" onclick="document.getElementById('inputSuratMasuk').click()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="17 8 12 3 7 8"/>
                    <line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
                Upload Surat Masuk
            </button>
        </form>
    </div>

    <form method="GET" action="{{ route('arsip.index') }}" class="card filter-card">
        <div class="filter-grid">
            @if (Auth::user()->hasAnyRole(['Super Admin', 'Admin']))
                <div class="form-group-center">
                    <label>Cakupan Surat</label>
                    <select name="pembuat" class="form-control">
                        <option value="semua" {{ request('pembuat') === 'semua' ? 'selected' : '' }}>Semua Surat</option>
                        <option value="saya" {{ request('pembuat') === 'saya' ? 'selected' : '' }}>Surat Buatan Saya</option>
                    </select>
                </div>
            @endif

            <div class="form-group-center">
                <label>Pencarian</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Nomor/Template/Pembuat..." class="form-control">
            </div>

            <div class="form-group-center">
                <label>Jenis Surat</label>
                <select name="jenis" class="form-control">
                    <option value="">-- Semua Jenis --</option>
                    <option value="Keluar" {{ request('jenis') === 'Keluar' ? 'selected' : '' }}>Keluar</option>
                    <option value="Masuk" {{ request('jenis') === 'Masuk' ? 'selected' : '' }}>Masuk</option>
                </select>
            </div>

            <div class="form-group-center">
                <label>Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="form-control">
            </div>

            <div class="form-group-center">
                <label>Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}" class="form-control">
            </div>

            <div class="form-group-center">
                <label>Status File</label>
                <select name="status_arsip" class="form-control">
                    <option value="">-- Semua --</option>
                    <option value="uploaded" {{ request('status_arsip') === 'uploaded' ? 'selected' : '' }}>Ada File</option>
                    <option value="pending" {{ request('status_arsip') === 'pending' ? 'selected' : '' }}>Belum Upload</option>
                </select>
            </div>
        </div>

        <div class="filter-actions">
            <a href="{{ route('arsip.index') }}" class="btn-ghost">Reset Filter</a>
            <button type="submit" class="btn-dark">Cari & Filter</button>
        </div>
    </form>

    <div class="card card--table-expanded">
        <table class="template-table-modern">
            <thead>
                <tr>
                    <th style="width: 16%;">Nomor Surat</th>
                    <th style="width: 20%;">Template</th>
                    <th style="width: 12%;">Pembuat</th>
                    <th style="width: 10%;">Tanggal</th>
                    <th style="width: 10%;">File Arsip</th>
                    <th style="width: 32%; text-align: center;">Aksi</th>
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
                        <td>
                            @php
                                $roleName = $surat->user?->role?->nama_role ?? 'User';

                                $badgeClass = match (strtolower($roleName)) {
                                    'super admin' => 'badge-role badge-role--super-admin',
                                    'admin' => 'badge-role badge-role--admin',
                                    default => 'badge-role badge-role--user',
                                };
                            @endphp

                            <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <span style="font-weight: 600; font-size: 13px; color: #334155;">
                                    {{ $surat->user->name ?? '-' }}
                                </span>

                                <span class="{{ $badgeClass }}">
                                    {{ $roleName }}
                                </span>
                            </div>
                        </td>
                        <td>{{ $surat->tanggal_surat->format('d/m/Y') }}</td>
                        <td>
                            @if ($surat->file_arsip)
                                <a href="{{ asset('storage/' . $surat->file_arsip) }}" target="_blank" class="file-badge-link">
                                    📄 Lihat File
                                </a>
                            @else
                                <span class="badge-empty">Belum ada</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons-group-expanded">
                                <a href="{{ route('surat.show', $surat) }}" class="btn-modern btn-modern--detail">Detail</a>
                                <a href="{{ route('surat.cetak', $surat) }}" target="_blank" class="btn-modern btn-modern--cetak">Cetak</a>

                                @if (strtolower($surat->status ?? '') === 'disetujui' || $surat->file_arsip)
                                    <span class="btn-modern--approved">✓ Sudah TTD</span>
                                @else
                                    <form action="{{ route('surat.ttd-digital', $surat) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui dan menandatangani surat ini secara digital?')">
                                        @csrf
                                        <button type="submit" class="btn-modern btn-modern--ttd">ACC & TTD</button>
                                    </form>
                                @endif

                                <form action="{{ route('surat.upload-arsip', $surat) }}" method="POST" enctype="multipart/form-data" class="upload-inline-form">
                                    @csrf
                                    <input type="file" name="file_arsip" required id="file-{{ $surat->id }}" class="hide-file-input" onchange="this.form.submit()">
                                    <button type="button" onclick="document.getElementById('file-{{ $surat->id }}').click()" class="btn-modern btn-modern--upload">
                                        {{ $surat->file_arsip ? 'Ganti File' : 'Upload File' }}
                                    </button>
                                </form>

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
                            Tidak ada data arsip surat yang ditemukan.
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