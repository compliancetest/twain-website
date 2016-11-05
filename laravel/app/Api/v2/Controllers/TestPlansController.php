<?php

namespace App\Api\v2\Controllers;

use App\CommunityOrganisationsApprovedProducts;
use App\Post;
use App\TestPlan;
use Validator;
use App\UserSubscription;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TestPlansController extends BaseApiController
{

    /**
     * @api {get} /v2/testplans Request Organisation Test Plans
     * @apiVersion 2.0.0
     *
     * @apiParam {string} product_id  Required - get test plans, associated with a product
     *
     * @apiName getTestPlans
     * @apiGroup Test Plans
     *
     * @apiDescription Method used to get organisation's list of test plans, associated with provided product ID
     *
     * @apiError 403 Forbidden
     * @apiErrorExample {json} No subscription:
     *   {
     *     "messages": ["Please subscribe to Test Suite with 'Application' Product Type"],
     *     "status": "error",
     *     "code": 403
     *   }
     *
     *
     * @apiError 403 Forbidden
     * @apiErrorExample {json} Not organization member:
     *   {
     *     "messages": ["Only organization member can perform testing"],
     *     "status": "error",
     *     "code": 403
     *   }
     *
     *  @apiError 403 Forbidden
     * @apiErrorExample {json} Organization is not approved yet:
     *   {
     *     "messages": ["Your organization can't perform testing."],
     *     "status": "error",
     *     "code": 403
     *   }
     *
     *  @apiErrorExample {json} The product registration has been not approved yet:
     * {
          "messages": [
            "The product registration has been not approved yet."
          ],
          "data": [],
          "status": "info",
          "code": 403
        }
     *
     * @apiError 404 Not Found
     * @apiErrorExample {json} Test plans not found:
     *   {
          "messages": [
            "Test plans not found. Please update product/edit features or add test plans on the web site."
          ],
          "data": [],
          "status": "warning",
          "code": 404
        }
     *
     * @apiError 422 Unprocessable entity
     * @apiErrorExample {json} Validation error:
     *   {
     *     "messages": [
     *         "The selected product id is invalid.",
     *     ],
     *     "status": "error",
     *     "code": 422
     *   }
     *
     * @apiSuccessExample {json} Success Response:
     *   {
     *     "data": [
     *       {
     *         "id": "a1b2a99c-bbb0-4a55-80c2-2ad3d600dde5",
     *         "test_suite_id": "twain-v2-3-compliance-data-sources-v1-0",
     *         "test_suite_title": "TWAIN v2.3 Compliance - Data Sources v1.0",
     *         "product_id": "4_twain2-freeimage-software-scanner_v2-201",
     *         "product_title": "TWAIN2 FreeImage Software Scanner",
     *         "conformance_level": "A"
     *       }
     *     ],
     *     "status": "success",
     *     "code": 200
     *   }
     *
     * @apiHeader (Headers) {String} Authorization Authorization value Basic (base64_encode(login:password)).
     *
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:wp_posts,post_name',
        ]);

        if ($validator->fails()) {
            return $this->respondUnprocessableEntity($validator->messages());
        }

        $product = Post::where('post_name', $request->get('product_id'))->first();
        $productType = $product->getMetaByKey('product_type');
        $hasSubscription = false;
        $suiteSubscriptions = \App\OrganisationSubscription::where(['user_id' => Auth::user()->ID])->get();
        foreach ($suiteSubscriptions as $suiteSubscription) {
            $type = Post::find($suiteSubscription->suite_family_mark)->meta()->where(['meta_key' => 'ts_tester_role'])->first()->meta_value;
            if ($type == $productType) {
                $hasSubscription = true;
            }
        }
        // we shouldn't show test plan's data to user without subscription
        if (!$hasSubscription) {
            return $this->respondForbiddenError(sprintf("Please subscribe to Test Suite with '%s' Product Type", $productType));
        }
        if(!CommunityOrganisationsApprovedProducts::where('product_id', Post::where('post_name', $request->get('product_id'))->first()->ID)->first()){
            return $this->setStatusCode(403)->respondWithDataAndMessage("The product registration has been not approved yet.", [], 'info');
        }
        $organisationPlans = \Auth::user()->organisation[0]->getTestPlans($request->get('product_id'));
        if (empty($organisationPlans)) {
            return $this->setStatusCode(404)->respondWithDataAndMessage("Test plans not found. Please update product/edit features or add test plans on the web site.", [], 'warning');
        }

        return $this->respondWithData($organisationPlans);
    }

    /**
     * @api {get} /v2/testplans/{TEST_PLAN_ID}/testcases Request Test plan's Test Cases
     * @apiVersion 2.0.0
     * 
     * @apiParam {string} [execution_mode]  Optional - get test cases by ExecutionMode (either 'Auto' or 'Manual')
     *
     * @apiName getTestCases
     * @apiGroup Test Plans
     *
     * @apiDescription Method used to get test plan's test cases
     *
     * @apiError 403 Forbidden
     * @apiErrorExample {json} Not organization member:
     *   {
     *     "messages": ["Only organization member can perform testing"],
     *     "status": "error",
     *     "code": 403
     *   }
     *
     * @apiError 403 Forbidden
     * @apiErrorExample {json} Organization is not approved yet:
     *   {
     *     "messages": ["Your organization can't perform testing."],
     *     "status": "error",
     *     "code": 403
     *   }
     *
     * @apiError 403 Forbidden
     * @apiErrorExample {json} Organization doesn't have access to test suite:
     *    {
     *     "messages": ["Your organisation doesn't have access to this test suite."],
     *     "status": "error",
     *     "code": 403
     *   }
     *
     *
     * @apiErrorExample {json} User don't have subscription to test plan's test suite:
     *   {
     *     "messages": ["You don't have subscription to test plan's test suite"],
     *     "status": "error",
     *     "code": 403
     *   }
     *
     * @apiError 404 Not Found
     * @apiErrorExample {json} Test Cases not found:
     *   {
     *     "messages":  ["Test Cases not found"],
     *     "status": "error",
     *     "code": 404
     *   }
     *
     * @apiError 422 Unprocessable entity
     * @apiErrorExample {json} Validation error:
     *   {
     *     "messages": ["The selected execution mode is invalid."],
     *     "status": "error",
     *     "code": 422
     *   }
     *
     * @apiSuccessExample {json} Success-Response:
     *   {
     *     "data": [
     *       {
     *         "id": "sc-01-v1-0",
     *         "title": "SC-01 v1.0",
     *         "description": "Confirm Basic Negotiation with CAP_SUPPORTEDCAPS."
     *       }
     *     ],
     *     "status": "success",
     *     "code": 200
     *   }
     *
     * @apiHeader (Headers) {String} Authorization Authorization value Basic (base64_encode(login:password)).
     *
     */

    public function testcases($testPlanId, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'execution_mode' => 'in:Auto,Manual'
        ]);

        if ($validator->fails()) {
            return $this->respondUnprocessableEntity($validator->messages());
        }

        $testPlan = TestPlan::find($testPlanId);
        $testSuiteData = Post::find($testPlan->suite_id);

        $hasAccessToTestSuite = $this->doesOrganisationHasAccessToTestSuite($testSuiteData->post_name);
        if(!$hasAccessToTestSuite){
            return $this->respondForbiddenError("Your organisation doesn't have access to this test suite.");
        }

        // we shouldn't show test plan's data to user without subscription
        if (!\Auth::user()->suiteSubscriptions()->where(['status' => 'Active', 'suite_family_mark' => $testPlan->suite_id])->first()) {
            return $this->respondForbiddenError("You don't have subscription to test plan's test suite");
        }
        $excludedCases = $testPlan->getExcludedCases();
        $successCases = $testPlan->getSuccessCases($testPlan->product_id);
        $failedCases = $testPlan->getFailedCases($testPlan->product_id);
        $optionalCases = $testPlan->getOptionalCases();

        $cases = [];

        $suiteId = $testPlan->suite_id;

        $roles = $testPlan->role;
        $levels = $testPlan->level;

        $query = DB::table('wp_posts')
            ->join('wp_postmeta AS pm1', function ($join) use ($suiteId) {
                $join->on('pm1.post_id', '=', 'wp_posts.ID')
                    ->where('pm1.meta_value', '=', $suiteId)
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
            ->join('wp_postmeta AS pm5', function ($join) {
                $join->on('pm5.post_id', '=', 'wp_posts.ID')
                    ->where('pm5.meta_key', '=', 'hide_case')
                    ->where('pm5.meta_value', '=', '0');
            })
            ->join('wp_test_suites_scenarios AS scenario', function ($join) {
                $join->on('scenario.id', '=', 'pm4.meta_value');
            })
            ->join('wp_postmeta AS pm7', function ($join) use ($roles) {
                $join->on('pm7.post_id', '=', 'wp_posts.ID')
                    ->where('pm7.meta_value', '=', $roles)
                    ->where('pm7.meta_key', '=', 'choose_tester_role');
            })
            ->join('wp_postmeta AS pm8', function ($join) use ($suiteId, $levels) {
                $join->on('pm8.post_id', '=', 'wp_posts.ID')
                    ->where('pm8.meta_value', '=', $levels)
                    ->where('pm8.meta_key', '=', 'conformance_level_' . $suiteId);
            });

        if ($request->get('execution_mode')) {
            $executionMode = $request->get('execution_mode');

            $query->join('wp_postmeta AS pm6', function ($join) use ($executionMode) {
                $join->on('pm6.post_id', '=', 'wp_posts.ID')
                    ->where('pm6.meta_value', '=', $executionMode)
                    ->where('pm6.meta_key', '=', 'executionMode');
            });
        }

        $testCases = $query->where('wp_posts.post_type', '=', 'test-case')
            ->groupBy('wp_posts.ID')
            ->orderBy('scenario.sequence')
            ->orderBy('wp_posts.post_title')
            ->select('wp_posts.ID', 'wp_posts.post_name', 'wp_posts.post_title', 'pm3.meta_value', 'pm4.meta_value AS scenarioId')->get();

        foreach ($testCases AS $testCase) {
            if (in_array($testCase->ID, $successCases) || array_key_exists($testCase->ID, $excludedCases) || in_array($testCase->ID, $failedCases) || in_array($testCase->ID, $optionalCases)) {
                continue;
            }
            $cases[] = [
                'id' => $testCase->post_name,
                'title' => $testCase->post_title,
                'description' => strip_tags($testCase->meta_value),
            ];
        }

        if (empty($testCases)) {
            return $this->respondNotFound("Test Cases not found");
        }

        return $this->respondWithData($cases);
    }
}
