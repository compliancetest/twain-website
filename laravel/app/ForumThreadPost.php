<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ForumThreadPost extends Model
{

    use UuidTrait;

    public $incrementing = false;

    protected $table = 'community_forum_posts';

    protected $fillable = [
        'content', 'author_id'
    ];

    public function thread()
    {
        return $this->belongsTo('\App\ForumThread');
    }

    public function user()
    {
        return $this->belongsTo('\App\User', 'author_id');
    }
}
