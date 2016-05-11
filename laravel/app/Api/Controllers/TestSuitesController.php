<?php

namespace App\Api\Controllers;

use App\Post;
use Validator;
use App\UserSubscription;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TestSuitesController extends BaseApiController
{

    /**
     * @api {get} /v1/testsuites Request Test Suites list
     * @apiParam {string} [tester_role]  Optional - test suite's tester role (either 'DataSource' or 'Application')
     * @apiParam {string} [product_id]  Optional - product id
     *
     * @apiName getTestSuites
     * @apiGroup TestSuites
     *
     * @apiDescription Method used to get test suites list
     *
     * @apiError 403 Forbidden
     * @apiErrorExample {json} No subscription:
     *   {
     *     "errors": {
     *       "message": [
     *         "You do not have any active subscription"
     *       ]
     *     },
     *     "code": 403
     *   }
     *
     * @apiError 404 Not Found
     * @apiErrorExample {json} Subscriptions not found:
     *   {
     *     "errors": {
     *       "message": [
     *          "Suites not found"
     *       ]
     *     },
     *     "code": 404
     *   }
     *
     * @apiError 422 Unprocessable entity
     * @apiErrorExample {json} Validation error:
     *   {
     *     "errors": {
     *       "tester_role": [
     *         "The selected tester role is invalid."
     *       ],
     *       "product_id": [
     *         "The selected product id is invalid."
     *       ]
     *     },
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
     *     "code": 200
     *   }
     *
     * @apiHeader (Headers) {String} Authorization Authorization value Basic (base64_encode(login:password)).
     *
     * @apiVersion 1.0.0
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tester_role' => 'in:Application,DataSource',
            'product_id' => 'exists:wp_posts,post_name,post_type,product-service',
        ]);

        if ($validator->fails()) {
            return $this->respondUnprocessableEntity($validator->messages());
        }

        $suites = [];

        /**
         * Return product's test suites if product_id parameter exists
         */
        if ($request->has('product_id')) {

            $product = Post::where(['post_name' => $request->get('product_id')])->first();
            $productSuiteMeta = $product->meta()->where(['meta_key' => 'product_suites'])->first();

            if (!empty($productSuiteMeta)) {
                foreach (json_decode($productSuiteMeta->meta_value, true) as $suiteId) {
                    $suite = Post::find($suiteId);
                    /**
                     * We filter test suites by tester role if tester_role parameter exists
                     */
                    if ($request->has('tester_role')) {
                        $suiteRoleMeta = $suite->meta()->where(['meta_key' => 'ts_tester_role'])->first();
                        if (empty($suiteRoleMeta) || $suiteRoleMeta->meta_value != $request->get('tester_role')) {
                            continue;
                        }
                    }
                    $suites[] = [
                        'id' => $suite->post_name,
                        'title' => $suite->post_title,
                    ];
                }
            }

        /**
         * Return subscribed test suites
         */
        } else {

            $subscriptions = UserSubscription::where(['user_id' => Auth::user()->ID, 'status' => 'Active'])->get();

            if ($subscriptions->isEmpty()) {
                return $this->respondForbiddenError("You do not have any active subscription");
            }

            foreach ($subscriptions as $subscription) {
                $suite = Post::find($subscription->suite_id);
                /**
                 * We filter test suites by tester role if tester_role parameter exists
                 */
                if ($request->has('tester_role')) {
                    $suiteRoleMeta = $suite->meta()->where(['meta_key' => 'ts_tester_role'])->first();
                    if (empty($suiteRoleMeta) || $suiteRoleMeta->meta_value != $request->get('tester_role')) {
                        continue;
                    }
                }
                $suites[] = [
                    'id' => $suite->post_name,
                    'title' => $suite->post_title,
                ];
            }
        }

        if (empty($suites)) {
            return $this->respondNotFound("Suites not found");
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
