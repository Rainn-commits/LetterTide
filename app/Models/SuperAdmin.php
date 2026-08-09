<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuperAdmin extends Model
{
    public function role()
    {
        return$this->belongsTo(Role::class);
    }
}
