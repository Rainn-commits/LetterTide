<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $surat->nomor_surat }}</title>
    <style>
        
        .surat-paper {
            width: 210mm;
            min-height: 297mm;
            padding: 30mm;
            margin: 0;
            background: #ffffff;
            box-sizing: border-box;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000000;
            margin: 0;
        }

        .surat-paper__header {
            display: block;
            width: 100%;
            margin-bottom: 6px;
        }

        .surat-paper__logo-left {
            display: inline-block;
            width: 24%;
            vertical-align: top;
        }

        .surat-paper__logo {
            max-height: 85px;
            width: auto;
        }

        .surat-paper__company-right {
            display: inline-block;
            width: 74%;
            vertical-align: top;
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
            <div class="surat-paper__header">
                <div class="surat-paper__logo-left">
                    @if ($logoDataUri)
                        <img src="{{ $logoDataUri }}" alt="Logo" class="surat-paper__logo">
                    @endif
                </div>

                <div class="surat-paper__company-right">
                    <h2>{{ $surat->kopSurat->nama_pt }}</h2>
                    @if($surat->kopSurat->alamat)
                        <p>{{ $surat->kopSurat->alamat }}</p>
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

                <div style="height: 90px;">
                    @if (strtolower($surat->status ?? '') === 'disetujui' && $qrDataUri)
                        <img src="{{ $qrDataUri }}" alt="QR TTD Digital" style="width: 70px; height: 70px;">
                    @else
                        <p style="color: #ef4444; font-size: 9pt; font-style: italic;">
                            (Belum Menyetujui / Ditandatangani)
                        </p>
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
