<?php

namespace App\Models\Concerns;

use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * @mixin Model
 * @method static void addGlobalScope(\Closure|string|\Illuminate\Database\Eloquent\Scope $scope, \Closure $implementation = null)
 * @method static void creating(\Closure|string $callback)
 */
trait BerdasarkanUnitKerja
{
    protected static function bootBerdasarkanUnitKerja(): void
    {
        static::addGlobalScope('unit_kerja', function (Builder $builder) {
            /** @var User|null $user */
            $user = Auth::user();

            if (!$user || $user->isGlobal()) {
                return;
            }

            $kolom = $builder->getModel()->getTable() . '.unit_kerja_id';

            $user->unit_kerja_id
                ? $builder->where($kolom, $user->unit_kerja_id)
                : $builder->whereRaw('1 = 0');
        });

        static::creating(function (Model $model) {
            if ($model->getAttribute('unit_kerja_id')) {
                return;
            }

            /** @var User|null $user */
            $user = Auth::user();

            if ($user && $user->unit_kerja_id) {
                $model->setAttribute('unit_kerja_id', $user->unit_kerja_id);
            }
        });
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_kerja_id');
    }
}
