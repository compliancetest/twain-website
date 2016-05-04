<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ForumThreadRead extends Model
{
    use UuidTrait;

    public $incrementing = false;

    protected $table = 'community_forum_threads_read';

    protected $fillable = [
        'user_id', 'thread_id'
    ];
}
