<?php

namespace App;


use Illuminate\Support\Str;

trait ForumSlugTrait
{
    /**
     * Boot the slug trait for the model.
     *
     * @return void
     */
    public static function bootForumSlugTrait()
    {
        static::creating(function($model) {
            $model->slug = self::getUniqueSlug($model, $model->title);
        });
    }

    public function generateSlug($fullName)
    {
        return Str::slug($fullName);
    }

    public static function findBySlug($slug)
    {
        return self::where(['slug' => $slug])->first();
    }

    public static function findBySlugOrFail($slug)
    {
        return self::where(['slug' => $slug])->firstOrFail();
    }

    public static function getUniqueSlug(\Illuminate\Database\Eloquent\Model $model, $value)
    {
        $slug = \Illuminate\Support\Str::slug($value);
        $slugCount = count($model->whereRaw("slug REGEXP '^{$slug}(-[0-9]+)?$' and id != '{$model->id}'")->get());

        return ($slugCount > 0) ? "{$slug}-{$slugCount}" : $slug;
    }
}