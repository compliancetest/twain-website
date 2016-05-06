<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Post extends Model
{
    protected $table = 'wp_posts';

    protected $primaryKey = 'ID';

    protected $fillable = [
        'post_title',
        'post_name',
        'post_type',
        'post_status',
        'post_author',
        'post_date',
        'comment_status',
        'ping_status'
    ];

    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function postmeta()
    {
        return $this->hasMany('App\PostMeta');
    }

    public static function getCommunityTestSuites($communityId)
    {
        return DB::table('wp_posts')
            ->join('wp_postmeta', function($join) use ($communityId){
                $join->on('wp_postmeta.post_id', '=', 'wp_posts.ID')
                     ->where('meta_key', '=', 'community_id')
                    ->where('meta_value', '=', $communityId);
            })
            ->get();
    }

    public function meta()
    {
        return $this->hasMany('App\PostMeta');
    }

    public static function getUniquePostName(\Illuminate\Database\Eloquent\Model $model, $value)
    {
        $slug = \Illuminate\Support\Str::slug($value);
        $slugCount = count($model->whereRaw("post_name REGEXP '^{$slug}(-[0-9]+)?$' and id != '{$model->id}'")->get());

        return ($slugCount > 0) ? "{$slug}-{$slugCount}" : $slug;
    }
}
