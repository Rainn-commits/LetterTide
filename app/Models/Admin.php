<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    public function role()
    {
        return$this->belongsTo(Role::class);
    }
}
