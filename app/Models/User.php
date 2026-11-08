<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    private ?bool $cacheGlobal = null;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'unit_kerja_id',
        'is_aktif',
        'can_sign',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_aktif' => 'boolean',
            'can_sign' => 'boolean',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_kerja_id');
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role?->nama_role, $roles, true);
    }

    public function punyaPermission(string $kode): bool
    {
        return (bool) $this->role?->permissions->contains('kode_permission', $kode);
    }

    public function punyaSalahSatuPermission(array $kode): bool
    {
        foreach ($kode as $k) {
            if ($this->punyaPermission($k)) {
                return true;
            }
        }

        return false;
    }

    public function isGlobal(): bool
    {
        if ($this->cacheGlobal === null) {
            $this->cacheGlobal = $this->punyaPermission('role.manage');
        }

        return $this->cacheGlobal;
    }

    public function scopeDiUnitSaya(Builder $query): Builder
    {
        /** @var User|null $saya */
        $saya = Auth::user();

        if (!$saya || $saya->isGlobal()) {
            return $query;
        }

        return $saya->unit_kerja_id
            ? $query->where('users.unit_kerja_id', $saya->unit_kerja_id)
            : $query->whereRaw('1 = 0');
    }
}
