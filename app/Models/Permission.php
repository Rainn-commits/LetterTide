<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['nama_permission', 'kode_permission'];
    public $timestamps = false;

    public const TIDAK_UNTUK_SUPER_ADMIN = [
        'surat.create',
        'arsip.manage',
        'arsip.unit',
        'surat.ttd',
        'retensi.evaluate',
        'retensi.manage',
    ];

    public const KETERGANTUNGAN = [
        'arsip.unit'     => ['arsip.manage'],
        'surat.ttd'      => ['arsip.manage'],
        'retensi.manage' => ['retensi.evaluate'],
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    /**
     * @param  array<int|string> $idPermission
     * @return array<int>
     */
    public static function rapikanUntukRole(array $idPermission, string $namaRole): array
    {
        $kodeKeId = static::query()->pluck('id', 'kode_permission');
        $idKeKode = $kodeKeId->flip();

        $kode = collect($idPermission)
            ->map(fn ($id) => $idKeKode[(int) $id] ?? null)
            ->filter()
            ->values()
            ->all();

        foreach (self::KETERGANTUNGAN as $syarat => $ikut) {
            if (in_array($syarat, $kode, true)) {
                $kode = array_merge($kode, $ikut);
            }
        }

        if ($namaRole === 'Super Admin') {
            $kode = array_diff($kode, self::TIDAK_UNTUK_SUPER_ADMIN);
        }

        return collect(array_unique($kode))
            ->map(fn ($k) => $kodeKeId[$k] ?? null)
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
    }
}
