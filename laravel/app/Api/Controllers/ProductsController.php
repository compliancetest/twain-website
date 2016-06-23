<?php

namespace App\Api\Controllers;

use App\Jobs\ProcessTransactionLog;
use App\Organisation;
use App\OrganisationMember;
use App\OrganisationSubscription;
use App\Post;
use App\PostMeta;
use App\PricingPlan;
use App\TestPlan;
use App\TestPlanExcludedCases;
use Aws\Laravel\AwsFacade as AWS;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpKernel;
use Validator;

class ProductsController extends BaseApiController
{

    private $product;

    /**
     * @api {post} /v1/products Create product
     *
     * @apiParam {JSON} identity  Mandatory - product identity json.
     * @apiParam {string} product_type  Mandatory - product type (either 'DataSource' or 'Application')
     * @apiParam {integre} organisation_id  Mandatory - User's organisation ID
     * @apiParamExample {json} DataSource example
     *
     *   {
     *     "Identity": {
     *       "ProtocolMajor": 2,
     *       "ProtocolMinor": 1,
     *       "Manufacturer": "TWAIN Working Group",
     *       "ProductName": "TWAIN2 FreeImage Software Scanner",
     *       "ProductFamily": "Software Scan",
     *       "Version": {
     *         "MajorNum": 2,
     *         "MinorNum": 1,
     *         "Language": "TWLG_ENGLISH",
     *         "Country": "TWCY_USA",
     *         "Info": "2.1.3 sample debug 32bit"
     *       },
     *       "SupportedGroups": [
     *         "DG_CONTROL",
     *         "DG_IMAGE",
     *         "DF_DS2"
     *       ]
     *     },
     *     "Capabilities": [
     *       "CAP_DEVICEONLINE",
     *       "CAP_SUPPORTEDCAPS",
     *       "CAP_UICONTROLLABLE",
     *       "CAP_XFERCOUNT",
     *       "ICAP_BITDEPTH",
     *       "ICAP_BITORDER",
     *       "ICAP_COMPRESSION",
     *       "ICAP_PHYSICALHEIGHT",
     *       "ICAP_PHYSICALWIDTH",
     *       "ICAP_PIXELFLAVOR",
     *       "ICAP_PIXELTYPE",
     *       "ICAP_PLANARCHUNKY",
     *       "ICAP_UNITS",
     *       "ICAP_XFERMECH",
     *       "ICAP_XNATIVERESOLUTION",
     *       "ICAP_XRESOLUTION",
     *       "ICAP_YNATIVERESOLUTION",
     *       "ICAP_YRESOLUTION"
     *     ]
     *   }
     *
     * @apiParamExample {json} Application example
     *   {
     *       "Identity": {
     *           "ProtocolMajor": 2,
     *           "ProtocolMinor": 1,
     *           "Manufacturer": "TWAIN Working Group",
     *           "ProductName": "TWAIN2 FreeImage EHR Software",
     *           "ProductFamily": "EHR Software",
     *           "Version": {
     *               "MajorNum": 2,
     *               "MinorNum": 1,
     *               "Language": "TWLG_ENGLISH",
     *               "Country": "TWCY_USA",
     *               "Info": "2.1.3 sample debug 32bit"
     *           },
     *           "SupportedGroups": ["DG_CONTROL",
     *           "DG_IMAGE",
     *           "DF_DS2"]
     *       }
     *   }
     * @apiName createProduct
     * @apiGroup Products
     *
     * @apiSuccessExample {json} Product created:
     *  {
     *     "data": {
     *       "id": "twain2-freeimage-software-scanner-v-2-1",
     *       "title": "TWAIN2 FreeImage Software Scanner",
     *       "link": "http://twain.my/product/twain2-freeimage-software-scanner-v-2-1"
     *     },
     *     "code": 201
     *   }
     *
     *  @apiSuccessExample {json} Product exist:
     *  {
     *     "data": {
     *       "id": "twain2-freeimage-software-scanner-v-2-1",
     *       "title": "TWAIN2 FreeImage Software Scanner",
     *       "link": "http://twain.my/product/twain2-freeimage-software-scanner-v-2-1"
     *     },
     *     "code": 200
     *   }
     *
     * @apiError 422 Validation error
     * @apiErrorExample {json} Validation error:
     *   {
     *     "errors": {
     *       "identity": [
     *         "The identity field is required."
     *       ],
     *       "product_type": [
     *         "The product type field is required."
     *       ]
     *     },
     *     "code": 422
     *   }
     *
     * @apiError 403 Forbidden
     * @apiErrorExample {json} Not organisation member:
     *   {
     *     "errors": {
     *       "message": [
     *         "Only organisation member can perform testing"
     *       ]
     *     },
     *     "code": 403
     *   }
     *
     * @apiError 403 Forbidden
     * @apiErrorExample {json} Permissions error:
     * {
     *    "errors": {
     *      "message": [
     *          "This product was created by another user!"
     *      ]
     *    },
     *    "code": 403
     *  }
     *
     * @apiHeader (Headers) {String} Authorization Authorization value Basic (base64_encode(login:password)).
     *
     * @apiVersion 1.0.0
     */
    public function create(\Illuminate\Http\Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identity' => 'required|json',
            'product_type' => 'required|in:DataSource,Application',
            'organisation_id' => 'required|exists:wp_organisations,id',
        ]);

        $user = \Auth::user();
        if ($validator->fails()) {
            return $this->respondUnprocessableEntity($validator->messages());
        }
        $jsonEntry = json_decode($request->get('identity'), true);
        $entity = $jsonEntry['Identity'];
        $productName = $entity['ProductName'];
        $productVersion = $entity['Version']['MajorNum'] . '.' . $entity['Version']['MinorNum'];
        $productId = $request->get('organisation_id') . '_' .$this->cleanSlug($entity['Manufacturer']) . "_" . $this->cleanSlug($productName) . "_v" . str_replace('.', '-', $productVersion);
        $this->product = Post::where(['post_name' => $productId])->first();
        if ($this->product) {
            if ($this->product->post_author == \Auth::user()->ID) {

                $this->_setProductVisibility($request, $entity);
                $this->_setProductTypeFields($request, $jsonEntry, false);
                $this->product->meta()->updateOrCreate(['meta_key' => 'product_description'], ['meta_value' => $entity['Version']['Info']]);

                $response = [
                    'id' => $this->product->post_name,
                    'title' => $this->product->post_title,
                    'link' => getSiteUrl() . '/product/' . $this->product->post_name,
                ];
                return $this->respondWithData($response);
            } else {
                return $this->respondForbiddenError('This product was created by another user!');
            }
        }

        $this->product = Post::create([
            'post_title' => $productName,
            'post_name' => $productId,
            'post_type' => 'product-service',
            'post_status' => 'publish',
            'post_author' => \Auth::user()->ID,
            'post_date' => Carbon::now(),
            'comment_status' => 'closed',
            'ping_status' => 'closed',
        ]);

        $this->_setProductVisibility($request, $entity);
        $this->_setProductTypeFields($request, $jsonEntry);

        $protocolVersion = $entity['ProtocolMajor']. '.' . $entity['ProtocolMinor'];
        $this->product->meta()->create(['meta_key' => 'product_id', 'meta_value' => $productId]);
        $this->product->meta()->create(['meta_key' => 'protocol_version', 'meta_value' => $protocolVersion]);
        $this->product->meta()->create(['meta_key' => 'product_manufacturer', 'meta_value' => $entity['Manufacturer']]);
        $this->product->meta()->create(['meta_key' => 'product_description', 'meta_value' => $entity['Version']['Info']]);
        $this->product->meta()->create(['meta_key' => 'product_type', 'meta_value' => $request->get('product_type')]);
        $this->product->meta()->create(['meta_key' => 'product_name', 'meta_value' => $productName]);
        $this->product->meta()->create(['meta_key' => 'product_version', 'meta_value' => $productVersion]);
        
        $this->product->meta()->create(['meta_key' => 'product_organisation_id', 'meta_value' => $request->get('organisation_id')]);

        /**
         * Create test plans for product
         */
        foreach ($user->getUserTestPlans() as $suiteName => $suite) {
            $type = $suite['testSuite']->meta()->where(['meta_key' => 'ts_tester_role'])->first()->meta_value;
            if ($type != $request->get('product_type')) {
                continue;
            }
            $organisationSubscription = OrganisationSubscription::where(['user_id' => $user->ID, 'suite_family_mark' => $suite['testSuite']->ID])->first();
            $pricingPlan = PricingPlan::where(['id' => $organisationSubscription->pricing_plan_id])->with('attributes')->first();
            $attributes = $pricingPlan->attributes->keyBy('type')->get('role');

            /**
             * Skip test plan creation for a test suite if test suite doesnt support product's protocol version
             */
            $testSuiteSupportedProtocols = json_decode($suite['testSuite']->getMetaByKey('protocol_versions'), true);

            if (empty($testSuiteSupportedProtocols) || !in_array($protocolVersion, $testSuiteSupportedProtocols)) {
                continue;
            }
            foreach (explode(',', $attributes->value) as $level) {
                $testPlan = TestPlan::create([
                    'organisation_subscription_id' => $organisationSubscription->id,
                    'product_id' => $this->product->ID,
                    'suite_id' => $suite['testSuite']->ID,
                    'creator_id' => $user->ID,
                    'level' => $level,
                    'role' => $request->get('product_type'),
                ]);
                if ($request->get('product_type') == 'DataSource') {
                    $testPlan->excludeTestCases();
                }
            }
        }

        $response = [
            'id' => $this->product->post_name,
            'title' => $this->product->post_title,
            'link' => getSiteUrl() . '/product/' . $this->product->post_name,
        ];
        return $this->setStatusCode(201)->respondWithData($response);
    }

    /**
     * Generate uri for string
     * @param $str
     * @return mixed
     */
    private function cleanSlug($str)
    {
        return trim(preg_replace('/-{2,}/', '-', strtolower(preg_replace("/[^A-Za-z0-9_]/", '-', $str))), ' _-');
    }

    /**
     * Set up product type fields - capabilities for Application and test suites list for DataSource
     * @param $request
     * @param $jsonEntry
     */
    private function _setProductTypeFields($request, $jsonEntry, $isCreate = true)
    {
        if ($request->get('product_type') == 'DataSource') {
            $this->product->meta()->updateOrCreate(['meta_key' => 'capabilities'], ['meta_value' => json_encode($jsonEntry['Capabilities'])]);
        } else {
            if ($isCreate) {
                $productSuites = [];
                foreach (getUserSubscribedSuites(\Auth::user()->ID) as $suite) {
                    $productType = PostMeta::where(['post_id' => $suite->suite_id, 'meta_key' => 'ts_tester_role'])->first();

                    if (!$productType || $productType->meta_value !== 'DataSource') {
                        continue;
                    }
                    $productSuites[] = $suite->suite_id;
                }
                $this->product->meta()->updateOrCreate(['meta_key' => 'product_suites'], ['meta_value' => json_encode($productSuites)]);
            }
        }
    }

    /**
     * Set product visibility field
     * @param $request
     * @param $entity
     */
    private function _setProductVisibility($request, $entity)
    {
        $organisation = Organisation::find($request->get('organisation_id'));
        $productsOrganisations = json_decode($organisation->products_organisations);
        if (!$productsOrganisations) {
            $productsOrganisations = [$organisation->organisation_name];
        }

        if (in_array($entity['Manufacturer'], $productsOrganisations)) {
            $this->product->meta()->updateOrCreate(['meta_key' => 'product_visibility'], ['meta_value' => 'Public']);
        } else {
            $this->product->meta()->updateOrCreate(['meta_key' => 'product_visibility'], ['meta_value' => 'Private']);
        }
    }
    /**
     * @api {get} /v1/products Get user organisation's products
     ** @apiParam {string} [product_type]  Optional - product type (either 'Application' or 'DataSource').
     *
     * @apiName getProducts
     * @apiGroup Products
     *
     * @apiSuccessExample {json} Products list:
     *  {
     *     "data": [
     *       {
     *         "id": "cn-01a-ds",
     *         "title": "CN-01a DS",
     *         "link": "http://twain.my/product/cn-01a-ds"
     *       },
     *       {
     *         "id": "cn-01a-ds",
     *         "title": "CN-01a DS",
     *         "link": "http://twain.my/product/cn-01a-ds"
     *       },
     *       {
     *         "id": "cn-02a-ds",
     *         "title": "CN-02a DS",
     *         "link": "http://twain.my/product/cn-02a-ds"
     *       }
     *     ],
     *     "code": 200
     *   }
     *
     * @apiError 403 Forbidden
     * @apiErrorExample {json} Not organisation member:
     *   {
     *     "errors": {
     *       "message": [
     *         "Only organisation member can perform testing"
     *       ]
     *     },
     *     "code": 403
     *   }
     *
     * @apiError 404 Products not found
     * @apiErrorExample {json} Products not found error:
     *  {
     *     "errors": {
     *       "message": [
     *          "No products were found for this user!"
*             ]
     *     },
     *     "code": 404
     *   }
     * @apiError 422 Invalid product_type value
     * @apiErrorExample {json} Invalid product_type value:
     *   {
     *     "errors": {
     *       "product_type": [
     *         "The selected product type is invalid."
     *       ]
     *     },
     *     "code": 422
     *   }
     *
     * @apiHeader (Headers) {String} Authorization Authorization value Basic (base64_encode(login:password)).
     *
     * @apiVersion 1.0.0
     */

    public function get(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_type' => 'in:DataSource,Application'
        ]);

         if ($validator->fails()) {
            return $this->respondUnprocessableEntity($validator->messages());
        }

        $userOrganisationId = \Auth::user()->organisation[0]->id;

        if($request->has('product_type')){
            $type = $request->get('product_type');

            $products = DB::table('wp_posts')
            ->join('wp_postmeta AS pm1', function ($join) use ($type) {
                $join->on('pm1.post_id', '=', 'wp_posts.ID')
                    ->where('pm1.meta_value', '=', $type)
                    ->where('pm1.meta_key', '=', 'product_type');
            })
             ->join('wp_postmeta AS pm2', function ($join) use ($userOrganisationId) {
                $join->on('pm1.post_id', '=', 'wp_posts.ID')
                    ->where('pm2.meta_value', '=', $userOrganisationId)
                    ->where('pm2.meta_key', '=', 'product_organisation_id');
            })
            ->where('wp_posts.post_type', '=', 'product-service')
            ->groupBy('wp_posts.ID')
            ->get();

            if(empty($products)){
                 return $this->respondNotFound('No products were found with '.$type.' type for this user!');
            }
        } else {
            $products = DB::table('wp_posts')
                ->join('wp_postmeta AS pm1', function ($join) use ($userOrganisationId) {
                    $join->on('pm1.post_id', '=', 'wp_posts.ID')
                        ->where('pm1.meta_value', '=', $userOrganisationId)
                        ->where('pm1.meta_key', '=', 'product_organisation_id');
                })
                ->where('wp_posts.post_type', '=', 'product-service')
                ->groupBy('wp_posts.ID')
                ->get();
            if (empty($products)) {
                return $this->respondNotFound('No products were found for this user!');
            }
        }

        $response = [];
        foreach($products as $product){
            $response[] = [
                'id' => $product->post_name,
                'title' => $product->post_title,
                'link' => getSiteUrl() . '/product/' . $product->post_name,
            ];
        }
        return $this->respondWithData($response);
    }
}
