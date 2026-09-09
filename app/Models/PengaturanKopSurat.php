<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanKopSurat extends Model
{
    protected $table = 'pengaturan_kop_surat';

    public $timestamps = false;

    protected $fillable = [
        'logo', 'nama_pt', 'alamat_pt', 'telepon', 'email', 'website', 'versi', 'created_at'
    ];

    public function surat()
    {
        return $this->hasMany(Surat::class, 'kop_surat_id');
    }
}
