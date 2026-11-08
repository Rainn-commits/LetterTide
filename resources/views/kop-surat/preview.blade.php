@extends('layouts.app')

@section('page-title', 'Preview Kop Surat')

@section('page-css')
    @vite('resources/css/surat-preview.css')
@endsection

@section('content')

    <div class="page-toolbar-preview">
        <a href="{{ route('kop-surat.index') }}" class="btn-ghost btn-ghost--icon-only" title="Kembali">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        </a>
        <div class="page-toolbar-preview__actions">
            <span class="btn-dark" style="cursor: default; opacity: 0.85;">Versi {{ $kopSurat->versi }} {{ $kopSurat->is_aktif ? '(Aktif)' : '(Tidak Aktif)' }}</span>
        </div>
    </div>

    <div class="surat-paper">
        <div class="surat-paper__header">
            <div class="surat-paper__logo-left">
                @if ($kopSurat->logo)
                    <img src="{{ asset('storage/' . str_replace('storage/', '', $kopSurat->logo)) }}" alt="Logo" class="surat-paper__logo">
                @endif
            </div>

            <div class="surat-paper__company-right">
                <h2>{{ $kopSurat->nama_pt }}</h2>
                @if ($kopSurat->alamat_pt)
                    <p>{{ $kopSurat->alamat_pt }}</p>
                @endif
                <p>
                    @if ($kopSurat->telepon)
                        WhatsApp. +62 {{ ltrim($kopSurat->telepon, '+62') }}
                    @endif
                    @if ($kopSurat->website)
                        &nbsp;&middot;&nbsp; Website. {{ $kopSurat->website }}
                    @endif
                </p>
                @if ($kopSurat->email)
                    <p>Email: {{ $kopSurat->email }}</p>
                @endif
            </div>
        </div>
        <div class="surat-paper__divider"></div>

        <table class="surat-paper__meta-table">
            <tr>
                <td style="width: 90px; vertical-align: top;">Nomor</td>
                <td style="width: 15px; vertical-align: top;">:</td>
                <td style="vertical-align: top;">000/CONTOH/INOVINDO/IX/2026</td>
                <td style="text-align: right; vertical-align: top;">
                    Bandung, {{ now()->locale('id')->translatedFormat('d F Y') }}
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Lampiran</td>
                <td style="vertical-align: top;">:</td>
                <td style="vertical-align: top;">-</td>
                <td></td>
            </tr>
        </table>

        <div class="surat-paper__title">
            (Contoh Judul Surat)
        </div>

        <div class="surat-paper__body">
            Ini adalah tampilan contoh untuk menunjukkan posisi kop surat, nomor, dan tata letak dokumen jika kop surat versi {{ $kopSurat->versi }} ini digunakan pada surat sesungguhnya. Isi surat asli akan muncul di bagian ini menggantikan paragraf contoh ini.
        </div>
    </div>

@endsection
