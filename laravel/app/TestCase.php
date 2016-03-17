<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class TestCase extends Model
{

    protected $table = 'wp_testcase';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_email', 'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];
}
