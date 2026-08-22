<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAktivitas extends Model
{
    protected $table = 'log_aktivitas';
    public $timestamps = false;

    protected $fillable = ['user_id', 'aksi', 'modul', 'keterangan'];

    protected $attributes = [
        'created_at' => null,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function catat(string $aksi, string $modul, ?string $keterangan = null): void
    {
        self::create([
            'user_id'     => auth()->guard()->id(),
            'aksi'        => $aksi,
            'modul'       => $modul,
            'keterangan'  => $keterangan,
            'created_at'  => now(),
        ]);
    }
}
