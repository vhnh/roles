<?php

namespace Vhnh\Roles;

use Illuminate\Database\Eloquent\Builder;

trait HasRoles
{
    protected static $role = false;
    
    protected static function booted()
    {
        if (static::$role) {
            static::addGlobalScope('role', function (Builder $builder) {
                $builder->where('type', static::$role);
            });

            static::creating(function ($model) {
                $model->forceFill(['type' => static::$role]);
            });
        }
    }

    public function newInstance($attributes = [], $exists = false, $model =  null)
    {
        $model = $model
            ? new $model((array) $attributes)
            : new static((array) $attributes);

        $model->exists = $exists;

        $model->setConnection(
            $this->getConnectionName()
        );

        $model->setTable($this->getTable());

        $model->mergeCasts($this->casts);

        return $model;
    }

    public function newFromBuilder($attributes = [], $connection = null)
    {
        $attributes = (array) $attributes;

        $model = $this->newInstance([], true, $this->availableRoles[$attributes['type']]);

        $model->setRawAttributes($attributes, true);

        $model->setConnection($connection ?: $this->getConnectionName());

        $model->fireModelEvent('retrieved', false);

        return $model;
    }
}
