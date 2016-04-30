<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WpOptions extends Model
{
    protected $table = 'wp_options';

    protected $fillable = [
        'option_name', 'option_value'
    ];

    public $timestamps = [];
}
