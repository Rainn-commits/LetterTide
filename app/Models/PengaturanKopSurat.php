<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanKopSurat extends Model
{
     protected $table = 'pengaturan_kop_surat';

    protected $fillable = [
        'logo', 'nama_pt', 'alamat_pt', 'telepon', 'email', 'website', 'versi', 'is_aktif',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    public function surat()
    {
        return $this->hasMany(Surat::class, 'kop_surat_id');
    }
}
