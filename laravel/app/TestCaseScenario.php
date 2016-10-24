<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestCaseScenario extends Model
{
    use UuidTrait;

    public $table = 'test_cases_scenarios';

    public $incrementing = false;

    protected $fillable = ['test_suites_scenario_id'];

    public function testCase()
    {
        return $this->belongsTo('\App\LaravelTestCase', 'test_case_id');
    }
}
