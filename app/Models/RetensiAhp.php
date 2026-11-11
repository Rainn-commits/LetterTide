<?php

namespace App\Models;

use App\Models\Concerns\BerdasarkanUnitKerja;
use Illuminate\Database\Eloquent\Model;

class RetensiAhp extends Model
{
    use BerdasarkanUnitKerja;

    protected $table = 'kriteria_ahp';

    protected $fillable = [
        'unit_kerja_id', 'matriks', 'lambda_max', 'ci', 'ri', 'cr', 'diubah_oleh',
    ];

    protected $casts = [
        'matriks' => 'array',
    ];
}
