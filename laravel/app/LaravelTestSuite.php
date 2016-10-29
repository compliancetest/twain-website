<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LaravelTestSuite extends Model
{
    use UuidTrait, SlugTrait;

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
        return $this->hasMany('\App\TestSuiteScenarios', 'test_suite_id')->orderBy('sequence');
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

    public function claims()
    {
        return $this->hasMany('\App\Claim', 'suite_minor_family_mark');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function community()
    {
        return $this->hasMany('App\Community');
    }

    /**
     * Get latest suite id for given suite_minor_family_mark
     * @param $suiteMinorFamilyMark
     * @return mixed
     */
    public static function getLatestSuiteForMinorFamilyMark($suiteMinorFamilyMark)
    {
        return self::where(['minor_family_mark' => $suiteMinorFamilyMark])
            ->orderBy('created_at', 'DESC')->first();
    }

    /**
     * Get latest suite id for given suite_minor_family_mark
     * @param $suiteMinorFamilyMark
     * @return mixed
     */
    public static function getMajorFamilyMarkForMinorFamilyMark($suiteMinorFamilyMark)
    {
        return self::where(['minor_family_mark' => $suiteMinorFamilyMark])
            ->orderBy('created_at', 'DESC')->first()->major_family_mark;
    }

    /**
     * get test suite's test cases list
     * @return mixed
     */
    public function getTestCases($role = false, $level = false)
    {
        $query = $this->testCases()
            ->join('test_cases_scenarios as cs', function ($join) {
                $join->on('test_cases.id', '=', 'cs.test_case_id');
            })
            ->join('test_suites_scenarios as ss', function ($join) {
                $join->on('ss.id', '=', 'cs.test_suites_scenario_id');
            })
            ->join('test_cases_conformance_levels as sl', function ($join) {
                $join->on('test_cases.id', '=', 'sl.test_case_id');
            })
            ->join('test_suites_conformance_levels as tsl', function ($join) {
                $join->on('sl.conformance_level_id', '=', 'tsl.id');
            })
            ->where('status', 'Active')
            ->groupBy('test_cases.id')
            ->orderBy('ss.sequence')
            ->orderBy('test_cases.full_name')
            ->select('test_cases.*', 'ss.code AS scenarioCode', 'ss.description AS scenarioDescription', 'ss.id AS scenarioID');

        if ($level) {
            $query->where('tsl.code', '=', $level);
        }
        if ($role) {
            $query->where('tester_role', '=', $role);
        }
        return $query->get();
    }

    public function getCases($filters = [], $isAdmin = false)
    {
        $query =  $this->testCases()->select("test_cases.*", 'TSS.id AS scenarioId', 'TSS.code AS scenarioCode')
            ->join('test_cases_scenarios as TCS', function ($join) {
                $join->on('TCS.test_case_id', '=', 'test_cases.id');
            })
            ->join('test_suites_scenarios as TSS', function ($join) {
                $join->on('TSS.id', '=', 'TCS.test_suites_scenario_id');
            })
            ->orderBy('TSS.sequence')
            ->orderBy('test_cases.full_name');
        if(!$isAdmin){
            $query->where('test_cases.status', 'Active');
        }
        return $query;
    }
}
