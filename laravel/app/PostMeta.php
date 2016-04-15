<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class PostMeta extends Model
{

    protected $table = 'wp_postmeta';

    protected $primaryKey = 'meta_id';

    protected $fillable = [
        'meta_key', 'meta_value'
    ];

    public $timestamps = false;

    public function post()
    {
        return $this->belongsTo('App\Post');
    }

}
