@extends('layouts.app')

@section('page-title', 'Pengaturan Kop Surat')

@section('page-css')
    @vite('resources/css/kop-surat.css')
@endsection

@section('content')
<h2>Pengaturan Kop Surat</h2>

@if(session('success'))<p>{{ session('success') }}</p>@endif

@if (!$kopSurat)
    <div class="alert-box">
        <p>Kop surat belum diatur.</p>
    </div>

    <a href="{{ route('kop-surat.create') }}" class="btn-primary">
        + Buat Kop Surat Baru
    </a>
@else
    <div class="kop-preview-card">

    </div>

    <a href="{{ route('kop-surat.edit', $kopSurat->id) }}" class="btn-primary">
        Ubah / Simpan Versi Baru
    </a>
@endif

<a href="{{ route('kop-surat.create') }}">Ubah / Simpan Versi Baru</a>

<h3>Riwayat Versi</h3>
<table border="1">
    @foreach($riwayat as $r)
        <tr><td>Versi {{ $r->versi }}</td><td>{{ $r->nama_pt }}</td></tr>
    @endforeach
</table>
@endsection
