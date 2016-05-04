<?php

namespace App;


trait SlugTrait
{
    /**
     * Boot the slug trait for the model.
     *
     * @return void
     */
    public static function bootSlugTrait()
    {
        static::creating(function($model) {
                $model->slug = self::getUniqueSlug($model, $model->title);
            });
    }

    public static function getUniqueSlug(\Illuminate\Database\Eloquent\Model $model, $value)
    {
        $slug = \Illuminate\Support\Str::slug($value);
        $slugCount = count($model->whereRaw("slug REGEXP '^{$slug}(-[0-9]+)?$' and id != '{$model->id}'")->get());

        return ($slugCount > 0) ? "{$slug}-{$slugCount}" : $slug;
    }

    public static function findBySlug($slug)
    {
        return self::where(['slug' => $slug])->firstOrFail();
    }
}