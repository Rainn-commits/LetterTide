@extends('layouts.app')
@section('content')
<h2>Pengaturan Kop Surat</h2>

@if(session('success'))<p>{{ session('success') }}</p>@endif

@if($kopSuratAktif)
    <img src="{{ Storage::url($kopSuratAktif->logo) }}" width="100">
    <p>{{ $kopSuratAktif->nama_pt }} — versi {{ $kopSuratAktif->versi }}</p>
    <p>{{ $kopSuratAktif->alamat_pt }}</p>
@else
    <p>Kop surat belum diatur.</p>
@endif

<a href="{{ route('kop-surat.create') }}">Ubah / Simpan Versi Baru</a>

<h3>Riwayat Versi</h3>
<table border="1">
    @foreach($riwayat as $r)
        <tr><td>Versi {{ $r->versi }}</td><td>{{ $r->nama_pt }}</td></tr>
    @endforeach
</table>
@endsection