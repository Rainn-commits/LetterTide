@extends('layouts.app')

@section('page-title', 'Atur Kop Surat')

@section('page-css')
    @vite('resources/css/kop-surat.css')
@endsection

@section('content')
<div class="kop-container">
    <h2 class="kop-title">{{ $kopSuratAktif ? 'Simpan sebagai Versi Baru' : 'Atur Kop Surat' }}</h2>

    <form method="POST" action="{{ route('kop-surat.store') }}" enctype="multipart/form-data" class="kop-form">
        @csrf

        <div class="form-group">
            <label class="form-label">Logo perusahaan</label>
            <input type="file" name="logo" accept="image/*" class="form-control-file">
            @if($kopSuratAktif && $kopSuratAktif->logo)
                <div class="logo-preview-wrapper" style="margin-top: 8px; display: flex; align-items: center; gap: 10px;">
                    <img src="{{ Storage::url($kopSuratAktif->logo) }}" width="60" style="border-radius: 4px; border: 1px solid #e2e8f0;">
                    <small style="color: #64748b;">Logo saat ini (dipakai jika tidak upload baru)</small>
                </div>
            @endif
        </div>

        <div class="form-group">
            <label class="form-label">Nama perusahaan</label>
            <input type="text" name="nama_pt" class="form-control" value="{{ old('nama_pt', $kopSuratAktif->nama_pt ?? '') }}" required>
        </div>

        <div class="form-group">
            <label class="form-label">Alamat</label>
            <input type="text" name="alamat_pt" class="form-control" value="{{ old('alamat_pt', $kopSuratAktif->alamat_pt ?? '') }}" required>
        </div>

        <div class="form-group">
            <label class="form-label">Telepon</label>
            <input type="text" name="telepon" class="form-control" value="{{ old('telepon', $kopSuratAktif->telepon ?? '') }}" required>
        </div>

        <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $kopSuratAktif->email ?? '') }}" required>
        </div>

        <div class="form-group">
            <label class="form-label">Website</label>
            <input type="text" name="website" class="form-control" value="{{ old('website', $kopSuratAktif->website ?? '') }}">
        </div>

        <div class="form-actions" style="margin-top: 24px;">
            <button type="submit" class="btn-primary">Simpan sebagai versi baru</button>
            <p style="margin-top: 8px;"><small style="color: #64748b;">Surat yang sudah dibuat sebelumnya tidak akan berubah.</small></p>
        </div>
    </form>
</div>
@endsection
