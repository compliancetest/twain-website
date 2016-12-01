<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestCaseFeature extends Model
{
    use UuidTrait;

    public $table = 'test_cases_features';

    public $incrementing = false;

    protected $fillable = ['test_suites_feature_id', 'test_case_id'];

    public function testCase()
    {
        return $this->belongsTo('\App\LaravelTestCase', 'test_case_id');
    }

    public function testSuiteFeature()
    {
        return $this->belongsTo('\App\TestSuiteFeatures', 'test_suites_feature_id');
    }
}
