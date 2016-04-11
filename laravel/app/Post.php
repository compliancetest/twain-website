<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Post extends Model
{
    protected $table = 'wp_posts';

    protected $primaryKey = 'ID';

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
}
