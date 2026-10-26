@extends('layouts.app')

@section('page-title', 'Preview Surat')

@section('page-css')
    @vite('resources/css/surat.css')
    @vite('resources/css/surat-preview.css')
@endsection

@section('content')

    <div class="page-toolbar-preview">
        <a href="{{ route('surat.create') }}" class="btn-ghost btn-ghost--icon-only" title="Buat Surat Lain">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        </a>

        <div class="page-toolbar-preview__actions">
            <a href="{{ route('surat.edit', $surat->id) }}" class="btn-warning">Edit Isi Surat</a>
            <a href="{{ route('surat.cetak', $surat->id) }}" target="_blank" class="btn-dark">Cetak</a>
        </div>
    </div>

    <div class="surat-paper">
        
        @if ($surat->kopSurat)
            <div class="surat-paper__header">
                <div class="surat-paper__logo-left">
                    @php
                        $logoPath = $surat->kopSurat->logo_path ?? $surat->kopSurat->logo ?? null;
                    @endphp
                    @if ($logoPath)
                        <img src="{{ asset('storage/' . str_replace('storage/', '', $logoPath)) }}" alt="Logo" class="surat-paper__logo">
                    @endif
                </div>

                <div class="surat-paper__company-right">
                    <h2>{{ $surat->kopSurat->nama_pt }}</h2>
                    @php
                        $alamatPt = $surat->kopSurat->alamat_pt ?? $surat->kopSurat->alamat ?? null;
                    @endphp
                    @if($alamatPt)
                        <p>{{ $alamatPt }}</p>
                    @endif
                    <p>
                        @if($surat->kopSurat->telepon)
                            WhatsApp. +62 {{ ltrim($surat->kopSurat->telepon, '+62') }}
                        @endif
                        @if($surat->kopSurat->website)
                            &nbsp;&middot;&nbsp; Website. {{ $surat->kopSurat->website }}
                        @endif
                    </p>
                    @if($surat->kopSurat->email)
                        <p>Email: {{ $surat->kopSurat->email }}</p>
                    @endif
                </div>
            </div>
            <div class="surat-paper__divider"></div>
        @endif

        @php
            $romawiBulan = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
            $bulanNum = (int) \Carbon\Carbon::parse($surat->tanggal_surat)->format('n');
            $bulanRomawi = $romawiBulan[$bulanNum] ?? 'I';
            $tahunDuaDigit = \Carbon\Carbon::parse($surat->tanggal_surat)->format('y');
            
            $nomorUrut = sprintf('%03d', $surat->id ?? 1);
            $kodeTemplate = $surat->template?->kode_template ?? 'SPSP';
            $namaPtSingkat = 'INOVINDO';

            $nomorSuratFormatted = "{$nomorUrut}/{$kodeTemplate}/{$namaPtSingkat}/{$bulanRomawi}/{$tahunDuaDigit}";
        @endphp

        <table class="surat-paper__meta-table" style="width: 100%; margin-bottom: 20px; border-collapse: collapse;">
            <tr>
                <td style="width: 90px; vertical-align: top;">Nomor</td>
                <td style="width: 15px; vertical-align: top;">:</td>
                <td style="vertical-align: top;">{{ $surat->nomor_surat ?? $nomorSuratFormatted }}</td>
                <td style="text-align: right; vertical-align: top;">
                    Bandung, {{ \Carbon\Carbon::parse($surat->tanggal_surat)->locale('id')->translatedFormat('d F Y') }}
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Lampiran</td>
                <td style="vertical-align: top;">:</td>
                <td style="vertical-align: top;">{{ $surat->lampiran ?? '-' }}</td>
                <td></td>
            </tr>
        </table>

        <div class="surat-paper__title">
            {{ $surat->template->nama_template }}
        </div>

        <div class="surat-paper__body">
            {!! $isiUtama !!}
        </div>

        <div class="signature-wrapper">
            <div class="signature-box">
                <div>Hormat Kami,</div>
                <div>{{ $jabatanPenandatangan }}</div>

                <div style="height: 90px; display: flex; flex-direction: column; align-items: center; justify-content: center; margin: 5px 0;">
                    @if (strtolower($surat->status ?? '') === 'disetujui' && $qrDataUri)
                        <img src="{{ $qrDataUri }}" alt="QR TTD Digital" style="width: 70px; height: 70px;">
                    @endif
                </div>

                <div class="signature-name">{{ $namaPenandatangan }}</div>
            </div>
        </div>

        @if ($isiLampiran !== null)
            <div style="page-break-before: always;"></div>

            <div class="surat-paper__body">
                {!! $isiLampiran !!}
            </div>
        @endif
    </div>

@endsection
