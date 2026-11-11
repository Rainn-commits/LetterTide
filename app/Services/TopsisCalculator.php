<?php

namespace App\Services;

use InvalidArgumentException;

class TopsisCalculator
{
    /**
     * @param array<int|string, array<int, float>> $matriks Alternatif => nilai per kriteria
     * @param array<int, float>                    $bobot   Bobot tiap kriteria (jumlah = 1)
     * @param array<int, string>                   $tipe    'benefit' | 'cost' tiap kriteria
     */
    public function hitung(array $matriks, array $bobot, array $tipe): array
    {
        $bobot = array_values($bobot);
        $tipe = array_values($tipe);
        $m = count($bobot);

        if (empty($matriks)) {
            throw new InvalidArgumentException('Matriks keputusan kosong.');
        }
        if (count($tipe) !== $m) {
            throw new InvalidArgumentException('Jumlah tipe kriteria harus sama dengan jumlah bobot.');
        }
        foreach ($tipe as $t) {
            if (!in_array($t, ['benefit', 'cost'], true)) {
                throw new InvalidArgumentException("Tipe kriteria tidak dikenal: {$t}");
            }
        }
        foreach ($matriks as $baris) {
            if (count($baris) !== $m) {
                throw new InvalidArgumentException('Setiap alternatif harus punya nilai untuk semua kriteria.');
            }
        }

        $pembagi = array_fill(0, $m, 0.0);
        foreach ($matriks as $baris) {
            foreach (array_values($baris) as $j => $x) {
                $pembagi[$j] += $x * $x;
            }
        }
        $pembagi = array_map('sqrt', $pembagi);

        $r = [];
        $v = [];
        foreach ($matriks as $kunci => $baris) {
            foreach (array_values($baris) as $j => $x) {
                $r[$kunci][$j] = $pembagi[$j] > 0 ? $x / $pembagi[$j] : 0.0;
                $v[$kunci][$j] = $bobot[$j] * $r[$kunci][$j];
            }
        }

        $idealPositif = [];
        $idealNegatif = [];
        for ($j = 0; $j < $m; $j++) {
            $kolom = array_column($v, $j);
            $maks = max($kolom);
            $min = min($kolom);
            $idealPositif[$j] = $tipe[$j] === 'benefit' ? $maks : $min;
            $idealNegatif[$j] = $tipe[$j] === 'benefit' ? $min : $maks;
        }

        $dPlus = [];
        $dMinus = [];
        $ci = [];
        foreach ($v as $kunci => $baris) {
            $sp = 0.0;
            $sn = 0.0;
            for ($j = 0; $j < $m; $j++) {
                $sp += ($baris[$j] - $idealPositif[$j]) ** 2;
                $sn += ($baris[$j] - $idealNegatif[$j]) ** 2;
            }
            $dPlus[$kunci] = sqrt($sp);
            $dMinus[$kunci] = sqrt($sn);

            $total = $dPlus[$kunci] + $dMinus[$kunci];
            $ci[$kunci] = $total > 1e-12 ? $dMinus[$kunci] / $total : 0.5;
        }

        return [
            'pembagi'       => $pembagi,
            'normalisasi'   => $r,
            'terbobot'      => $v,
            'ideal_positif' => $idealPositif,
            'ideal_negatif' => $idealNegatif,
            'd_plus'        => $dPlus,
            'd_minus'       => $dMinus,
            'ci'            => $ci,
        ];
    }
}
