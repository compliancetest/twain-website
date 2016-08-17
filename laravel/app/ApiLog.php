<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    protected $fillable = [
        'user_id', 'ip_address', 'request_type', 'uri',
        'request', 'response'
    ];
}
