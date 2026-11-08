<?php

namespace App\Models;

use App\Models\Concerns\BerdasarkanUnitKerja;
use Illuminate\Database\Eloquent\Model;

class PengaturanKopSurat extends Model
{
    use BerdasarkanUnitKerja;

    protected $table = 'pengaturan_kop_surat';

    public $timestamps = false;

    protected $fillable = [
        'logo','nama_kop', 'nama_pt', 'alamat_pt', 'telepon', 'email', 'website', 'versi', 'is_aktif', 'created_at', 'unit_kerja_id'
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    public function surat()
    {
        return $this->hasMany(Surat::class, 'kop_surat_id')->withoutGlobalScopes();
    }
}
