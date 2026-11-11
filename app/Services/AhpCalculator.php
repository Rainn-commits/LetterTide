<?php

namespace App\Services;

use InvalidArgumentException;

class AhpCalculator
{
    public const RANDOM_INDEX = [
        1 => 0.00, 2 => 0.00, 3 => 0.58, 4 => 0.90, 5 => 1.12,
        6 => 1.24, 7 => 1.32, 8 => 1.41, 9 => 1.45, 10 => 1.49,
    ];

    public const BATAS_CR = 0.10;

    public function hitung(array $matriks): array
    {
        $matriks = array_values(array_map('array_values', $matriks));
        $n = count($matriks);

        $this->validasi($matriks, $n);

        $w = array_fill(0, $n, 1 / $n);
        for ($iter = 0; $iter < 10000; $iter++) {
            $next = $this->kali($matriks, $w);
            $sum = array_sum($next);
            $next = array_map(fn ($v) => $v / $sum, $next);

            $selisih = 0.0;
            for ($i = 0; $i < $n; $i++) {
                $selisih = max($selisih, abs($next[$i] - $w[$i]));
            }
            $w = $next;

            if ($selisih < 1e-14) {
                break;
            }
        }

        $aw = $this->kali($matriks, $w);
        $lambdaMax = 0.0;
        for ($i = 0; $i < $n; $i++) {
            $lambdaMax += $aw[$i] / $w[$i];
        }
        $lambdaMax /= $n;

        $ci = $n > 1 ? ($lambdaMax - $n) / ($n - 1) : 0.0;
        $ri = self::RANDOM_INDEX[$n] ?? end(self::RANDOM_INDEX);
        $cr = $ri > 0 ? $ci / $ri : 0.0;

        return [
            'n'          => $n,
            'bobot'      => $w,
            'lambda_max' => $lambdaMax,
            'ci'         => $ci,
            'ri'         => $ri,
            'cr'         => $cr,
            'konsisten'  => $cr <= self::BATAS_CR + 1e-9,
        ];
    }

    public function bangunMatriks(int $n, array $segitigaAtas): array
    {
        $perluan = $n * ($n - 1) / 2;
        if (count($segitigaAtas) !== $perluan) {
            throw new InvalidArgumentException("Dibutuhkan {$perluan} nilai perbandingan.");
        }

        $m = array_fill(0, $n, array_fill(0, $n, 1.0));
        $k = 0;
        for ($i = 0; $i < $n; $i++) {
            for ($j = $i + 1; $j < $n; $j++) {
                $nilai = (float) $segitigaAtas[$k++];
                $m[$i][$j] = $nilai;
                $m[$j][$i] = 1 / $nilai;
            }
        }

        return $m;
    }

    private function validasi(array $m, int $n): void
    {
        if ($n < 1) {
            throw new InvalidArgumentException('Matriks kosong.');
        }

        for ($i = 0; $i < $n; $i++) {
            if (count($m[$i]) !== $n) {
                throw new InvalidArgumentException('Matriks harus persegi.');
            }
            if (abs($m[$i][$i] - 1.0) > 1e-9) {
                throw new InvalidArgumentException('Diagonal matriks harus bernilai 1.');
            }
            for ($j = 0; $j < $n; $j++) {
                if ($m[$i][$j] <= 0) {
                    throw new InvalidArgumentException('Semua elemen harus > 0.');
                }
                if (abs($m[$i][$j] * $m[$j][$i] - 1.0) > 1e-6) {
                    throw new InvalidArgumentException('Matriks harus reciprocal (a_ij x a_ji = 1).');
                }
            }
        }
    }

    private function kali(array $m, array $v): array
    {
        $hasil = [];
        foreach ($m as $baris) {
            $s = 0.0;
            foreach ($baris as $j => $a) {
                $s += $a * $v[$j];
            }
            $hasil[] = $s;
        }

        return $hasil;
    }
}
