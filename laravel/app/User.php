<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{

    protected $table = 'wp_users';

    protected $primaryKey = 'ID';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_email', 'user_pass', 'user_login',
    ];

    protected $username = 'user_email';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'user_pass',
    ];

    public function getAuthPassword()
    {
        return $this->user_pass;
    }

    public function organisation()
    {
        return $this->belongsToMany('App\Organisation', 'wp_organisations_members');
    }
}
