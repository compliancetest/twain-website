<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LaravelTestCase extends Model
{
    use UuidTrait;

    protected $table = 'test_cases';

    public $incrementing = false;

    protected $fillable = [
        'slug', 'name', 'version_major', 'version_minor', 'version_patch', 'description', 'full_name', 'tester_role',
        'harness_role', 'initiator', 'test_execution_profile_id', 'configuration_profile_id', 'outcome_type', 'is_optional', 'test_pattern',
        'wp_id', 'published_at', 'created_at', 'updated_at', 'status'
    ];

    public function testSuites()
    {
        return $this->belongsToMany('App\LaravelTestSuite', 'test_suite_test_case', 'test_case_id', 'test_suite_id');
    }

    public function conformanceLevels()
    {
        return $this->hasMany('\App\TestCaseConformanceLevel', 'test_case_id');
    }

    public function scenarios()
    {
        return $this->hasMany('\App\TestCaseScenario', 'test_case_id');
    }

     public function roles()
    {
        return $this->hasMany('\App\TestCaseRole', 'test_case_id');
    }

    public function samples()
    {
        return $this->hasMany('\App\TestCaseSample', 'test_case_id');
    }

    public function features()
    {
        return $this->hasMany('\App\TestCaseFeature', 'test_case_id');
    }

    public function capabilities()
    {
        return $this->hasMany('\App\TestCaseCapability', 'test_case_id');
    }

    public function steps()
    {
        return $this->hasMany('\App\TestCaseStep', 'test_case_id');
    }
}
