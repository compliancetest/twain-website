<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ForumThread extends Model
{

    use UuidTrait, SlugTrait;

    public $incrementing = false;

    protected $table = 'community_forum_threads';

    protected $fillable = [
        'title', 'content', 'author_id'
    ];

    public function community()
    {
        return $this->belongsTo('\App\Community');
    }

    public function replies()
    {
        return $this->hasMany('\App\ForumThreadPost', 'thread_id');
    }

    public function user()
    {
        return $this->belongsTo('\App\User', 'author_id');
    }
}
