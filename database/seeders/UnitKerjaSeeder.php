<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitKerjaSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['kode' => 'PUSAT',   'nama' => 'Kantor Pusat'],
            ['kode' => 'WEB',     'nama' => 'Inovindo Web'],
            ['kode' => 'ACADEMY', 'nama' => 'Inovindo Academy'],
        ];

        foreach ($units as $unit) {
            DB::table('unit_kerja')->updateOrInsert(
                ['kode' => $unit['kode']],
                ['nama' => $unit['nama'], 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
