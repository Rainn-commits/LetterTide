<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $surat->nomor_surat }}</title>
    <style>
        @page {
            margin: 30mm;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000000;
            margin: 0;
        }

        .surat-paper {
            width: 100%;
            background: #ffffff;
        }

        .surat-paper__header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }

        .surat-paper__header-table td {
            padding: 0;
            vertical-align: middle;
        }

        .surat-paper__logo-left {
            width: 25%;
            text-align: left;
        }

        .surat-paper__logo {
            max-height: 75px;
            width: auto;
        }

        .surat-paper__company-right {
            width: 85%;
            text-align: right;
            color: #000000;
        }

        .surat-paper__company-right h2 {
            font-size: 15pt;
            font-weight: bold;
            margin: 0 0 2px 0;
            text-transform: uppercase;
        }

        .surat-paper__company-right p {
            font-size: 9.5pt;
            margin: 1px 0;
            line-height: 1.35;
        }

        .surat-paper__divider {
            border: 0;
            border-top: 2px solid #000;
            margin: 6px 0 20px 0;
        }

        .surat-paper__meta-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .surat-paper__meta-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .surat-paper__title {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            margin: 20px 0;
            text-decoration: underline;
        }

        .surat-paper__body {
            font-size: 12pt;
            line-height: 1.5;
            text-align: justify;
            margin-bottom: 30px;
        }

        .signature-wrapper {
            width: 100%;
            text-align: right;
            margin-top: 40px;
        }

        .signature-box {
            display: inline-block;
            width: 260px;
            text-align: center;
            font-size: 12pt;
            line-height: 1.4;
        }
    </style>
</head>
<body>
    <div class="surat-paper">
        @if ($surat->kopSurat)
            <table class="surat-paper__header-table">
                <tr>
                    <td class="surat-paper__logo-left">
                        @if ($logoDataUri)
                            <img src="{{ storage_path('app/public/' . $logoDataUri) }}" alt="Logo" class="surat-paper__logo">
                        @endif
                    </td>
                    <td class="surat-paper__company-right">
                        <h2>{{ $surat->kopSurat->nama_pt }}</h2>
                        @php
                            $alamatPt = $surat->kopSurat->alamat_pt ?? $surat->kopSurat->alamat ?? null;
                        @endphp
                        @if ($alamatPt)
                            <p>{{ $alamatPt }}</p>
                        @endif
                        <p>
                            @if ($surat->kopSurat->telepon)
                                WhatsApp. +62 {{ ltrim($surat->kopSurat->telepon, '+62') }}
                            @endif
                            @if ($surat->kopSurat->website)
                                &nbsp;&middot;&nbsp; Website. {{ $surat->kopSurat->website }}
                            @endif
                        </p>
                        @if ($surat->kopSurat->email)
                            <p>Email: {{ $surat->kopSurat->email }}</p>
                        @endif
                    </td>
                </tr>
            </table>
            <div class="surat-paper__divider"></div>
        @endif

        <table class="surat-paper__meta-table">
            <tr>
                <td style="width: 90px;">Nomor</td>
                <td style="width: 15px;">:</td>
                <td>{{ $surat->nomor_surat }}</td>
                <td style="text-align: right;">
                    Bandung, {{ \Carbon\Carbon::parse($surat->tanggal_surat)->locale('id')->translatedFormat('d F Y') }}
                </td>
            </tr>
            <tr>
                <td>Lampiran</td>
                <td>:</td>
                <td>{{ $surat->lampiran ?? '-' }}</td>
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
                <div style="height: 90px; padding-top: 10px; box-sizing: border-box;">
                    @if (strtolower($surat->status ?? '') === 'disetujui' && $qrDataUri)
                        <img src="{{ $qrDataUri }}" alt="QR TTD Digital" style="width: 70px; height: 70px;">
                    @endif
                </div>
                <div style="font-weight: bold; text-decoration: underline;">
                    {{ $namaPenandatangan }}
                </div>
            </div>
        </div>

        @if ($isiLampiran !== null)
            <div style="page-break-before: always;"></div>
            <div class="surat-paper__body">
                {!! $isiLampiran !!}
            </div>
        @endif
    </div>
</body>
</html>
