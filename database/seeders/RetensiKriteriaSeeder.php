<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RetensiKriteria;

class RetensiKriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RetensiKriteria::insert([
            ['kode' => 'K1', 'nama_kriteria' => 'Nilai Guna Dokumen', 'bobot' => 0.4, 'tipe' => 'benefit', 'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'K2', 'nama_kriteria' => 'Sifat / Kerahasiaan Surat', 'bobot' => 0.3, 'tipe' => 'benefit', 'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'K3', 'nama_kriteria' => 'Masa Simpan / Umur Surat', 'bobot' => 0.2, 'tipe' => 'cost', 'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'K4', 'nama_kriteria' => 'Frekuensi Akses / Download', 'bobot' => 0.1, 'tipe' => 'benefit', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
