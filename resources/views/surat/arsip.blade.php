@extends('layouts.app')

@section('page-title', 'Arsip Surat')

@section('page-css')
    @vite('resources/css/surat.css')
@endsection

@section('content')

    @if (session('success'))
        <div class="surat-alert surat-alert--success">{{ session('success') }}</div>
    @endif

    <div class="wizard-step">
        <h2 class="wizard-step__title">Arsip Surat</h2>
    </div>

    <div class="arsip-header-action">
        <form action="#" method="POST" enctype="multipart/form-data" id="formUploadSuratMasuk">
            @csrf
            <input type="file" name="file_surat_masuk" accept=".pdf,.doc,.docx" id="inputSuratMasuk" class="hide-file-input" onchange="alert('File terpilih: ' + this.files[0].name)">
            
            <button type="button" class="btn-upload-masuk" onclick="document.getElementById('inputSuratMasuk').click()">
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

    <div class="card card--table">
        <table class="template-table">
            <thead>
                <tr>
                    <th>Nomor Surat</th>
                    <th>Template</th>
                    <th>Pembuat</th>
                    <th>Tanggal</th>
                    <th>File Arsip</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($suratList as $surat)
                    <tr>
                        <td><strong>{{ $surat->nomor_surat }}</strong></td>
                        <td>{{ $surat->template->nama_template ?? 'Template Dihapus' }}</td>
                        <td>
                            @php
                                $roleName = $surat->user?->roles?->first()?->name ?? 'User';
                                $badgeClass = match($roleName) {
                                    'Super Admin' => 'badge-role--super-admin',
                                    'Admin'       => 'badge-role--admin',
                                    default       => 'badge-role--user',
                                };
                            @endphp
                            <span class="badge-role {{ $badgeClass }}">{{ $surat->user->name ?? '-' }}</span>
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
                            <div class="action-buttons-group">
                                <a href="{{ route('surat.show', $surat) }}" class="btn-action-detail">Detail</a>
                                <a href="{{ route('surat.cetak', $surat) }}" target="_blank" class="btn-action-cetak">Cetak</a>

                                @if (strtolower($surat->status ?? '') === 'disetujui' || $surat->file_arsip)
                                    <span class="badge-status-approved">
                                        ✓ Sudah TTD
                                    </span>
                                @else
                                    <form action="{{ route('surat.ttd-digital', $surat) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui dan menandatangani surat ini secara digital?')">
                                        @csrf
                                        <button type="submit" class="btn-ttd-pending">
                                            ACC & TTD
                                        </button>
                                    </form>
                                @endif

                                <form action="{{ route('surat.upload-arsip', $surat) }}" method="POST" enctype="multipart/form-data" class="upload-inline-form">
                                    @csrf
                                    <input type="file" name="file_arsip" required id="file-{{ $surat->id }}" class="hide-file-input" onchange="this.form.submit()">
                                    <button type="button" onclick="document.getElementById('file-{{ $surat->id }}').click()" class="btn-dark btn-sm">
                                        {{ $surat->file_arsip ? 'Ganti File' : 'Upload File' }}
                                    </button>
                                </form>
                                <form action="{{ route('surat.destroy', $surat) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus surat ini? Data yang dihapus tidak bisa dikembalikan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-delete" title="Hapus Surat">
                                        Hapus
                                    </button>
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
