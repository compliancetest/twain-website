<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VerifyRequest extends Model
{
    use UuidTrait;

    public $incrementing = false;

    protected $fillable = [
        'test_plan_id', 'requestor_id', 'transactions', 'assignee_id',
        'product_id', 'test_suite_id'
    ];

    public function getUserRequests()
    {
        
    }
}
