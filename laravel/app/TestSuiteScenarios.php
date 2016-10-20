<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestSuiteScenarios extends Model
{
    use UuidTrait;

    public $table = 'test_suites_scenarios';

    public $incrementing = false;

    protected $fillable = ['code', 'description', 'sequence'];

    public function testSuite()
    {
        return $this->belongsTo('\App\LaravelTestSuite', 'test_suite_id');
    }
}
