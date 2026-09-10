<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Surat - {{ $surat->nomor_surat }}</title>
    @vite('resources/css/surat-edit.css')
    @vite('resources/css/surat-preview.css')
    <style>
        .surat-paper {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
        }

        @media print {
            html, body {
                background: #fff !important;
                margin: 0 !important;
                padding: 0 !important;
                -webkit-print-color-adjust: exact;
            }

            .no-print { 
                display: none !important; 
            }

            .surat-paper { 
                box-shadow: none !important; 
                border: none !important; 
                margin: 0 !important; 
                padding: 0 !important; 
                width: 100% !important;
            }

            @page { 
                size: A4; 
                margin: 30mm; 
            }

            body, 
            .surat-paper, 
            .surat-paper *,
            .surat-paper p, 
            .surat-paper div, 
            .surat-paper td, 
            .surat-paper span,
            .surat-paper li {
                font-size: 12pt !important;
                line-height: 1.5 !important;
            }

            .surat-paper__company-right h2 { 
                font-size: 14pt !important; 
            }
            .surat-paper__company-right p { 
                font-size: 9.5pt !important; 
            }
        }

        .signature-wrapper {
            display: flex;
            justify-content: flex-end;
            width: 100%;
            margin-top: 40px;
        }
        .signature-box {
            width: 250px;
            text-align: center;
        }
        .signature-box span[style*="color: #ef4444"],
        .surat-paper .signature-box span {
            color: #ef4444 !important;
            font-size: 9pt !important; 
            font-style: italic !important;
        }
    </style>
    <style>
        body, 
        .surat-paper, 
        .surat-paper *,
        .surat-paper p, 
        .surat-paper div, 
        .surat-paper span,
        .surat-paper li {
            font-family: "Times New Roman", Times, serif !important;
            font-size: 12pt !important;
            line-height: 1.5 !important;
            color: #000 !important;
        }

        .surat-paper table,
        .surat-paper table tr,
        .surat-paper table th,
        .surat-paper table td,
        .surat-paper table p,
        .surat-paper table span {
            font-family: "Times New Roman", Times, serif !important;
            font-size: 12pt !important;
            line-height: 1.3 !important;
        }

        .surat-paper__company-right h2 { font-size: 14pt !important; }
        .surat-paper__company-right p { font-size: 9.5pt !important; }

        @media print {
            html, body {
                background: #fff !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .no-print { 
                display: none !important; 
            }

            .surat-paper { 
                box-shadow: none !important; 
                border: none !important; 
                margin: 0 !important; 
                padding: 0 !important; 
                width: 100% !important;
            }

            @page { 
                size: A4; 
                margin: 30mm !important; 
            }
        }

        .signature-wrapper {
            display: flex;
            justify-content: flex-end;
            width: 100%;
            margin-top: 40px;
        }
        .signature-box {
            width: 250px;
            text-align: center;
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="padding: 15px; text-align: center; background: #f3f4f6;">
        <button onclick="window.print()" style="padding: 8px 16px; cursor: pointer; font-weight: bold;">Cetak Dokumen</button>
        <button onclick="window.close()" style="padding: 8px 16px; cursor: pointer;">Tutup</button>
    </div>

    <div class="surat-paper">

        @if ($surat->kopSurat)
        <div class="surat-paper__header" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
            <div class="surat-paper__logo-left" style="width: 25%;">
                @php
                    $logoPath = $surat->kopSurat->logo_path ?? $surat->kopSurat->logo ?? null;
                @endphp
                @if ($logoPath)
                    <img src="{{ asset('storage/' . str_replace('storage/', '', $logoPath)) }}"
                        alt="Logo"
                        class="surat-paper__logo"
                        style="max-height: 80px !important; width: auto !important; display: block !important;">
                @endif
            </div>

            <div class="surat-paper__company-right" style="width: 75%; text-align: right;">
                <h2 style="font-size: 15pt; font-weight: bold; margin: 0; text-transform: uppercase;">{{ $surat->kopSurat->nama_pt }}</h2>
                @php
                    $alamatPt = $surat->kopSurat->alamat_pt ?? $surat->kopSurat->alamat ?? null;
                @endphp
                @if($alamatPt)
                    <p style="font-size: 9.5pt; margin: 2px 0;">{{ $alamatPt }}</p>
                @endif
                <p style="font-size: 9.5pt; margin: 1px 0;">
                    @if($surat->kopSurat->telepon) WhatsApp. +62 {{ ltrim($surat->kopSurat->telepon, '+62') }} @endif
                    @if($surat->kopSurat->website) &middot; Website. {{ $surat->kopSurat->website }} @endif
                </p>
                @if($surat->kopSurat->email)
                    <p style="font-size: 9.5pt; margin: 1px 0;">Email: {{ $surat->kopSurat->email }}</p>
                @endif
            </div>
        </div>

        <div style="border-top: 2px solid #000; margin-top: 6px; margin-bottom: 20px;"></div>
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

        <table style="width: 100%; margin-bottom: 20px; border-collapse: collapse;">
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

        <div style="text-align: center; font-weight: bold; font-size: 12pt; margin: 20px 0; text-decoration: underline;">
            {{ $surat->template->nama_template }}
        </div>

        <div style="font-size: 12pt; line-height: 1.5; text-align: justify; margin-bottom: 30px;">
            {!! $isiUtama !!}
        </div>

        <div class="signature-wrapper">
            <div class="signature-box">
                <div>Hormat Kami,</div>
                <div>{{ $jabatanPenandatangan }}</div>

                <div style="height: 90px; display: flex; flex-direction: column; align-items: center; justify-content: center; margin: 5px 0;">
                    @if (strtolower($surat->status ?? '') === 'disetujui' && $qrDataUri)
                        <img src="{{ $qrDataUri }}" alt="QR TTD Digital" style="width: 70px; height: 70px;">
                    @else
                        <span style="color: #ef4444; font-size: 9pt; font-style: italic;">
                            (Belum Menyetujui / Ditandatangani)
                        </span>
                    @endif
                </div>

                <div style="font-weight: bold; text-decoration: underline;">
                    {{ $namaPenandatangan }}
                </div>
            </div>
        </div>

        @if ($isiLampiran !== null)
            <div style="page-break-before: always;"></div>

            <div style="font-size: 12pt; line-height: 1.5; text-align: justify;">
                {!! $isiLampiran !!}
            </div>
        @endif

    </div>

</body>
</html>
