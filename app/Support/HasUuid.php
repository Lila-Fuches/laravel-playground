<?php

namespace Support;

use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

trait HasUuid
{
    public static function bootHasUuid(): void
    {
        self::creating(function (Model $model) {
            if (! is_null($model->uuid)) {
                return $model;
            }

            $model->uuid = Uuid::uid4();
        });
    }

    public function scopeWhereUuid(Builder $builder, string $uuid): Builder
    {
        return $builder->where('uuid', $uuid);
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function getRouteKey(): string
    {
        return $this->uuid;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }
}
