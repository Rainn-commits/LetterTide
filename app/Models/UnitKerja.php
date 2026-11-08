<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitKerja extends Model
{
    protected $table = 'unit_kerja';

    protected $fillable = ['kode', 'nama'];

    public function users()
    {
        return $this->hasMany(User::class, 'unit_kerja_id');
    }
}
