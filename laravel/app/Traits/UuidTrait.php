<?php

namespace App;

use Ramsey\Uuid\Uuid;

trait UuidTrait
{
    /**
     * Boot the Uuid trait for the model.
     *
     * @return void
     */
    public static function bootUuidTrait()
    {
        static::creating(function($model) {
                $model->incrementing = false;
                $model->{$model->getKeyName()} = Uuid::uuid4()->__toString();
            });
    }
}