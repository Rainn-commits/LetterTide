<?php

namespace Database\Seeders;

use App\Models\RetensiAhp;
use App\Models\RetensiKriteria;
use App\Services\AhpCalculator;
use Illuminate\Database\Seeder;

class RetensiKriteriaSeeder extends Seeder
{
    private const PERBANDINGAN_AWAL = [2, 3, 5, 2, 3, 2];

    public function run(): void
    {
        $kriteria = [
            ['kode' => 'K1', 'nama_kriteria' => 'Nilai Guna Dokumen',      'tipe' => 'benefit'],
            ['kode' => 'K2', 'nama_kriteria' => 'Sifat / Kerahasiaan Surat', 'tipe' => 'benefit'],
            ['kode' => 'K3', 'nama_kriteria' => 'Masa Simpan / Umur Surat',  'tipe' => 'cost'],
            ['kode' => 'K4', 'nama_kriteria' => 'Frekuensi Akses / Download', 'tipe' => 'benefit'],
        ];

        $ahp = new AhpCalculator();
        $matriks = $ahp->bangunMatriks(count($kriteria), self::PERBANDINGAN_AWAL);
        $hasil = $ahp->hitung($matriks);

        foreach ($kriteria as $i => $k) {
            RetensiKriteria::updateOrCreate(
                ['kode' => $k['kode']],
                $k + ['bobot' => $hasil['bobot'][$i]]
            );
        }

        RetensiAhp::updateOrCreate(['id' => 1], [
            'matriks'    => $matriks,
            'lambda_max' => $hasil['lambda_max'],
            'ci'         => $hasil['ci'],
            'ri'         => $hasil['ri'],
            'cr'         => $hasil['cr'],
        ]);
    }
}
