<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role_id', 'unit_kerja_id', 'is_aktif', 'can_sign'])]
#[Hidden(['password', 'remember_token'])]

class User extends Authenticatable
{
    use HasFactory, Notifiable;

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
        return in_array($this->role?->nama_role, $roles);
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
        return $this->punyaPermission('role.manage');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_aktif' => 'boolean',
            'can_sign' => 'boolean',
        ];
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'unit_kerja_id',
        'is_aktif',
        'can_sign',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
        'can_sign' => 'boolean',
    ];
}
