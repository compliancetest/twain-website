<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class TestPlan extends Model
{
    use UuidTrait, SoftDeletes;

    public $incrementing = false;

    protected $fillable = [
        'organisation_subscription_id', 'product_id', 'suite_minor_family_mark', 'level', 'role', 'creator_id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function claim()
    {
        return $this->hasOne('App\Claim');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function excludedCases()
    {
        return $this->hasMany('App\TestPlanExcludedCases');
    }

    public function getExcludedCases()
    {
        return $this->excludedCases->keyBy('test_case_id')->toArray();
    }

    /**
     * Get array of test suite's optional cases
     * @return mixed
     */
    public function getOptionalCases()
    {
        $suiteId = $this->suite_id;
        return DB::table('wp_posts')
            ->select('wp_posts.*')
            ->join('wp_postmeta AS pm1', function ($join) use ($suiteId) {
                $join->on('pm1.post_id', '=', 'wp_posts.ID')
                    ->where('pm1.meta_value', '=', $suiteId)
                    ->where('pm1.meta_key', '=', 'test_suite');
            })
            ->join('wp_postmeta AS pm2', function ($join) {
                $join->on('pm2.post_id', '=', 'wp_posts.ID')
                    ->where('pm2.meta_value', '=', 'Yes')
                    ->where('pm2.meta_key', '=', 'testcase_status');
            })
            ->lists('ID');
    }

    /**
     * Get test cases list with SUCCESS status from transactions table
     * @return mixed
     */
    public function getSuccessCases($productId)
    {
        $suiteId = $this->suite_id;
        return DB::table('transactions')
            ->select('transactions.*')
            ->join('test_outcome_statuses AS TO', function ($join) use ($suiteId) {
                $join->on('TO.id', '=', 'transactions.test_outcome_status_id')
                    ->where('TO.code', '=', 'PASS');
            })
            ->where('product_id', $productId)
            ->where('audit_record', true)
            ->lists('test_case_id');
    }

    /**
     * Get test cases list with FAIL status from transactions table
     * @return mixed
     */
    public function getFailedCases($productId)
    {
        $suiteId = $this->suite_id;
        return DB::table('transactions')
            ->select('transactions.*')
            ->join('test_outcome_statuses AS TO', function ($join) use ($suiteId) {
                $join->on('TO.id', '=', 'transactions.test_outcome_status_id')
                    ->where('TO.code', '=', 'FAIL');
            })
            ->where('product_id', $productId)
            ->where('audit_record', true)
            ->lists('test_case_id');
    }

    /**
     * Get test cases list with SKIP status from transactions table
     * @return mixed
     */
    public function getSkippedCases()
    {
        return DB::table('test_plans_excluded_cases')
            ->select('test_plans_excluded_cases.*')
            ->where('test_plan_id', $this->id)
            ->where('is_skipped', true)
            ->lists('test_case_id');
    }

    /**
     * Get test cases list with SKIP status from transactions table
     * @return mixed
     */
    public function getSkippedTransactions()
    {
        $suiteId = $this->suite_id;
        return DB::table('transactions')
            ->select('transactions.*')
            ->join('test_outcome_statuses AS TO', function ($join) use ($suiteId) {
                $join->on('TO.id', '=', 'transactions.test_outcome_status_id')
                    ->where('TO.code', '=', 'SKIP');
            })
            ->where('product_id', $this->product_id)
            ->where('audit_record', true)
            ->lists('test_case_id');
    }

    /**
     * Exclude cases with unsupported capabilities / features
     * @param string $type
     * @param array $applicationProductFeatures
     */
    public function excludeTestCases($type = 'DataSource', $applicationProductFeatures = [])
    {
        $testSuite = LaravelTestSuite::find($this->suite_minor_family_mark);
        $product = Product::find($this->product_id);
        $testCases = $testSuite->getTestCases($this->role, $this->level);
        $productCapabilities = $product->capabilities()->pluck('capability')->toArray();
        $productFeatures = $product->features()->pluck('test_suites_feature_id')->toArray();
        foreach ($testCases as $testCase) {
            if ($type == 'DataSource') {
                $capabilities = $testCase->capabilities()->pluck('capability')->toArray();
                $diff = array_diff($capabilities, $productCapabilities);
                if (!empty($diff)) {
                    TestPlanExcludedCases::create([
                        'test_case_id' => $testCase->id,
                        'excluded_by_user_id' => \Auth::user()->ID,
                        'test_plan_id' => $this->id,
                        'reason' => 'Those capabilities not supported by data source: ' . implode(', ', $diff),
                        'is_skipped' => true
                    ]);
                }
            } else {
                $testCaseFeatures = $testCase->features()->pluck('test_suites_feature_id')->toArray();
                $diff = array_diff($testCaseFeatures, $productFeatures);
                if (!empty($diff)) {
                    TestPlanExcludedCases::create([
                        'test_case_id' => $testCase->id,
                        'excluded_by_user_id' => \Auth::user()->ID,
                        'test_plan_id' => $this->id,
                        'reason' => 'Those features not supported by application: ' . implode(', ', $diff),
                        'is_skipped' => true
                    ]);
                }
            }
        }
    }

    public function canBeClaimed()
    {
        $suite = LaravelTestSuite::find($this->suite_minor_family_mark);
        $testCases = $suite->getTestCases($this->role, $this->level);

        if ($testCases->isEmpty()) {
            return false;
        }

        $excludedCases = $this->getExcludedCases();
        $successCases = $this->getSuccessCases($this->product_id);
        $optionalCases = $this->getOptionalCases();
        $skippedCases = $this->getSkippedCases();

        foreach ($testCases as $case) {
            if (in_array($case->id, $successCases) || in_array($case->id, $skippedCases) || array_key_exists($case->id, $excludedCases) || in_array($case->id, $optionalCases)) {
                //success case
            } else {
                return false;
            }
        }
        return true;
    }

    /**
     * Check that test plan has excluded / skipped cases
     * @return bool
     */
    public function hasExclusions()
    {
        $suite = LaravelTestSuite::find($this->suite_minor_family_mark);
        $testCases = $suite->getTestCases($this->role, $this->level);

        $excludedCases = $this->getExcludedCases();
        $successCases = $this->getSuccessCases($this->product_id);
        $skippedCases = $this->getSkippedCases();

        $hasExclusions = false;

        foreach ($testCases as $case) {
            if (!in_array($case->id, $successCases) && (in_array($case->id, $skippedCases) || array_key_exists($case->id, $excludedCases))) {
                $hasExclusions = true;
            }
        }
        return $hasExclusions;
    }
}
