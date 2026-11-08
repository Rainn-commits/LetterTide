<?php

namespace App\Models;

use App\Models\Concerns\BerdasarkanUnitKerja;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LogAktivitas extends Model
{
    use BerdasarkanUnitKerja;

    protected $table = 'log_aktivitas';
    public $timestamps = false;

    protected $fillable = ['user_id', 'aksi', 'modul', 'keterangan', 'created_at', 'unit_kerja_id'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function catat(string $aksi, string $modul, ?string $keterangan = null): void
    {
        self::create([
            'user_id'    => Auth::id(),
            'aksi'       => $aksi,
            'modul'      => $modul,
            'keterangan' => $keterangan,
            'created_at' => now(),
        ]);
    }
}
