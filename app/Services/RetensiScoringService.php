<?php

namespace App\Services;

use App\Models\EvaluasiRetensi;
use App\Models\RetensiKriteria;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class RetensiScoringService
{
    private const SKALA_NILAI_GUNA = [
        'Hukum'         => 4,
        'Keuangan'      => 3,
        'Operasional'   => 2,
        'Pemberitahuan' => 1,
    ];

    private const SKALA_KERAHASIAAN = [
        'Sangat Rahasia' => 3,
        'Rahasia'        => 2,
        'Biasa/Terbuka'  => 1,
    ];

    private const AMBANG_PERMANEN     = 0.70;
    private const AMBANG_SIMPAN_LOKAL = 0.40;

    private const URUTAN_KRITERIA = ['K1', 'K2', 'K3', 'K4'];

    public function __construct(private TopsisCalculator $topsis = new TopsisCalculator())
    {
    }

    public function evaluasiMassal(): int
    {
        $hitung = $this->jalankan();

        if ($hitung === null) {
            return 0;
        }

        foreach ($hitung['items'] as $item) {
            $id = $item->id;
            $ci = $hitung['topsis']['ci'][$id];

            $item->update([
                'skor_total'         => round($ci, 6),
                'd_plus'             => $hitung['topsis']['d_plus'][$id],
                'd_minus'            => $hitung['topsis']['d_minus'][$id],
                'status_rekomendasi' => $this->klasifikasikan($ci),
                'status_persetujuan' => 'Menunggu Validasi',
                'dievaluasi_pada'    => now(),
                'dievaluasi_oleh'    => Auth::id(),
            ]);
        }

        return $hitung['items']->count();
    }

    public function detail(): ?array
    {
        return $this->jalankan();
    }

    private function jalankan(): ?array
    {
        $kriteria = RetensiKriteria::all()->keyBy('kode');

        $items = EvaluasiRetensi::with('surat')
            ->whereHas('surat')
            ->whereNotNull('nilai_guna')
            ->whereNotNull('kerahasiaan')
            ->get();

        if ($items->isEmpty()) {
            return null;
        }

        $bobot = [];
        $tipe = [];
        foreach (self::URUTAN_KRITERIA as $kode) {
            $bobot[] = (float) ($kriteria[$kode]->bobot ?? 0);
            $tipe[] = $kriteria[$kode]->tipe ?? 'benefit';
        }

        $jumlah = array_sum($bobot);
        if ($jumlah > 0) {
            $bobot = array_map(fn ($b) => $b / $jumlah, $bobot);
        } else {
            $bobot = array_fill(0, count($bobot), 1 / count($bobot));
        }

        $matriks = [];
        foreach ($items as $item) {
            $umurHari = Carbon::parse($item->surat->tanggal_surat)->diffInDays(now());

            $matriks[$item->id] = [
                self::SKALA_NILAI_GUNA[$item->nilai_guna] ?? 1,
                self::SKALA_KERAHASIAAN[$item->kerahasiaan] ?? 1,
                $umurHari,
                $item->surat->jumlah_akses ?? 0,
            ];
        }

        return [
            'items'    => $items,
            'kriteria' => $kriteria,
            'bobot'    => $bobot,
            'tipe'     => $tipe,
            'matriks'  => $matriks,
            'topsis'   => $this->topsis->hitung($matriks, $bobot, $tipe),
        ];
    }

    private function klasifikasikan(float $ci): string
    {
        if ($ci >= self::AMBANG_PERMANEN) {
            return 'Permanen';
        }

        if ($ci >= self::AMBANG_SIMPAN_LOKAL) {
            return 'Simpan Lokal';
        }

        return 'Musnah';
    }
}
