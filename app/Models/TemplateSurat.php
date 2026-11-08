<?php

namespace App\Models;

use App\Models\Surat;
use App\Models\Concerns\BerdasarkanUnitKerja;
use Illuminate\Database\Eloquent\Model;

class TemplateSurat extends Model
{
    use BerdasarkanUnitKerja;

    protected $table = 'template_surat';

    protected $fillable = [
        'nama_template',
        'isi_template',
        'versi',
        'is_aktif',
        'unit_kerja_id',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    public function surat()
    {
       return $this->hasMany(Surat::class, 'template_id')->withoutGlobalScopes();
    }
}
