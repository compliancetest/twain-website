<?php

namespace App\Api\Controllers;

use App\Profile;
use App\TestCase;
use App\TestingDetail;
use App\Post;
use App\UserSubscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel;

class TestSuitesController extends BaseApiController
{

    /**
     * @api {get} /v1/testsuites Request Test Suites list
     *
     * @apiName getTestSuites
     * @apiGroup TestSuites
     *
     * @apiDescription Method used to get test suites list
     *
     * @apiError 404 Not Found
     * @apiErrorExample {json} Subscriptions not found:
     *   {
     *     "errors": {
     *       "message": [
     *          "Subscriptions not found"
     *       ]
     *     },
     *     "code": 404
     *   }
     *
     * @apiSuccessExample {json} Success Response:
     *   {
     *     "data": [
     *       {
     *         "id": "twain-compliance-technical-app-v1-0",
     *         "title": "TWAIN Compliance Technical - App v1.0"
     *       },
     *       {
     *         "id": "twain-compliance-technical-ds-v1-0",
     *         "title": "TWAIN Compliance Technical - DS v1.0"
     *       }
     *     ],
     *     "code": 200
     *   }
     *
     * @apiHeader (Headers) {String} Authorization Authorization value Basic (base64_encode(login:password)).
     *
     * @apiVersion 1.0.0
     */
    public function index()
    {
        $subscriptions = UserSubscription::where(['user_id' => Auth::user()->ID, 'status' => 'Active'])->get();
        if ($subscriptions->isEmpty()) {
            return $this->respondNotFound("Subscriptions not found");
        }

        $suites = [];

        foreach ($subscriptions as $subscription) {
            $suite = Post::find($subscription->suite_id);
            $suites[] = [
                'id' => $suite->post_name,
                'title' => $suite->post_title,
            ];
        }

        return $this->respondWithData($suites);
    }

    /**
     * @api {get} /api/v1/testsuites/{SUITE_ID}/testcases Request Test Suite's Test Case
     *
     * @apiName getTestCases
     * @apiGroup TestSuites
     *
     * @apiDescription Method used to get test suite's active test cases
     *
     * @apiError 404 Not Found
     * @apiErrorExample {json} Subscriptions not found:
     *   {
     *     "errors": {
     *       "message": [
     *          "Subscriptions not found"
     *       ]
     *     },
     *     "code": 404
     *   }
     *
     * @apiError 404 Not Found
     * @apiErrorExample {json} Test Cases not found:
     *   {
     *     "errors": {
     *       "message":  [
     *          "Test Cases not found"
 *           ]
     *     },
     *     "code": 404
     *   }
     *
     * @apiSuccessExample {json} Success-Response:
     *   {
     *     "data": [
     *       {
     *         "id": "dsm-01-v1-0-1",
     *         "title": "DSM-01 v1.0.1",
     *         "description": "Test successful open the data source manager. Transition the session state from 1 to 2."
     *       },
     *       {
     *         "id": "ixf-01-v1-0",
     *         "title": "IXF-01 v1.0",
     *         "description": "Image transfer in the Native mode"
     *       }
     *     ],
     *     "code": 200
     *   }
     *
     * @apiHeader (Headers) {String} Authorization Authorization value Basic (base64_encode(login:password)).
     *
     * @apiVersion 1.0.0
     */

    public function testcases($suiteId)
    {
        $suite = Post::where(['post_name' => $suiteId])->first();
        $subscription = UserSubscription::where(['user_id' => Auth::user()->ID, 'status' => 'Active', 'suite_id' => $suite->ID])->first();
        if (!$subscription) {
            return $this->respondNotFound("Subscriptions not found");
        }

        $cases = [];

        $testCases = DB::table('wp_posts')
            ->join('wp_postmeta AS pm1', function ($join) use ($suite) {
                $join->on('pm1.post_id', '=', 'wp_posts.ID')
                    ->where('pm1.meta_value', '=', $suite->ID)
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
            ->where('wp_posts.post_type', '=', 'test-case')
            ->groupBy('wp_posts.ID')
            ->select('wp_posts.post_name', 'wp_posts.post_title', 'pm3.meta_value')->get();

        if (empty($testCases)) {
            return $this->respondNotFound("Test Cases not found");
        }

        foreach ($testCases AS $testCase) {
            $cases[] = [
                'id' => $testCase->post_name,
                'title' => $testCase->post_title,
                'description' => strip_tags($testCase->meta_value),
            ];
        }

        return $this->respondWithData($cases);
    }
}
