<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestingDetail extends Model
{

    use UuidTrait;

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'user_id', 'product_id', 'test_case_id', 'test_suite_id', 'start_time', 'end_time', 'is_running'
    ];
}
