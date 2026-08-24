<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RetensiKriteria extends Model
{
    protected $table = 'retensi_kriteria';
    protected $fillable = ['kode', 'nama_kriteria', 'bobot', 'tipe'];
}
