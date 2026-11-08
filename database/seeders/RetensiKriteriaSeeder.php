<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RetensiKriteriaSeeder extends Seeder
{
    public function run(): void
    {
        $bawaan = [
            ['kode' => 'K1', 'nama_kriteria' => 'Nilai Guna Dokumen',          'bobot' => 0.4, 'tipe' => 'benefit'],
            ['kode' => 'K2', 'nama_kriteria' => 'Sifat / Kerahasiaan Surat',   'bobot' => 0.3, 'tipe' => 'benefit'],
            ['kode' => 'K3', 'nama_kriteria' => 'Masa Simpan / Umur Surat',    'bobot' => 0.2, 'tipe' => 'cost'],
            ['kode' => 'K4', 'nama_kriteria' => 'Frekuensi Akses / Download',  'bobot' => 0.1, 'tipe' => 'benefit'],
        ];

        foreach (DB::table('unit_kerja')->pluck('id') as $unitId) {
            if (DB::table('retensi_kriteria')->where('unit_kerja_id', $unitId)->exists()) {
                continue;
            }

            foreach ($bawaan as $k) {
                DB::table('retensi_kriteria')->insert($k + [
                    'unit_kerja_id' => $unitId,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }
    }
}
