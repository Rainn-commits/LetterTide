<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['nama_permission', 'kode_permission'];
    public $timestamps = false;

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }
}