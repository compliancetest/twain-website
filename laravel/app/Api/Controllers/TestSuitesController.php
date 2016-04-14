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
     * {"error":{"message":"Subscriptions not found"},"code":404}
     *
     * @apiSuccessExample {json} Success-Response:
     * {"data":[{"id":"twain-compliance-technical-app-v1-0","title":"TWAIN Compliance Technical - App v1.0"},{"id":"twain-compliance-technical-ds-v1-0","title":"TWAIN Compliance Technical - DS v1.0"}],"code":200}
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
     * {"error":{"message":"Subscriptions not found"},"code":404}
     *
     * @apiError 404 Not Found
     * @apiErrorExample {json} Test Cases not found:
     * {"error":{"message":"Test Cases not found"},"code":404}
     *
     * @apiSuccessExample {json} Success-Response:
     * {"data":[{"id":"dsm-01-v1-0-1","title":"DSM-01 v1.0.1","description":"Test successful open the data source manager. Transition the session state from 1 to 2."},{"id":"ixf-01-v1-0","title":"IXF-01 v1.0","description":"Image transfer in the Native mode"},{"id":"dsm-02-v1-0","title":"DSM-02 v1.0","description":"Test successful select the data source. Transition the session state from 2 to 3."},{"id":"cap-01a-v1-0","title":"CAP-01a v1.0","description":"Test successful negotiation of TWAIN mandatory capability using the data source's user interface. MSG_ENABLEDS variant."},{"id":"cap-01b-v1-0","title":"CAP-01b v1.0","description":"Test successful negotiation of TWAIN mandatory capability using the data source's user interface. MSG_ENABLEDSUIONLY variant."},{"id":"cap-03-v1-0","title":"CAP-03 v1.0","description":"Test error handling for negotiation of TWAIN mandatory capabilities through TWAIN\u2019s programmatic interface."},{"id":"cap-05-v1-0","title":"CAP-05 v1.0","description":"Test successful negotiation of TWAIN mandatory capabilities through TWAIN\u2019s programmatic interface."},{"id":"ixf-02-v1-0","title":"IXF-02 v1.0","description":"Image transfer in the Memory mode."},{"id":"ixf-03a-v1-0","title":"IXF-03a v1.0","description":"Image transfer in the File mode."},{"id":"ixf-03b-v1-0","title":"IXF-03b v1.0","description":"Image transfer in the Memory-File mode."},{"id":"ixf-04a-v1-0","title":"IXF-04a v1.0","description":"Test error handling for all error conditions during image transfer in the Native mode."},{"id":"ixf-04b-v1-0","title":"IXF-04b v1.0","description":"Test error handling for all error conditions during image transfer in the Memory mode."},{"id":"ixf-04c-v1-0","title":"IXF-04c v1.0","description":"Test error handling for all error conditions during image transfer in File mode."},{"id":"ixf-04d-v1-0","title":"IXF-04d v1.0","description":"Test error handling for all error conditions during image transfer in the Memory-File mode."},{"id":"ixf-05a-v1-0","title":"IXF-05a v1.0","description":"Test transfer cancel during image transfer in the Native mode."},{"id":"ixf-05b-v1-0","title":"IXF-05b v1.0","description":"Test transfer cancel during image transfer in the Memory mode."},{"id":"ixf-05c-v1-0","title":"IXF-05c v1.0","description":"Test transfer cancel during image transfer in the File mode."},{"id":"ixf-05d-v1-0","title":"IXF-05d v1.0","description":"Test transfer cancel during image transfer in the Memory-File mode."},{"id":"err-01-v1-0","title":"ERR-01 v1.0","description":"Test correct transition state from 7 to 6 to 5."},{"id":"err-02-v1-0","title":"ERR-02 v1.0","description":"Test correct transition state from 5 to 4. Disable the data source."},{"id":"err-04-v1-0","title":"ERR-04 v1.0","description":"Test correct transition state from 3 to 2. Close the data source manager."},{"id":"dsm-03-v1-0","title":"DSM-03 v1.0","description":"Test successful open the data source. Transition the session state from 3 to 4."},{"id":"dsm-04-v1-0","title":"DSM-04 v1.0","description":"Test unsuccessful open the data source manager. Handle error conditions in the session state 1 including TWCC_LOWMEMORY and TWCC_SEQERROR."},{"id":"dsm-05-v1-0","title":"DSM-05 v1.0","description":"Test unsuccessful select the data source. Handle error an condition in the session state 2 TWCC_LOWMEMORY."},{"id":"err-03-v1-0","title":"ERR-03 v1.0","description":"Test correct transition state from 4 to 3. Close the data source."},{"id":"dsm-06-v1-0","title":"DSM-06 v1.0","description":"Test unsuccessful open the data source. Handle error conditions in the session state 3 including TWCC_LOWMEMORY, TWCC_MAXCONNECTIONS, TWCC_NODS and TWCC_OPERATIONERROR."}],"code":200}
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
