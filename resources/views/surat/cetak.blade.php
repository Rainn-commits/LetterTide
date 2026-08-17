<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Surat - {{ $surat->nomor_surat }}</title>
    @vite('resources/css/surat.css')
    <style>
        @media print {
            body { background: none; margin: 0; padding: 0; }
            .no-print { display: none !important; }
            .surat-paper { box-shadow: none !important; border: none !important; margin: 0 !important; width: 100% !important; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="padding: 15px; text-align: center; background: #f3f4f6;">
        <button onclick="window.print()" style="padding: 8px 16px; cursor: pointer;">Cetak Dokumen</button>
        <button onclick="window.close()" style="padding: 8px 16px; cursor: pointer;">Tutup</button>
    </div>

    <div class="surat-paper">
        <div class="surat-paper__kop">
            @if ($surat->kopSurat?->logo)
                <img src="{{ asset('storage/' . $surat->kopSurat->logo) }}" alt="Logo" class="surat-paper__logo">
            @endif
            <div class="surat-paper__company">
                <strong>{{ $surat->kopSurat->nama_pt ?? 'PT Inovindo Digital Media' }}</strong>
                <span>{{ $surat->kopSurat->alamat_pt ?? '' }}</span>
                @if ($surat->kopSurat && ($surat->kopSurat->telepon || $surat->kopSurat->email || $surat->kopSurat->website))
                    <span class="surat-paper__contact">
                        {{ collect([
                            $surat->kopSurat->telepon ? 'Telp: ' . $surat->kopSurat->telepon : null,
                            $surat->kopSurat->email,
                            $surat->kopSurat->website,
                        ])->filter()->implode(' &middot; ') }}
                    </span>
                @endif
            </div>
        </div>

        <hr class="surat-paper__divider">

        <div class="surat-paper__meta">
            <span>Nomor: {{ $surat->nomor_surat }}</span>
            <span>{{ $surat->tanggal_surat->translatedFormat('d F Y') }}</span>
        </div>

        <div class="surat-paper__title">{{ $surat->template->nama_template }}</div>

        <div class="surat-paper__body">{!! nl2br(e($isiSuratRendered)) !!}</div>

        <div class="surat-paper__signature">
            <span>{{ $surat->kopSurat->nama_pt ?? 'PT Inovindo Digital Media' }},</span>
            <span>{{ $surat->tanggal_surat->translatedFormat('d F Y') }}</span>
            <div class="surat-paper__signature-space"></div>
            <span>{{ $surat->user->name }}</span>
        </div>
    </div>

</body>
</html>