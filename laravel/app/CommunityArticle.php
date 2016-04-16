<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommunityArticle extends Model
{

    use UuidTrait;

    public $incrementing = false;

    protected $table = 'community_articles';

    protected $fillable = [
        'creator_id', 'title', 'content', 'visibility', 'slug'
    ];

    /**
     * @param $slug
     * @return mixed
     */
    public static function findBySlug($slug)
    {
        return CommunityArticle::where(['slug' => $slug])->firstOrFail();
    }

    public static function getUniqueSlug(\Illuminate\Database\Eloquent\Model $model, $value)
    {
        $slug = \Illuminate\Support\Str::slug($value);
        $slugCount = count($model->whereRaw("slug REGEXP '^{$slug}(-[0-9]+)?$' and id != '{$model->id}'")->get());

        return ($slugCount > 0) ? "{$slug}-{$slugCount}" : $slug;
    }

    public function attachments()
    {
        return $this->hasMany('App\CommunityArticleAttachment', 'article_id');
    }

    public function community()
    {
        return $this->belongsTo('App\Community');
    }
}
