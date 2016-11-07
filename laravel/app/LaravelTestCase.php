<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LaravelTestCase extends Model
{
    use UuidTrait, SlugTrait;

    protected $table = 'test_cases';

    public $incrementing = false;

    protected $fillable = [
        'slug', 'name', 'version_major', 'version_minor', 'version_patch', 'description', 'full_name', 'tester_role',
        'harness_role', 'initiator', 'test_execution_profile_id', 'configuration_profile_id', 'outcome_type', 'is_optional', 'test_pattern',
        'wp_id', 'published_at', 'created_at', 'updated_at', 'status', 'community_id'
    ];

    public function testSuites()
    {
        return $this->belongsToMany('App\LaravelTestSuite', 'test_suite_test_case', 'test_case_id', 'test_suite_id');
    }

    public function conformanceLevels()
    {
        return $this->hasMany('\App\TestCaseConformanceLevel', 'test_case_id')->with('testSuiteConformanceLevel');
    }

    /**
     * Get conformance levels for a given test suite
     * @return mixed
     */
    public function getConformanceLevels()
    {
        return $this->conformanceLevels()
            ->select('TSCL.*')
            ->join('test_suites_conformance_levels as TSCL', function ($join) {
                $join->on('TSCL.id', '=', 'test_cases_conformance_levels.conformance_level_id');
            })
            ->where('TSCL.code', '!=', 'Default')->get();
    }

    /**
     * @return mixed
     */
    public function getUniqueConformanceLevels()
    {
        return $this->conformanceLevels()
            ->select('TSCL.*')
            ->join('test_suites_conformance_levels as TSCL', function ($join) {
                $join->on('TSCL.id', '=', 'test_cases_conformance_levels.conformance_level_id');
            })
            ->where('TSCL.code', '!=', 'Default')
            ->groupBy('TSCL.code')
            ->pluck('code')->toArray();
    }

    public function scenarios()
    {
        return $this->hasMany('\App\TestCaseScenario', 'test_case_id')->with('testSuiteScenario');
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
        return $this->hasMany('\App\TestCaseFeature', 'test_case_id')->with('testSuiteFeature');
    }

    public function capabilities()
    {
        return $this->hasMany('\App\TestCaseCapability', 'test_case_id');
    }

    public function steps()
    {
        return $this->hasMany('\App\TestCaseStep', 'test_case_id')->orderBy('step');
    }

    public function updateRelations($request)
    {

        $processedEntries = [];
        if (is_array($request->get('test_suite_id'))) {
            foreach ($request->get('test_suite_id') as $testSuiteId => $entryId) {
                $processedEntries[] = $entryId;
                $this->testSuites()->updateOrCreate(['test_suite_id' => $entryId]);
            }
            if (!empty($processedEntries)) {
                $this->testSuites()->whereNotIn('test_suite_id', $processedEntries)->delete();
            }
        }

        $processedEntries = [];
        if (is_array($request->get('conformanceLevel'))) {
            foreach ($request->get('conformanceLevel') as $testSuiteId => $entry) {
                foreach($entry as $entryId) {
                    $processedEntries[] = $entryId;
                    $this->conformanceLevels()->updateOrCreate(['conformance_level_id' => $entryId]);
                }
            }
            if (!empty($processedEntries)) {
                $this->conformanceLevels()->whereNotIn('conformance_level_id', $processedEntries)->delete();
            }
        }

        $processedEntries = [];
        if (is_array($request->get('scenario'))) {
            foreach ($request->get('scenario') as $testSuiteId => $entry) {
                foreach($entry as $entryId) {
                    $processedEntries[] = $entryId;
                    $this->scenarios()->updateOrCreate(['test_suites_scenario_id' => $entryId]);
                }
            }
            if (!empty($processedEntries)) {
                $this->scenarios()->whereNotIn('test_suites_scenario_id', $processedEntries)->delete();
            }
        }

        $processedEntries = [];
        if (is_array($request->get('features'))) {
            foreach ($request->get('features') as $testSuiteId => $entry) {
                foreach($entry as $entryId) {
                    $processedEntries[] = $entryId;
                    $this->features()->updateOrCreate(['test_suites_feature_id' => $entryId]);
                }
            }
            if (!empty($processedEntries)) {
                $this->features()->whereNotIn('test_suites_feature_id', $processedEntries)->delete();
            }
        }

        //save scenarios
        $processedEntries = [];
        if ($request->get('steps')) {
            foreach ($request->get('steps') as $subName => $row) {
                if ($subName == 'step') {
                    foreach ($row as $key => $name) {
                        $processedEntries[] = $name;
                        $this->steps()->updateOrCreate(['step' => $name], [
                            'action' => @$request->get('steps')['action'][$key],
                            'expected_result' => @$request->get('steps')['expected_result'][$key],
                        ]);
                    }
                    if (!empty($processedEntries)) {
                        $this->steps()->whereNotIn('step', $processedEntries)->delete();
                    }
                }
            }
        }
    }

    /**
     *
     * @param $request
     * @return bool
     */
    public function isVersionUpdated($request)
    {
        if ($this->version_major < $request->get('version_major') ||
            $this->version_minor < $request->get('version_minor') ||
            $this->version_patch < $request->get('version_patch')
        ) {
            return true;
        }
        return false;
    }
}
