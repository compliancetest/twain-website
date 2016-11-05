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
            if(!empty($model->title)) {
                $model->slug = self::getUniqueSlug($model, $model->title);
            } else {
                $fullName = $model->name;
                if(isset($model->version_major)) {
                    $fullName .= ' v' . (string)$model->version_major . '-' . (string)$model->version_minor;
                    if ($model->version_patch) {
                        $fullName .= '-' . (string)$model->version_patch;
                    }
                } else {
                    $fullName .= ' v' . $model->version;
                }
                $model->full_name = $fullName;
                $model->slug = self::getUniqueSlug($model, $fullName);
                if(isset($model->minor_family_mark)) {
                    $model->minor_family_mark = $model->id;
                    $model->major_family_mark = $model->id;
                }
            }
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