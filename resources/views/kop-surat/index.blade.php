@extends('layouts.app')

@section('page-title', 'Pengaturan Kop Surat')

@section('page-css')
    @vite('resources/css/kop-surat.css')
@endsection

@section('content')
<h2>Pengaturan Kop Surat</h2>

@if(session('success'))
    <p>{{ session('success') }}</p>
@endif

@if (!$kopSuratAktif)
    <div class="alert-box">
        <p>Kop surat belum diatur.</p>
    </div>

    <a href="{{ route('kop-surat.create') }}" class="btn-primary">
        + Buat Kop Surat Baru
    </a>
@else
    <div class="kop-preview-card">
        <p><strong>{{ $kopSuratAktif->nama_pt }}</strong></p>
        <p>{{ $kopSuratAktif->alamat_pt }}</p>
        <p>Telp: {{ $kopSuratAktif->telepon }} | Email: {{ $kopSuratAktif->email }}</p>
    </div>

    <a href="{{ route('kop-surat.create') }}" class="btn-primary">
        Ubah / Simpan Versi Baru
    </a>
@endif

<h3>Riwayat Versi</h3>
<table border="1">
    @foreach($riwayat as $r)
        <tr>
            <td>Versi {{ $r->versi }}</td>
            <td>{{ $r->nama_pt }}</td>
        </tr>
    @endforeach
</table>
@endsection
