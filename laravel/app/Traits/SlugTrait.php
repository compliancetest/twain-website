<?php

namespace App;


use Illuminate\Support\Str;

trait SlugTrait
{
//    /**
//     * Boot the slug trait for the model.
//     *
//     * @return void
//     */
//    public static function bootSlugTrait()
//    {
//        static::creating(function($model) {
//            if(!empty($model->title)) {
//                $model->slug = self::getUniqueSlug($model, $model->title);
//            } else {
//                $fullName = $model->name;
//                if(isset($model->version_major)) {
//                    $fullName .= ' v' . (string)$model->version_major . '-' . (string)$model->version_minor;
//                    if ($model->version_patch) {
//                        $fullName .= '-' . (string)$model->version_patch;
//                    }
//                } else {
//                    $fullName .= ' v' . $model->version;
//                }
//                $model->full_name = $fullName;
//                $model->slug = self::getUniqueSlug($model, $fullName);
//                if(isset($model->minor_family_mark)) {
//                    $model->minor_family_mark = $model->id;
//                    $model->major_family_mark = $model->id;
//                }
//            }
//        });
//    }

    public static function generateCaseSuiteFullName($request)
    {
        $fullName = $request->get('name');
        $fullName .= ' v' . (string)$request->get('version_major') . '-' . (string)$request->get('version_minor');
        if ($request->get('version_patch')) {
            $fullName .= '-' . (string)$request->get('version_patch');
        }
        return $fullName;
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
}