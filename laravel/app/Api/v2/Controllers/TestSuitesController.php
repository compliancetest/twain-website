<?php

namespace App\Api\v2\Controllers;

use App\LaravelTestSuite;
use App\Post;
use App\Product;
use Validator;
use App\UserSubscription;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TestSuitesController extends BaseApiController
{

    /**
     * @api {get} /v2/testsuites Request Test Suites list
     * @apiVersion 2.0.0
     *
     * @apiParam {string} [product_type]  Optional - get test suites by product type (either 'DataSource' or 'Application')
     * @apiParam {string} [product_id]  Optional - get test suites, associated with a product
     *
     * @apiName getTestSuites
     * @apiGroup Test Suites
     *
     * @apiDescription Method used to get test suites list
     *
     * @apiError 403 Forbidden
     * @apiErrorExample {json} No subscription:
     *   {
     *     "messages": ["You do not have any active subscription"],
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
     * @apiError 403 Forbidden
     * @apiErrorExample {json} Organization is not approved yet:
     *   {
     *     "messages": ["Your organization can't perform testing."],
     *     "status": "error",
     *     "code": 403
     *   }
     *
     *
     * @apiError 404 Not Found
     * @apiErrorExample {json} Subscriptions not found:
     *   {
     *     "messages": ["Suites not found"],
     *     "status": "error",
     *     "code": 404
     *   }
     *
     * @apiError 422 Unprocessable entity
     * @apiErrorExample {json} Validation error:
     *   {
     *     "messages": [
     *          "The selected tester role is invalid.",
     *         "The selected product id is invalid."
     *      ],
     *     "status": "error",
     *     "code": 422
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
            'product_type' => 'in:Application,DataSource',
            'product_id' => 'exists:products,slug',
        ]);

        if ($validator->fails()) {
            return $this->respondUnprocessableEntity($validator->messages());
        }

        $suites = [];

        /**
         * Return product's test suites if product_id parameter exists
         */
        if ($request->has('product_id')) {

            $product = Product::findBySlug($request->get('product_id'));

            foreach ($product->getFeatures() as $testSuiteId => $features) {
                $suite = LaravelTestSuite::find($testSuiteId);
                /**
                 * We filter test suites by tester role if tester_role parameter exists
                 */
                if ($request->has('product_type')) {
                    if ($suite->product_type != $request->get('product_type')) {
                        continue;
                    }
                }
                $suites[] = [
                    'id' => $suite->slug,
                    'title' => $suite->full_name,
                ];
            }

        /**
         * Return subscribed test suites
         */
        } else {

            $subscriptions = Auth::user()->suiteSubscriptions;

            if (!count($subscriptions)) {
                return $this->respondForbiddenError("You do not have any active subscription");
            }

            foreach ($subscriptions as $subscription) {
                $suite = LaravelTestSuite::getLatestSuiteForMinorFamilyMark($subscription->suite_minor_family_mark);
                /**
                 * We filter test suites by tester role if tester_role parameter exists
                 */
                if ($request->has('product_type')) {
                    if ($suite->product_type != $request->get('product_type')) {
                        continue;
                    }
                }
                $suites[] = [
                    'id' => $suite->slug,
                    'title' => $suite->full_name,
                ];
            }
        }

        if (empty($suites)) {
            return $this->respondNotFound("Suites not found");
        }

        return $this->respondWithData($suites);
    }

    /**
     * @api {get} /v2/testsuites/{SUITE_ID}/testcases Request Test Suite's Test Case
     * @apiVersion 2.0.0
     * 
     * @apiParam {string} [execution_mode]  Optional - get test cases by ExecutionMode (either 'Auto' or 'Manual')
     *
     * @apiName getTestCases
     * @apiGroup Test Suites
     *
     * @apiDescription Method used to get test suite's active test cases
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
     * @apiError 404 Not Found
     * @apiErrorExample {json} Subscriptions not found:
     *   {
     *     "messages": ["Subscriptions not found"],
     *     "status": "error",
     *     "code": 404
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
     *     "status": "success",
     *     "code": 200
     *   }
     *
     * @apiHeader (Headers) {String} Authorization Authorization value Basic (base64_encode(login:password)).
     *
     */

    public function testcases($suiteSlug, Request $request)
    {

        $validator = Validator::make($request->all(), [
            'execution_mode' => 'in:Auto,Manual'
        ]);

        if ($validator->fails()) {
            return $this->respondUnprocessableEntity($validator->messages());
        }

        $hasAccessToTestSuite = $this->doesOrganisationHasAccessToTestSuite($suiteSlug);
        if(!$hasAccessToTestSuite){
            return $this->respondForbiddenError("Your organisation doesn't have access to this test suite.");
        }
        
        $suite = LaravelTestSuite::findBySlug($suiteSlug);
        $subscription = Auth::user()->suiteSubscriptions()->where('suite_minor_family_mark', $suite->minor_family_mark)->first();
        if (!$subscription) {
            return $this->respondNotFound("Subscriptions not found");
        }

        $cases = [];

        $testCases = $suite->getOrderedCases(['execution_mode' => $request->get('execution_mode')]);

        if (!count($testCases)) {
            return $this->respondNotFound("Test Cases not found");
        }

        foreach ($testCases AS $testCase) {
            $cases[] = [
                'id' => $testCase->slug,
                'title' => $testCase->full_name,
                'description' => $testCase->description,
            ];
        }

        return $this->respondWithData($cases);
    }
}
