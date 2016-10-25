<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestCaseCapability extends Model
{
    use UuidTrait;

    public $table = 'test_cases_capabilities';

    public $incrementing = false;

    protected $fillable = ['capability'];

    public function testCase()
    {
        return $this->belongsTo('\App\LaravelTestCase', 'test_case_id');
    }
}
