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
        'organisation_subscription_id', 'product_id', 'suite_id', 'level', 'role', 'creator_id'
    ];

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
     * Get test cases list with SUCESS status from transactions table
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
     * Get test cases list with not SUCESS status from transactions table
     * @return mixed
     */
    public function getFailedCases($productId)
    {
        $suiteId = $this->suite_id;
        return DB::table('transactions')
            ->select('transactions.*')
            ->join('test_outcome_statuses AS TO', function ($join) use ($suiteId) {
                $join->on('TO.id', '=', 'transactions.test_outcome_status_id')
                    ->where('TO.code', '!=', 'PASS');
            })
            ->where('product_id', $productId)
            ->where('audit_record', true)
            ->lists('test_case_id');
    }
}
