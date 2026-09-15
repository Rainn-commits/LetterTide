@extends('layouts.app') 
 
@section('page-title', 'Arsip Surat') 
 
@section('page-css') 
    @vite('resources/css/surat-arsip.css') 
@endsection 
 
@section('content') 
    @if (session('success')) 
        <div class="surat-alert surat-alert--success">{{ session('success') }}</div> 
    @endif 
 
    <div class="arsip-header-wrapper"> 
        <h2 class="arsip-header-title">Arsip Surat</h2> 
 
        @if ($folder === 'masuk') 
            <button type="button" class="btn-modern-upload-masuk" 
                onclick="document.getElementById('formSuratMasukPanel').style.display = document.getElementById('formSuratMasukPanel').style.display === 'none' ? 'block' : 'none';"> 
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"> 
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/> 
                    <polyline points="17 8 12 3 7 8"/> 
                    <line x1="12" y1="3" x2="12" y2="15"/> 
                </svg> 
                Tambah Surat Masuk 
            </button> 
        @endif 
    </div> 
 
    <div class="arsip-folder-tabs"> 
        <a href="{{ route('arsip.index', array_merge(request()->except(['page', 'folder']), ['folder' => 'keluar'])) }}" 
            class="arsip-folder-tabs__link {{ $folder === 'keluar' ? 'is-active' : '' }}"> 
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m22 2-7 20-4-9-9-4Z"/>
                <path d="M22 2 11 13"/>
            </svg> 
            Surat Keluar 
        </a> 

        <a href="{{ route('arsip.index', array_merge(request()->except(['page', 'folder']), ['folder' => 'masuk'])) }}" 
            class="arsip-folder-tabs__link {{ $folder === 'masuk' ? 'is-active' : '' }}"> 
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="22 12 16 12 14 15 10 15 8 12 2 12"/>
                <path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11Z"/>
            </svg> 
            Surat Masuk 
        </a> 
    </div> 
 
    @if ($folder === 'masuk') 
        <div class="card filter-card {{ $errors->any() ? 'is-visible' : '' }}" id="formSuratMasukPanel"> 
            <h3 style="margin: 0 0 14px 0; font-size: 15px; color: var(--color-ink);">Tambah Surat Masuk</h3> 
 
            <form action="{{ route('arsip.surat-masuk.store') }}" method="POST" enctype="multipart/form-data"> 
                @csrf 

                <div class="filter-grid"> 
                    <div class="form-group-center"> 
                        <label>Nomor Surat (dari Pengirim)</label> 
                        <input type="text" name="nomor_surat" value="{{ old('nomor_surat') }}" class="form-control" required> 

                        @error('nomor_surat') 
                            <p style="color: var(--color-danger); font-size: 12px; margin: 4px 0 0;">{{ $message }}</p> 
                        @enderror 
                    </div> 
 
                    <div class="form-group-center"> 
                        <label>Pengirim / Instansi Asal</label> 
                        <input type="text" name="pengirim" value="{{ old('pengirim') }}" class="form-control" required> 

                        @error('pengirim') 
                            <p style="color: var(--color-danger); font-size: 12px; margin: 4px 0 0;">{{ $message }}</p> 
                        @enderror 
                    </div> 
 
                    <div class="form-group-center"> 
                        <label>Perihal</label> 
                        <input type="text" name="perihal" value="{{ old('perihal') }}" class="form-control" required> 

                        @error('perihal') 
                            <p style="color: var(--color-danger); font-size: 12px; margin: 4px 0 0;">{{ $message }}</p> 
                        @enderror 
                    </div> 
 
                    <div class="form-group-center"> 
                        <label>Tanggal Surat</label> 
                        <input type="date" name="tanggal_surat" value="{{ old('tanggal_surat') }}" class="form-control" required> 

                        @error('tanggal_surat') 
                            <p style="color: var(--color-danger); font-size: 12px; margin: 4px 0 0;">{{ $message }}</p> 
                        @enderror 
                    </div> 
 
                    <div class="form-group-center"> 
                        <label>File Surat (PDF/DOC, maks 5MB)</label> 
                        <input type="file" name="file_arsip" accept=".pdf,.doc,.docx" class="form-control" required> 

                        @error('file_arsip') 
                            <p style="color: var(--color-danger); font-size: 12px; margin: 4px 0 0;">{{ $message }}</p> 
                        @enderror 
                    </div> 
                </div> 
 
                <div class="filter-actions"> 
                    <button type="button" class="btn-ghost" 
                        onclick="document.getElementById('formSuratMasukPanel').style.display='none';">
                        Batal
                    </button> 

                    <button type="submit" class="btn-dark">
                        Simpan Surat Masuk
                    </button> 
                </div> 
            </form> 
        </div> 
    @endif 
 
    <form method="GET" action="{{ route('arsip.index') }}" class="card filter-card"> 
        <input type="hidden" name="folder" value="{{ $folder }}"> 
 
        <div class="filter-grid"> 
            @if (Auth::user()->hasAnyRole(['Super Admin', 'Admin'])) 
                <div class="form-group-center"> 
                    <label>Cakupan Surat</label> 

                    <select name="pembuat" class="form-control"> 
                        <option value="semua" {{ request('pembuat') === 'semua' ? 'selected' : '' }}>
                            Semua Surat
                        </option> 

                        <option value="saya" {{ request('pembuat') === 'saya' ? 'selected' : '' }}>
                            Surat Buatan Saya
                        </option> 
                    </select> 
                </div> 
            @endif 
 
            <div class="form-group-center"> 
                <label>Pencarian</label> 

                <input type="text" 
                    name="q" 
                    value="{{ request('q') }}" 
                    placeholder="Nomor/Template/Pembuat..." 
                    class="form-control"> 
            </div> 
 
            <div class="form-group-center"> 
                <label>Jenis Surat</label> 

                <select name="template_id" class="form-control"> 
                    <option value="">-- Semua Surat --</option> 

                    @foreach ($templates as $t) 
                        <option value="{{ $t->id }}" {{ (string) request('template_id') === (string) $t->id ? 'selected' : '' }}> 
                            {{ $t->nama_template }} 
                        </option> 
                    @endforeach 
                </select> 
            </div> 
 
            <div class="form-group-center"> 
                <label>Tanggal Mulai</label> 

                <input type="date" 
                    name="tanggal_mulai" 
                    value="{{ request('tanggal_mulai') }}" 
                    class="form-control"> 
            </div> 
 
            <div class="form-group-center"> 
                <label>Tanggal Selesai</label> 

                <input type="date" 
                    name="tanggal_selesai" 
                    value="{{ request('tanggal_selesai') }}" 
                    class="form-control"> 
            </div> 
 
            <div class="form-group-center"> 
                <label>Status File</label> 

                <select name="status_arsip" class="form-control"> 
                    <option value="">-- Semua --</option> 

                    <option value="uploaded" {{ request('status_arsip') === 'uploaded' ? 'selected' : '' }}>
                        Ada File
                    </option> 

                    <option value="pending" {{ request('status_arsip') === 'pending' ? 'selected' : '' }}>
                        Belum Upload
                    </option> 
                </select> 
            </div> 
        </div> 
 
        <div class="filter-actions"> 
            <a href="{{ route('arsip.index', ['folder' => $folder]) }}" class="btn-ghost">
                Reset Filter
            </a> 

            <button type="submit" class="btn-dark">
                Cari & Filter
            </button> 
        </div> 
    </form> 
 
    <div class="card card--table-expanded"> 
        <table class="template-table-modern"> 
            <thead> 
                <tr> 
                    <th style="width: 16%;">Nomor Surat</th>
                    <th style="width: 20%;">{{ $folder === 'masuk' ? 'Perihal' : 'Template' }}</th>
                    <th style="width: 12%;">{{ $folder === 'masuk' ? 'Diarsipkan Oleh' : 'Pembuat' }}</th>
                    <th style="width: 10%;">Tanggal</th>
                    <th style="width: 10%;">File Arsip</th>
                    <th style="width: 12%;">Status</th>
                    <th style="width: 20%; text-align: center;">Aksi</th>
                </tr> 
            </thead> 

            <tbody> 
                @forelse ($suratList as $surat) 
                    <tr> 
                        <td class="col-nomor"> 
                            <strong>{{ $surat->nomor_surat }}</strong> 

                            @if ($surat->jenis === 'Masuk' && $surat->pengirim) 
                                <div style="font-size: 11px; color: var(--color-ink-soft); margin-top: 2px;"> 
                                    Dari: {{ $surat->pengirim }} 
                                </div> 
                            @endif 
                        </td> 

                        <td class="col-template"> 
                            @if ($surat->jenis === 'Keluar') 
                                {{ $surat->template->nama_template ?? 'Template Dihapus' }} 
                            @else 
                                {{ $surat->perihal ?? '-' }} 
                            @endif 
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
 
                            <span class="{{ $badgeClass }}"> 
                                {{ $roleName }} 
                            </span> 
                        </td> 

                        <td>
                            {{ $surat->tanggal_surat->format('d/m/Y') }}
                        </td> 

                        <td> 
                            @if ($surat->file_arsip) 
                                <a href="{{ asset('storage/' . $surat->file_arsip) }}" 
                                    target="_blank" 
                                    class="file-badge-link"> 
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                        <line x1="16" y1="13" x2="8" y2="13"/>
                                        <line x1="16" y1="17" x2="8" y2="17"/>
                                        <line x1="10" y1="9" x2="8" y2="9"/>
                                    </svg> 
                                    Lihat File 
                                </a> 
                            @else 
                                <span class="badge-empty">Belum ada</span> 
                            @endif 
                        </td> 

                        {{-- STATUS --}}
                        <td>
                            @if (strtolower($surat->status ?? '') === 'disetujui')
                                <span class="status-badge status-badge--signed">
                                    Sudah Ditandatangani
                                </span>
                            @else
                                <span class="status-badge status-badge--unsigned">
                                    Belum Ditandatangani
                                </span>
                            @endif
                        </td>

                        {{-- AKSI --}}
                        <td> 
                            <div class="action-buttons-group-expanded"> 
                                @if ($surat->jenis === 'Keluar') 
                                    <a href="{{ route('surat.show', $surat) }}" 
                                        class="btn-modern btn-modern--detail btn-modern--icon-only" 
                                        title="Lihat Detail"> 
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg> 
                                    </a> 

                                    <a href="{{ route('surat.cetak', $surat) }}" 
                                        target="_blank" 
                                        class="btn-modern btn-modern--cetak btn-modern--icon-only" 
                                        title="Cetak Surat"> 
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="6 9 6 2 18 2 18 9"/>
                                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                                            <rect x="6" y="14" width="12" height="8"/>
                                        </svg> 
                                    </a> 
 
                                    @if (strtolower($surat->status ?? '') === 'disetujui') 
                                        <span class="btn-modern--approved btn-modern--icon-only" 
                                            title="Sudah Ditandatangani"> 
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                                <polyline points="22 4 12 14.01 9 11.01"/>
                                            </svg> 
                                        </span> 
                                    @else 
                                        <form action="{{ route('surat.ttd-digital', $surat) }}" 
                                            method="POST" 
                                            style="display: inline-block;" 
                                            data-confirm="Apakah Anda yakin ingin menyetujui dan menandatangani surat ini secara digital?" 
                                            data-confirm-variant="success" 
                                            data-confirm-label="Ya, tanda tangani"> 
                                            @csrf 

                                            <button type="submit" 
                                                class="btn-modern btn-modern--ttd btn-modern--icon-only" 
                                                title="ACC & Tanda Tangan Digital"> 
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"> 
                                                    <path d="M12 20h9"/>
                                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/> 
                                                </svg> 
                                            </button> 
                                        </form> 
                                    @endif 
                                @else 
                                    @if ($surat->file_arsip) 
                                        <a href="{{ asset('storage/' . $surat->file_arsip) }}" 
                                            target="_blank" 
                                            class="btn-modern btn-modern--detail btn-modern--icon-only" 
                                            title="Lihat File"> 
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg> 
                                        </a> 
                                    @endif 
                                @endif 
 
                                <form action="{{ route('surat.upload-arsip', $surat) }}" 
                                    method="POST" 
                                    enctype="multipart/form-data" 
                                    class="upload-inline-form"> 
                                    @csrf 

                                    <input type="file" 
                                        name="file_arsip" 
                                        required 
                                        id="file-{{ $surat->id }}" 
                                        class="hide-file-input" 
                                        onchange="this.form.submit()"> 

                                    <button type="button" 
                                        onclick="document.getElementById('file-{{ $surat->id }}').click()" 
                                        class="btn-modern btn-modern--upload btn-modern--icon-only" 
                                        title="{{ $surat->file_arsip ? 'Ganti File' : 'Upload File' }}"> 
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                            <polyline points="17 8 12 3 7 8"/>
                                            <line x1="12" y1="3" x2="12" y2="15"/>
                                        </svg> 
                                    </button> 
                                </form> 

                                <form action="{{ route('surat.destroy', $surat) }}" 
                                    method="POST" 
                                    style="display: inline-block;" 
                                    data-confirm="Apakah Anda yakin ingin menghapus surat ini? Data yang dihapus tidak bisa dikembalikan." 
                                    data-confirm-variant="danger" 
                                    data-confirm-label="Ya, Hapus"> 
                                    @csrf 
                                    @method('DELETE') 

                                    <button type="submit" 
                                        class="btn-modern btn-modern--delete btn-modern--icon-only" 
                                        title="Hapus Surat"> 
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                            <line x1="10" y1="11" x2="10" y2="17"/>
                                            <line x1="14" y1="11" x2="14" y2="17"/>
                                        </svg> 
                                    </button> 
                                </form> 
                            </div> 
                        </td> 
                    </tr> 
                @empty 
                    <tr> 
                        <td colspan="7" class="table-empty-state"> 
                            @if ($folder === 'masuk') 
                                Belum ada Surat Masuk. Klik "Tambah Surat Masuk" di atas untuk mengarsipkan surat dari pihak luar. 
                            @else 
                                Tidak ada data arsip surat yang ditemukan. 
                            @endif 
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
