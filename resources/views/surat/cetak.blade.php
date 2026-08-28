<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Surat - {{ $surat->nomor_surat }}</title>
    @vite('resources/css/surat.css')
    @vite('resources/css/surat-preview.css')
    <style>
        @media print {
            body { background: none; margin: 0; padding: 0; }
            .no-print { display: none !important; }
            .surat-paper { box-shadow: none !important; border: none !important; margin: 0 !important; width: 100% !important; padding: 0 !important; }
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
        <button onclick="window.print()" style="padding: 8px 16px; cursor: pointer;">Cetak Dokumen</button>
        <button onclick="window.close()" style="padding: 8px 16px; cursor: pointer;">Tutup</button>
    </div>

    <div class="surat-paper">

        @if ($surat->kopSurat)
            <div class="surat-paper__header" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                <div class="surat-paper__logo-left" style="width: 25%;">
                    @if ($surat->kopSurat->logo)
                        <img src="{{ asset('storage/' . $surat->kopSurat->logo) }}" alt="Logo" style="max-height: 80px; width: auto;">
                    @endif
                </div>

                <div class="surat-paper__company-right" style="width: 75%; text-align: right;">
                    <h2 style="font-size: 15pt; font-weight: bold; margin: 0; text-transform: uppercase;">{{ $surat->kopSurat->nama_pt }}</h2>
                    <p style="font-size: 9.5pt; margin: 2px 0;">{{ $surat->kopSurat->alamat_pt }}</p>
                    <p style="font-size: 9.5pt; margin: 1px 0;">
                        @if($surat->kopSurat->telepon) WhatsApp. {{ $surat->kopSurat->telepon }} @endif
                        @if($surat->kopSurat->website) &middot; Website. {{ $surat->kopSurat->website }} @endif
                    </p>
                    @if($surat->kopSurat->email)
                        <p style="font-size: 9.5pt; margin: 1px 0;">Email: {{ $surat->kopSurat->email }}</p>
                    @endif
                </div>
            </div>
            <div style="border-top: 2px solid #000; margin-top: 6px; margin-bottom: 20px;"></div>
        @endif

        <div style="text-align: right; margin-bottom: 15px;">
            Bandung, {{ \Carbon\Carbon::parse($surat->tanggal_surat)->translatedFormat('d F Y') }}
        </div>

        <table style="margin-bottom: 20px; border-collapse: collapse;">
            <tr>
                <td style="width: 80px;">Nomor</td>
                <td style="width: 15px;">:</td>
                <td>{{ $surat->nomor_surat }}</td>
            </tr>
            <tr>
                <td>Lampiran</td>
                <td>:</td>
                <td>{{ $surat->lampiran ?? '-' }}</td>
            </tr>
        </table>

        <div style="text-align: center; font-weight: bold; font-size: 12pt; margin: 20px 0; text-decoration: underline;">
            {{ $surat->template->nama_template }}
        </div>

        <div style="font-size: 12pt; line-height: 1.5; text-align: justify; margin-bottom: 30px;">
            {!! nl2br(e($isiSuratRendered)) !!}
        </div>

        <div class="signature-wrapper">
            <div class="signature-box">
                <div>Hormat Kami,</div>
                <div>{{ $jabatanPenandatangan ?? 'Direktur PT. Inovindo Digital Media' }}</div>

                <div style="height: 90px; display: flex; flex-direction: column; align-items: center; justify-content: center; margin: 5px 0;">
                    @if (strtolower($surat->status ?? '') === 'disetujui')
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode(route('surat.show', $surat)) }}" alt="QR TTD Digital" style="width: 70px; height: 70px;">
                        <span style="font-size: 7.5pt; color: #15803d; font-weight: bold; margin-top: 2px;">
                            ✓ Signed Digital by {{ $surat->penandatangan->name ?? $namaPenandatangan }}
                        </span>
                    @else
                        <span style="color: #ef4444; font-size: 9pt; font-style: italic;">
                            (Belum Menyetujui / Ditandatangani)
                        </span>
                    @endif
                </div>

                <div style="font-weight: bold; text-decoration: underline;">
                    {{ $namaPenandatangan ?? $surat->user->name }}
                </div>
                <div style="font-size: 10pt; color: #333;">
                    {{ $surat->kopSurat?->website ?? 'www.inovindo.co.id' }}
                </div>
            </div>
        </div>

    </div>

</body>
</html>
