<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TestSuite extends Model
{

    protected $table = 'wp_test_suites';

    protected $primaryKey = 'suite_id';

    /**
     * get test suite's test cases list
     * @return mixed
     */
    public function getTestCases()
    {
        $suite = $this->suite_id;
        $query = DB::table('wp_posts')
            ->join('wp_postmeta AS pm1', function ($join) use ($suite) {
                $join->on('pm1.post_id', '=', 'wp_posts.ID')
                    ->where('pm1.meta_value', '=', $suite)
                    ->where('pm1.meta_key', '=', 'test_suite');
            })
            ->join('wp_postmeta AS pm2', function ($join) {
                $join->on('pm2.post_id', '=', 'wp_posts.ID')
                    ->where('pm2.meta_value', '=', 'Active')
                    ->where('pm2.meta_key', '=', 'test_case_status');
            })
            ->join('wp_postmeta AS pm3', function ($join) {
                $join->on('pm3.post_id', '=', 'wp_posts.ID')
                    ->where('pm3.meta_key', '=', 'test_intent_description');
            })
            ->join('wp_postmeta AS pm4', function ($join) {
                $join->on('pm4.post_id', '=', 'wp_posts.ID')
                    ->where('pm4.meta_key', 'LIKE', 'scenario_%');
            })
            ->join('wp_test_suites_scenarios AS scenario', function ($join) {
                $join->on('scenario.id', '=', 'pm4.meta_value');
            })
            ->join('wp_postmeta AS pm5', function ($join) {
                $join->on('pm5.post_id', '=', 'wp_posts.ID')
                    ->where('pm5.meta_value', '=', '0')
                    ->where('pm5.meta_key', '=', 'hide_case');
            });

        return $query->where('wp_posts.post_type', '=', 'test-case')
            ->groupBy('wp_posts.ID')
            ->orderBy('scenario.sequence')
            ->orderBy('wp_posts.post_title')
            ->select('wp_posts.*', 'scenario.code AS scenarioCode', 'scenario.description AS scenarioDescription', 'scenario.id AS scenarioID')->get();
    }
}
