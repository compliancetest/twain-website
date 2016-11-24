<?php

namespace App;


use Illuminate\Support\Str;

trait SlugTrait
{

    public static function generateCaseSuiteFullName($request)
    {
        $fullName = $request->get('name');
        $fullName .= ' v' . (string)$request->get('version_major') . '.' . (string)$request->get('version_minor');
        if ($request->get('version_patch')) {
            $fullName .= '.' . (string)$request->get('version_patch');
        }
        return $fullName;
    }

    public function generateSlug($fullName)
    {
        return Str::slug(str_replace([' ', '.'], '-', $fullName));
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