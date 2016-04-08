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
        'user_email', 'user_pass',
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

    public function getFullName()
    {
        return cp_get_user_fullname($this->ID);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany('App\CommunityMembers', 'user_id');
    }

    public function confirmedSubscriptions()
    {
        return $this->subscriptions()->where(['is_confirmed' => 1])->get();
    }
}
