<?php

namespace App\Services;

use App\Models\EvaluasiRetensi;
use App\Models\RetensiKriteria;
use App\Models\Surat;
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

    public function evaluasiMassal(): int
    {
        $bobotKriteria = RetensiKriteria::all()->keyBy('kode');

        $evaluasiSiap = EvaluasiRetensi::with('surat')
            ->whereNotNull('nilai_guna')
            ->whereNotNull('kerahasiaan')
            ->get();

        if ($evaluasiSiap->isEmpty()) {
            return 0;
        }

        $rawK3 = [];
        $rawK4 = [];

        foreach ($evaluasiSiap as $item) {
            $umurHari = Carbon::parse($item->surat->tanggal_surat)->diffInDays(now());
            $rawK3[$item->id] = $umurHari;
            $rawK4[$item->id] = $item->surat->jumlah_akses;
        }

        $minK3 = min($rawK3); $maxK3 = max($rawK3);
        $minK4 = min($rawK4); $maxK4 = max($rawK4);

        foreach ($evaluasiSiap as $item) {
            $skorK1 = self::SKALA_NILAI_GUNA[$item->nilai_guna] ?? 1;
            $skorK2 = self::SKALA_KERAHASIAAN[$item->kerahasiaan] ?? 1;

            $normK1 = $skorK1 / max(self::SKALA_NILAI_GUNA);
            $normK2 = $skorK2 / max(self::SKALA_KERAHASIAAN);

            $normK3 = $this->normalisasiCost($rawK3[$item->id], $minK3, $maxK3);

            $normK4 = $this->normalisasiBenefit($rawK4[$item->id], $minK4, $maxK4);

            $skorTotal =
                ($bobotKriteria['K1']->bobot ?? 0) * $normK1 +
                ($bobotKriteria['K2']->bobot ?? 0) * $normK2 +
                ($bobotKriteria['K3']->bobot ?? 0) * $normK3 +
                ($bobotKriteria['K4']->bobot ?? 0) * $normK4;

            $item->update([
                'skor_total'         => round($skorTotal, 4),
                'status_rekomendasi' => $this->klasifikasikan($skorTotal),
                'dievaluasi_pada'    => now(),
                'dievaluasi_oleh'    => Auth::id(),
            ]);
        }

        return $evaluasiSiap->count();
    }

    private function normalisasiBenefit(float $nilai, float $min, float $max): float
    {
        if ($max == $min) return 1;
        return ($nilai - $min) / ($max - $min);
    }

    private function normalisasiCost(float $nilai, float $min, float $max): float
    {
        if ($max == $min) return 1;
        return ($max - $nilai) / ($max - $min);
    }

    private function klasifikasikan(float $skor): string
    {
        if ($skor >= self::AMBANG_PERMANEN) {
            return 'Permanen';
        }

        if ($skor >= self::AMBANG_SIMPAN_LOKAL) {
            return 'Simpan Lokal';
        }

        return 'Musnah';
    }
}
