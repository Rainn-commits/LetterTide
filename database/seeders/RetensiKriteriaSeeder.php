<?php

namespace Database\Seeders;

use App\Services\RetensiScoringService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RetensiKriteriaSeeder extends Seeder
{
    public function run(RetensiScoringService $service): void
    {
        foreach (DB::table('unit_kerja')->pluck('id') as $unitId) {
            $service->siapkanUnit((int) $unitId);
        }
    }
}
