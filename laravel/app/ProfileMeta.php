<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProfileMeta extends Model
{
    protected $table = 'wp_community_profile_meta';

    public $timestamps = [];

    protected $fillable = [
        'meta_key', 'meta_value'
    ];

    public function profile()
    {
        return $this->belongsTo('App\Profile');
    }
}
