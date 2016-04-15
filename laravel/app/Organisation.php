<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Organisation extends Model
{
    protected $table = 'wp_organisations';

    public function members()
    {
        return $this->belongsTo('App\User', 'wp_organisations_members');
    }
}
