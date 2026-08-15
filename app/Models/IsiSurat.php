<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IsiSurat extends Model
{
    protected $table = 'isi_surat';
    public $timestamps = false;

    protected $fillable = ['surat_id', 'nama_field', 'nilai_field'];

    public function surat()
    {
        return $this->belongsTo(Surat::class);
    }
}
