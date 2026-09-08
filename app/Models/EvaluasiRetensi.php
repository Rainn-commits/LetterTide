<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluasiRetensi extends Model
{
    protected $table = 'evaluasi_retensi';

    protected $fillable = [
        'surat_id', 'nilai_guna', 'kerahasiaan',
        'skor_total', 'status_rekomendasi',
        'status_persetujuan', 'divalidasi_oleh', 'divalidasi_pada',
        'dievaluasi_oleh', 'dievaluasi_pada',
    ];

    protected $casts = [
        'dievaluasi_pada' => 'datetime',
        'divalidasi_pada' => 'datetime',
    ];

    public function surat()
    {
        return $this->belongsTo(Surat::class);
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'dievaluasi_oleh');
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'divalidasi_oleh');
    }
}
