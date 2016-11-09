<?php

namespace App\Api\v2\Controllers;

use App\CommunityOrganisationsApprovedProducts;
use App\LaravelTestSuite;
use App\Post;
use App\Product;
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
            'product_id' => 'required|exists:products,slug',
        ]);

        if ($validator->fails()) {
            return $this->respondUnprocessableEntity($validator->messages());
        }

        $product = Product::findBySlug($request->get('product_id'));
        $hasSubscription = false;
        foreach (Auth::user()->suiteSubscriptions as $suiteSubscription) {
            $testSuite = LaravelTestSuite::getLatestSuiteForMinorFamilyMark($suiteSubscription->suite_minor_family_mark);
            if ($testSuite->product_type == $product->type) {
                $hasSubscription = true;
            }
        }
        // we shouldn't show test plan's data to user without subscription
        if (!$hasSubscription) {
            return $this->respondForbiddenError(sprintf("Please subscribe to Test Suite with '%s' Product Type", $productType));
        }
        if(!CommunityOrganisationsApprovedProducts::where('product_id', $product->id)->first()){
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
        $testSuiteData = LaravelTestSuite::getLatestSuiteForMinorFamilyMark($testPlan->suite_minor_family_mark);

        $hasAccessToTestSuite = $this->doesOrganisationHasAccessToTestSuite($testSuiteData->slug);
        if(!$hasAccessToTestSuite){
            return $this->respondForbiddenError("Your organisation doesn't have access to this test suite.");
        }

        // we shouldn't show test plan's data to user without subscription
        if (!\Auth::user()->suiteSubscriptions()->where(['status' => 'Active', 'suite_minor_family_mark' => $testPlan->suite_minor_family_mark])->first()) {
            return $this->respondForbiddenError("You don't have subscription to test plan's test suite");
        }
        $excludedCases = $testPlan->getExcludedCases();
        $successCases = $testPlan->getSuccessCases($testPlan->product_id);
        $failedCases = $testPlan->getFailedCases($testPlan->product_id);
        $optionalCases = $testPlan->getOptionalCases();

        $cases = [];

        $testCases = $testSuiteData->getOrderedCases([
            'role' => $testPlan->role,
            'level' => $testPlan->level,
            'execution_mode' => $request->get('execution_mode'),
        ]);

        foreach ($testCases AS $testCase) {
            if (in_array($testCase->id, $successCases) || array_key_exists($testCase->id, $excludedCases) || in_array($testCase->id, $failedCases) || in_array($testCase->id, $optionalCases)) {
                continue;
            }
            $cases[] = [
                'id' => $testCase->slug,
                'title' => $testCase->full_name,
                'description' => $testCase->description,
            ];
        }

        if (empty($cases)) {
            return $this->respondNotFound("Test Cases not found");
        }

        return $this->respondWithData($cases);
    }
}
