@extends('layouts.app')

@section('page-title', 'Preview Template Surat')

@section('page-css')
    @vite('resources/css/surat-preview.css')
    <style>
        .ph-preview {
            background: #FFF3BF;
            border-radius: 3px;
            padding: 0 3px;
            font-weight: 600;
        }

        .preview-hint {
            width: 215mm;
            max-width: 100%;
            box-sizing: border-box;
            margin: 0 auto 14px;
            padding: 10px 14px;
            background: #F0FDFE;
            border: 1px solid rgba(0, 195, 208, 0.25);
            border-radius: 10px;
            color: #475569;
            font-size: 0.85rem;
        }

        .kop-picker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin: 0;
            font-size: 0.85rem;
            color: #475569;
        }

        .kop-picker select {
            padding: 7px 10px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
            font-size: 0.85rem;
            max-width: 220px;
        }
    </style>
@endsection

@section('content')

    <div class="page-toolbar-preview">
        <a href="{{ route('template-surat.index') }}" class="btn-ghost btn-ghost--icon-only" title="Kembali">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        </a>

        <div class="page-toolbar-preview__actions">
            @if ($daftarKop->isNotEmpty())
                <form method="GET" action="{{ route('template-surat.preview', $template) }}" class="kop-picker">
                    <label for="kop_id">Kop surat</label>
                    <select id="kop_id" name="kop_id" onchange="this.form.submit()">
                        @foreach ($daftarKop as $kop)
                            <option value="{{ $kop->id }}" {{ $kopSurat && $kopSurat->id === $kop->id ? 'selected' : '' }}>
                                {{ $kop->nama_kop }} (v{{ $kop->versi }})
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif

            <span class="btn-dark" style="cursor: default; opacity: 0.85;">
                Versi {{ $template->versi }} {{ $template->is_aktif ? '(Aktif)' : '(Lama)' }}
            </span>
        </div>
    </div>

    <div class="preview-hint">
        Bagian berwarna kuning adalah isian (placeholder) yang akan diganti dengan data asli saat surat dibuat.
        @if (! $kopSurat)
            Belum ada kop surat, jadi bagian kop tidak ditampilkan.
        @endif
    </div>

    <div class="surat-paper">

        @if ($kopSurat)
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
        @endif

        @php
            $romawiBulan = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
            $nomorContoh = '000/CONTOH/INOVINDO/' . $romawiBulan[now()->month - 1] . '/' . now()->year;
        @endphp

        <table class="surat-paper__meta-table" style="width: 100%; margin-bottom: 20px; border-collapse: collapse;">
            <tr>
                <td style="width: 90px; vertical-align: top;">Nomor</td>
                <td style="width: 15px; vertical-align: top;">:</td>
                <td style="vertical-align: top;">{{ $nomorContoh }}</td>
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
            {{ $template->nama_template }}
        </div>

        <div class="surat-paper__body">
            {!! $isiUtama !!}
        </div>

        <div class="signature-wrapper">
            <div class="signature-box">
                <div>Hormat Kami,</div>
                <div>{{ $jabatanPenandatangan }}</div>

                <div style="height: 90px; margin: 5px 0;"></div>

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
