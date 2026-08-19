@extends('layouts.app')
@section('content')
<h2>{{ $kopSuratAktif ? 'Simpan sebagai Versi Baru' : 'Atur Kop Surat' }}</h2>

<form method="POST" action="{{ route('kop-surat.store') }}" enctype="multipart/form-data">
    @csrf

    <label>Logo perusahaan</label><br>
    <input type="file" name="logo" accept="image/*"><br>
    @if($kopSuratAktif && $kopSuratAktif->logo)
        <img src="{{ Storage::url($kopSuratAktif->logo) }}" width="60"><small>Logo saat ini (dipakai jika tidak upload baru)</small>
    @endif

    <label>Nama perusahaan</label><br>
    <input type="text" name="nama_pt" value="{{ old('nama_pt', $kopSuratAktif->nama_pt ?? '') }}" required><br>

    <label>Alamat</label><br>
    <input type="text" name="alamat_pt" value="{{ old('alamat_pt', $kopSuratAktif->alamat_pt ?? '') }}" required><br>

    <label>Telepon</label><br>
    <input type="text" name="telepon" value="{{ old('telepon', $kopSuratAktif->telepon ?? '') }}" required><br>

    <label>Email</label><br>
    <input type="email" name="email" value="{{ old('email', $kopSuratAktif->email ?? '') }}" required><br>

    <label>Website</label><br>
    <input type="text" name="website" value="{{ old('website', $kopSuratAktif->website ?? '') }}"><br>

    <button type="submit">Simpan sebagai versi baru</button>
    <p><small>Surat yang sudah dibuat sebelumnya tidak akan berubah.</small></p>
</form>
@endsection
