@extends('layouts.app')

@section('page-title', 'Preview Surat')

@section('page-css')
    @vite('resources/css/surat.css')
    @vite('resources/css/surat-preview.css')
@endsection

@section('content')

    <div class="page-toolbar-preview">
        <a href="{{ route('surat.create') }}" class="btn-ghost">&larr; Buat Surat Lain</a>
        <div class="page-toolbar-preview__actions">
            <a href="{{ route('surat.edit', $surat->id) }}" class="btn-warning" style="padding: 8px 16px; border-radius: 6px; text-decoration: none;">Edit Isi Surat</a>
            <a href="{{ route('surat.cetak', $surat->id) }}" target="_blank" class="btn-dark" style="padding: 8px 16px; border-radius: 6px; text-decoration: none;">Cetak</a>
        </div>
    </div>

    <div class="surat-paper">

        @if ($surat->kopSurat)
            <div class="surat-paper__header">
                <div class="surat-paper__company-left">
                    <h2>{{ $surat->kopSurat->nama_pt }}</h2>
                    @if ($surat->kopSurat->logo_path)
                        <div class="surat-paper__logo-box">
                            <img src="{{ Storage::url($surat->kopSurat->logo_path) }}" alt="Logo" class="surat-paper__logo">
                        </div>
                    @endif
                </div>
                <div class="surat-paper__company-right">
                    <div>{{ $surat->kopSurat->alamat }}</div>
                    <div>WhatsApp. {{ $surat->kopSurat->telepon }} &nbsp; Website. {{ $surat->kopSurat->website }}</div>
                </div>
            </div>
            <div class="surat-paper__divider"></div>
        @endif

        <div class="surat-paper__meta-top">
            Bandung, {{ \Carbon\Carbon::parse($surat->tanggal_surat)->translatedFormat('d F Y') }}
        </div>

        <table class="surat-paper__meta-table">
            <tr>
                <td style="width: 80px;">Nomor</td>
                <td style="width: 15px;">:</td>
                <td>{{ $surat->nomor_surat }}</td>
            </tr>
        </table>

        <div class="surat-paper__title">
            {{ $surat->template->nama_template }}
        </div>

        <div class="surat-paper__body">
            {{ $isiSuratRendered }}
        </div>

        @php
            $values = $surat->isiSurat->pluck('nilai_field', 'nama_field')->toArray();

            $namaPenandatangan = $values['nama_penandatangan'] 
                              ?? $values['Nama Penandatangan'] 
                              ?? $values['penandatangan'] 
                              ?? null;

            $jabatanPenandatangan = $values['jabatan_penandatangan'] 
                                 ?? $values['Jabatan Penandatangan'] 
                                 ?? $values['jabatan'] 
                                 ?? 'Direktur PT. Inovindo Digital Media';

            if (empty($namaPenandatangan)) {
                $namaPenandatangan = $surat->user?->name ?? 'Novi Setia Nurviat';
            }
        @endphp

        <div class="signature-container">
            <div class="signature-box">
                <div>Hormat Kami,</div>
                <div>{{ $jabatanPenandatangan }}</div>

                <div class="signature-space"></div>

                <span class="signature-name">
                    {{ $namaPenandatangan }}
                </span>
                <div style="font-size: 10pt; color: #333;">
                    {{ $surat->kopSurat?->website ?? 'www.inovindo.co.id' }}
                </div>
            </div>
        </div>

    </div>

@endsection
