<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LaravelTestSuite extends Model
{
    use UuidTrait;

    protected $table = 'test_suites';

    public $incrementing = false;

    protected $fillable = [
        'community_id', 'slug', 'name', 'full_name', 'description', 'version_major', 'version_minor', 'version_patch',
        'short_name', 'issuer', 'revision_description', 'status', 'product_type', 'excerpt', 'minor_family_mark',
        'major_family_mark', 'wp_id', 'published_at', 'minor_family_mark', 'updated_at', 'created_at'
    ];

    public function testCases()
    {
        return $this->belongsToMany('App\LaravelTestCase', 'test_suite_test_case', 'test_suite_id', 'test_case_id');
    }

    public function types()
    {
        return $this->hasMany('\App\TestSuiteTypes', 'test_suite_id');
    }

    public function conformanceLevels()
    {
        return $this->hasMany('\App\TestSuiteConformanceLevels', 'test_suite_id');
    }

    public function features()
    {
        return $this->hasMany('\App\TestSuiteFeatures', 'test_suite_id');
    }

    public function scenarios()
    {
        return $this->hasMany('\App\TestSuiteScenarios', 'test_suite_id');
    }

    public function profileTypes()
    {
        return $this->hasMany('\App\TestSuiteProfileType', 'test_suite_id');
    }

    public function relatedTestSuites()
    {
        return $this->hasMany('\App\TestSuiteRelatedSuite', 'test_suite_id');
    }

    public function roles()
    {
        return $this->hasMany('\App\TestSuiteRole', 'test_suite_id');
    }

    public function specificationDocuments()
    {
        return $this->hasMany('\App\TestSuiteSpecificationDocument', 'test_suite_id');
    }

    public function protocolVersions()
    {
        return $this->hasMany('\App\TestSuiteProtocolVersion', 'test_suite_id');
    }
}
