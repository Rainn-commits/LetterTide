<?php

namespace App\Models;

use App\Models\Concerns\BerdasarkanUnitKerja;
use Illuminate\Database\Eloquent\Model;

class RetensiKriteria extends Model
{
    use BerdasarkanUnitKerja;

    protected $table = 'retensi_kriteria';
    protected $fillable = ['kode', 'nama_kriteria', 'bobot', 'tipe', 'unit_kerja_id'];
}
