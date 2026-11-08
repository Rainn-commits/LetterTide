<?php

namespace App\Models\Concerns;

use App\Models\UnitKerja;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BerdasarkanUnitKerja
{
    protected static function bootBerdasarkanUnitKerja(): void
    {
        static::addGlobalScope('unit_kerja', function (Builder $builder) {
            $user = Auth::user();

            if (!$user || $user->isGlobal()) {
                return;
            }

            $kolom = $builder->getModel()->getTable() . '.unit_kerja_id';

            $user->unit_kerja_id
                ? $builder->where($kolom, $user->unit_kerja_id)
                : $builder->whereRaw('1 = 0');
        });

        static::creating(function ($model) {
            if ($model->unit_kerja_id) {
                return;
            }

            $user = Auth::user();

            if ($user && $user->unit_kerja_id) {
                $model->unit_kerja_id = $user->unit_kerja_id;
            }
        });
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_kerja_id');
    }
}
