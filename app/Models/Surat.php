<?php

namespace App\Models;

use App\Models\Concerns\BerdasarkanUnitKerja;
use Illuminate\Database\Eloquent\Model;

class Surat extends Model
{
    use BerdasarkanUnitKerja;

    protected $table = 'surat';

    protected $fillable = [
        'template_id',
        'user_id',
        'ttd_by_user_id',
        'kop_surat_id',
        'nomor_surat',
        'lampiran',
        'jenis',
        'pengirim',
        'perihal',
        'tanggal_surat',
        'file_arsip',
        'jumlah_akses',
        'status',
        'unit_kerja_id',
    ];

    public function evaluasiRetensi()
    {
        return $this->hasOne(EvaluasiRetensi::class);
    }

    protected $casts = [
        'tanggal_surat' => 'date',
    ];

    public function template()
    {
        return $this->belongsTo(TemplateSurat::class, 'template_id')->withoutGlobalScopes();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kopSurat()
    {
        return $this->belongsTo(PengaturanKopSurat::class, 'kop_surat_id')->withoutGlobalScopes();
    }

    public function isiSurat()
    {
        return $this->hasMany(IsiSurat::class);
    }

    public function penandatangan()
    {
        return $this->belongsTo(User::class, 'ttd_by_user_id');
    }
}
