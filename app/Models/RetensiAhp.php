<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RetensiAhp extends Model
{
    protected $table = 'retensi_ahp';

    protected $fillable = [
        'matriks', 'lambda_max', 'ci', 'ri', 'cr', 'diubah_oleh',
    ];

    protected $casts = [
        'matriks' => 'array',
    ];
}
