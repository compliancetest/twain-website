<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class PostMeta extends Model
{

    protected $table = 'wp_postmeta';

    protected $primaryKey = 'meta_id';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo('App\Post');
    }

}
