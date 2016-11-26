<?php

namespace App\Api\v2\Controllers;

use Validator;
use App\Product;
use App\TestPlan;
use App\Organisation;
use App\CommunityMembers;
use App\LaravelTestSuite;
use App\OrganisationMember;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel;
use App\OrganisationSubscription;
use Illuminate\Support\Facades\Auth;
use App\CommunityOrganisationsApprovedProducts;
use App\CommunityOrganisationsApprovedTestSuites;

class ProductsController extends BaseApiController
{

    private $product;

    /**
     * @api {post} /v2/products Create product
     * @apiVersion 2.0.0
     *
     * @apiParam {JSON} identity  Mandatory - product identity json.
     * @apiParam {string} product_type  Mandatory - product type (either 'DataSource' or 'Application')
     * @apiParam {integer} organisation_id  Mandatory - User's organisation ID
     * @apiParam {string} [product_id]  Optional - Product with this ID will be updated
     * @apiParamExample {json} DataSource example
     *
     *  {
     *       "Identity": {
     *           "ProtocolMajor": 2,
     *           "ProtocolMinor": 322,
     *           "Manufacturer": "TEST!!",
     *           "ProductName": "1111",
     *           "ProductFamily": "Software Scan",
     *           "Version": {
     *               "MajorNum": 2,
     *               "MinorNum": 201,
     *               "Language": "TWLG_ENGLISH",
     *               "Country": "TWCY_USA",
     *               "Info": "2.1.3 sample debug 32bit"
     *           },
     *           "SupportedGroups": ["DG_CONTROL",
     *           "DG_IMAGE",
     *           "DF_DS2"]
     *       },
     *       "Model": "TEST_MODEL1",
     *       "Capabilities": ["CAP_SUPPORTEDCAPS",
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
     *       "ICAP_YRESOLUTION"]
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
     *          "SupportedGroups": ["DG_CONTROL",
     *              "DG_IMAGE",
     *              "DF_DS2"]
     *       },
     *       "Model": "TEST_MODEL2"
     *   }
     * @apiName createProduct
     * @apiGroup Products
     *
     * @apiSuccessExample {json} Product exist (approved):
     *  {
     * "messages": [
     * "The product has been updated successfully"
     * ],
     * "data": {
     * "id": "4_twain-working-group_77121542111111twain-virtual-software-scanner_v2-1_none2",
     * "title": "77121542111111TWAIN Virtual Software Scanner v2.1",
     * "link": "http://twain.my/product/4_twain-working-group_77121542111111twain-virtual-software-scanner_v2-1_none2",
     * "model": "None2"
     * },
     * "status": "success",
     * "code": 200
     * }
     *
     * @apiSuccessExample {json} Product exist (not approved):
     *  {
     * "messages": [
     * "This product registration will require approval"
     * ],
     * "data": {
     * "id": "4_twain-working-group_77121542111111twain-virtual-software-scanner_v2-1_none2",
     * "title": "77121542111111TWAIN Virtual Software Scanner v2.1",
     * "link": "http://twain.my/product/4_twain-working-group_77121542111111twain-virtual-software-scanner_v2-1_none2",
     * "model": "None2"
     * },
     * "status": "info",
     * "code": 200
     * }
     *
     * @apiSuccessExample {json} Product created:
     *  {
     * "messages": [
     * "This product registration will require approval"
     * ],
     * "data": {
     * "id": "4_twain-working-group_77121542111111twain-virtual-software-scanner_v2-1_none2",
     * "title": "77121542111111TWAIN Virtual Software Scanner v2.1",
     * "link": "http://twain.my/product/4_twain-working-group_77121542111111twain-virtual-software-scanner_v2-1_none2",
     * "model": "None2"
     * },
     * "status": "info",
     * "code": 201
     * }
     * @apiError 422 Validation error
     * @apiErrorExample {json} Validation error:
     *  {
     * "messages": [The product type field is required."],
     * "status": "error",
     * "code": 422
     * }
     *
     * @apiError 422 Validation error
     * @apiErrorExample {json} Validation error:
     *  {
     * "messages": [
     * "The identity field is required.",
     * "The organisation id field is required."
     * ],
     * "status": "error",
     * "code": 422
     * }
     *
     * @apiError 403 Forbidden
     * @apiErrorExample {json} Not organization member:
     *  {
     * "messages": [
     * "Only organization member can perform testing"
     * ],
     * "status": "error",
     * "code": 403
     * }
     *
     * @apiError 403 Forbidden
     * @apiErrorExample {json} Organization is not approved yet:
     *
     * {
     * "messages": [
     * "Your organization can't perform testing."
     * ],
     * "status": "error",
     * "code": 403
     * }
     *
     * @apiError 403 Forbidden
     * @apiErrorExample {json} No subscription with provided Product Type:
     *
     *  {
     * "messages": [
     * "Please subscribe to Test Suite with '{Application|DataSource}' Product Type"
     * ],
     * "status": "error",
     * "code": 403
     * }
     *
     * @apiError 403 Forbidden
     * @apiErrorExample {json} Permissions error:
     *
     * {
     * "messages": [
     * "This product was created by another organisation!"
     * ],
     * "status": "error",
     * "code": 403
     * }
     *
     * @apiHeader (Headers) {String} Authorization Authorization value Basic (base64_encode(login:password)).
     *
     */
    public function create(\Illuminate\Http\Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identity' => 'required|json',
            'product_type' => 'required|in:DataSource,Application',
            'organisation_id' => 'required|exists:wp_organisations,id',
            'product_id' => 'exists:wp_posts,post_name',
        ]);

        $user = \Auth::user();
        if ($validator->fails()) {
            return $this->respondUnprocessableEntity($validator->messages());
        }
        $jsonEntry = json_decode($request->get('identity'), true);
        $entity = $jsonEntry['Identity'];
        $productName = $entity['ProductName'];
        $productModel = !empty($jsonEntry['Model']) ? (string)$jsonEntry['Model'] : null;
        $productVersion = $entity['Version']['MajorNum'] . '.' . $entity['Version']['MinorNum'];
        if (!empty($request->get('product_id'))) {
            $productId = $request->get('product_id');
        } else {
            $productId = $request->get('organisation_id') . '_' . $this->cleanSlug($entity['Manufacturer']) . "_" . $this->cleanSlug($productName) . "_v" . str_replace('.', '-', $productVersion);
            if (!is_null($productModel)) {
                $productId .= '_' . $this->cleanSlug($productModel);
            }
        }
        $protocolVersion = $entity['ProtocolMajor'] . '.' . $entity['ProtocolMinor'];
        $this->product = Product::findBySlug($productId);
        if ($this->product) {
            //any organisation member can update product
            if (isset($user->organisation[0]->id) && OrganisationMember::where(['organisation_id' => $user->organisation[0]->id, 'user_id' => $this->product->user_id])->first()) {
                $this->_setProductVisibility($this->product, $request, $entity);
                $this->_setProductTypeFields($this->product, $request, $jsonEntry, false);

                /**
                 * Recreate test plans for DataSource product.
                 * Test plans for Application product will be regenerated once user update supported features
                 */
                if ($request->get('product_type') == 'DataSource') {
                    $this->generateTestPlans($this->product->id, $request, $protocolVersion);
                }

                $this->product->protocol_version = $protocolVersion;
                $this->product->description = $entity['Version']['Info'];
                $this->product->model = $productModel;

                $this->product->full_name = $productName . ' v' . $productVersion;
                if (!empty($productModel)) {
                    $this->product->full_name = $this->product->full_name . ' for ' . $productModel;
                }

                $this->product->save();

                if (!CommunityOrganisationsApprovedProducts::where('product_id', $this->product->id)->first()) {
                    $message = 'The product has been updated successfully';
                    $status = 'success';
                } else {
                    $status = 'info';
                    $message = 'This product registration will require approval';
                    $this->setStatusCode(403);
                }
                $response = [
                    'id' => $this->product->slug,
                    'title' => $this->product->full_name,
                    'link' => getSiteUrl() . '/product/' . $this->product->slug,
                    'model' => $productModel,
                ];
                return $this->respondWithDataAndMessage($message, $response, $status);
            } else {
                return $this->respondForbiddenError('This product was created by another organisation!');
            }
        }

        $this->product = Product::create([
            'name' => $productName,
            'slug' => $productId,
            'user_id' => \Auth::user()->ID,
            'model' => (string) $productModel,
            'protocol_version' => $protocolVersion,
            'manufacturer' => $entity['Manufacturer'],
            'description' => $entity['Version']['Info'],
            'type' => $request->get('product_type'),
            'version' => $productVersion,
            'organisation_id' => $request->get('organisation_id'),
        ]);

        $this->_setProductVisibility($this->product, $request, $entity);
        $this->_setProductTypeFields($this->product, $request, $jsonEntry);

        $this->product->full_name = $productName . ' v' . $protocolVersion;
        if (!empty($productModel)) {
            $this->product->full_name .= ' for ' . $productModel;
        }

        /**
         * Create test plans for DataSource product.
         * Test plans for application product will be generated once user set supported features
         */
        if ($request->get('product_type') == 'DataSource') {
            $this->generateTestPlans($this->product->id, $request, $protocolVersion);
        }

        $this->product->save();

        $response = [
            'id' => $this->product->slug,
            'title' => $this->product->name . ' v' . $productVersion,
            'link' => getSiteUrl() . '/product/' . $this->product->slug,
            'model' => $productModel,
        ];

        $emailData = [
            '[author_name]' => cp_get_user_fullname($this->product->user_id),
            '[product_url]' => getSiteUrl() . '/product/' . $this->product->slug,
            '[product_name]' => $this->product->full_name,
            '[site_title]' => get_site_title(),
            '[organisation]' => Auth::user()->organisation[0]->organisation_name,
            '[env]' => get_option('env'),
            '[website_url]' => getSiteUrl(),
        ];
        sendEmails(CommunityMembers::where('is_mod', true)->orWhere('is_admin', true)->get()->toArray(), 'product_approvement_to_admin', $emailData);

        return $this->setStatusCode(201)->respondWithDataAndMessage('This product registration will require approval', $response, 'info');
    }

    /**
     * generate test plans for product. old test plans will be deleted
     * @param $productId
     * @param Request $request
     * @param $protocolVersion
     */
    public function generateTestPlans($productId, Request $request, $protocolVersion)
    {
        $user = Auth::user();
        TestPlan::where('product_id', $productId)->delete();
        foreach ($user->getUserTestPlans() as $suiteName => $suite) {
            $type = $suite['testSuite']->product_type;
            $aprovementEntry = CommunityOrganisationsApprovedTestSuites::where(['organisation_id' => $request->get('organisation_id'), 'suite_major_family_mark' => $suite['testSuite']->major_family_mark])->first();
            if ($type != $request->get('product_type') || !$aprovementEntry) {
                continue;
            }
            $organisationSubscription = OrganisationSubscription::where(['user_id' => $user->ID, 'suite_minor_family_mark' => $suite['testSuite']->minor_family_mark])->first();
            if (!$organisationSubscription) {
                continue;
            }

            /**
             * Skip test plan creation for a test suite if test suite doesnt support product's protocol version
             */
            $testSuiteSupportedProtocols = $suite['testSuite']->protocolVersions()->pluck('version')->toArray();

            if (count($testSuiteSupportedProtocols) && !in_array($protocolVersion, $testSuiteSupportedProtocols)) {
                continue;
            }
            foreach ($suite['testSuite']->conformanceLevels as $level) {
                $testPlan = TestPlan::create([
                    'organisation_subscription_id' => $organisationSubscription->id,
                    'product_id' => $this->product->id,
                    'suite_minor_family_mark' => $suite['testSuite']->minor_family_mark,
                    'creator_id' => $user->ID,
                    'level' => $level->code,
                    'role' => $request->get('product_type'),
                ]);
                if ($request->get('product_type') == 'DataSource') {
                    $testPlan->excludeTestCases();
                }
            }
        }
    }

    /**
     * @api {get} /v2/products/{productId}/features Get product features
     * @apiVersion 2.0.0
     *
     * @apiName Supported features
     * @apiGroup Products
     *
     * @apiSuccessExample {json} Product's features list
     *
     *   {
     *      "data": [
     *        {
     *          "id": "twain-v2-3-compliance-applications-v1-0",
     *          "title": "TWAIN v2.3 Compliance - Applications v1.0",
     *          "status": true,
     *          "features": [
     *            {
     *              "title": "UI image transfer",
     *              "description": "UI image transfer",
     *              "status": true
     *            },
     *            {
     *              "title": "Non-UI image transfer",
     *              "description": "Non-UI image transfer",
     *              "status": false
     *            }
     *          ]
     *        }
     *      ],
     *      "status": "success",
     *      "code": 200
     *    }
     *
     *
     * @apiError 404 Invalid product ID
     * @apiErrorExample {json} Invalid product ID
     *   {
     *      "messages": ["Product id is invalid"],
     *      "status": "error",
     *      "code": 404
     *    }
     *
     * @apiError 403 Forbidden
     * @apiErrorExample {json} Not organization member
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
     *
     * @apiError 403 Forbidden
     * @apiErrorExample {json} Invalid product type
     *  {
     *      "messages": ["This product has incorrect type"],
     *      "status": "error",
     *      "code": 403
     *    }
     *
     * @apiHeader (Headers) {String} Authorization Authorization value Basic (base64_encode(login:password)).
     *
     */

    public function listFeatures($productSlug)
    {
        $product = Product::findBySlug($productSlug);

        if ($product->type !== 'Application') {
            return $this->respondForbiddenError('This product has incorrect type');
        }

        $result = [];
        foreach (Auth::user()->suiteSubscriptions as $suite) {

            if ($suite->testSuite->product_type !== 'Application') {
                continue;
            }

            $suiteFeatures = $suite->testSuite->features;
            $suiteData = [
                'id' => $suite->testSuite->slug,
                'title' => $suite->testSuite->full_name,
                'status' => $product->features()->whereIn('test_suites_feature_id', $suite->testSuite->features()->pluck('id'))->first() ? true : false,
                'features' => [],
            ];

            foreach ($suiteFeatures as $suiteFeature) {
                $suiteData['features'][] = [
                    'title' => $suiteFeature->name,
                    'description' => $suiteFeature->description,
                    'status' => count($product->features->where('test_suites_feature_id', $suiteFeature->id)) ? true : false,
                ];
            }
            $result[] = $suiteData;
        }

        return $this->respondWithData($result);
    }

    /**
     * @api {post} /v2/products/{productId}/features Set product features
     * @apiVersion 2.0.0
     *
     * @apiParam {JSON} features  Mandatory - features json.
     *
     * @apiParamExample {json} Features JSON example
     *
     *   [{
     *       "id": "twain-v2-3-compliance-applications-v1-0",
     *       "features": ["UI image transfer"]
     *   }]
     *
     * @apiName Set Features List
     * @apiGroup Products
     *
     * @apiSuccessExample {json} Features saved
     *   {
     *      "data": [
     *        {
     *          "id": "twain-v2-3-compliance-applications-v1-0",
     *          "title": "TWAIN v2.3 Compliance - Applications v1.0",
     *          "status": true,
     *          "features": [
     *            {
     *              "title": "UI image transfer",
     *              "description": "UI image transfer",
     *              "status": true
     *            },
     *            {
     *              "title": "Non-UI image transfer",
     *              "description": "Non-UI image transfer",
     *              "status": false
     *            }
     *          ]
     *        }
     *      ],
     *      "status": "success",
     *      "code": 200
     *    }
     *
     *
     * @apiError 422 Validation error
     * @apiErrorExample {json} Validation error
     *   {
     *     "message": ["The features field is required."],
     *     "status": "error",
     *     "code": 422
     *   }
     *
     * @apiError 422 Validation error
     * @apiErrorExample {json} Validation error
     * {
     *     "messages": [
     *         "Test suite id is invalid. Feature index - 0.",
     *         "Test suite id is invalid. Feature index - 1.",
     *         "Features field should be an array. Feature index - 1."
     *       ],
     *     "status": "error",
     *     "code": 422
     *   }
     *
     * @apiError 403 Forbidden
     * @apiErrorExample {json} Not organization member
     *   {
     *     "messagse": ["Only organization member can perform testing"],
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
     * @apiError 404 Invalid product ID
     * @apiErrorExample {json} Invalid product ID
     *   {
     *      "messages": ["Product id is invalid"],
     *      "status": "error",
     *      "code": 404
     *    }
     *
     * @apiError 404 Invalid test suite ID
     * @apiErrorExample {json} Invalid test suite ID
     * {
     *   "messages": ["Test suite ID is invalid"],
     *   "status": "error",
     *   "code": 404
     * }
     *
     * @apiError 403 Forbidden
     * @apiErrorExample {json} Invalid product type
     *  {
     *      "message": ["This product has incorrect type"],
     *      "status": "error",
     *      "code": 403
     *    }
     *
     * @apiHeader (Headers) {String} Authorization Authorization value Basic (base64_encode(login:password)).
     *
     */

    public function saveFeatures($productSlug, Request $request)
    {
        $product = Product::findBySlug($productSlug);

        if ($product->type !== 'Application') {
            return $this->respondForbiddenError('This product has incorrect type');
        }

        $validator = Validator::make($request->all(), [
            'features' => 'required|json',
        ]);

        if ($validator->fails()) {
            return $this->respondUnprocessableEntity($validator->messages());
        }

        $features = json_decode($request->get('features'), true);

        $errors = [];
        foreach ($features as $key => $feature) {
            $validator = Validator::make($feature, [
                'id' => 'required|string|exists:test_suites,slug',
                'features' => 'array',
            ],
                [
                    'id.required' => 'Test suite id field is required',
                    'id.string' => 'Test suite id field is required and should be a string',
                    'id.exists' => 'Test suite id is invalid',
                    'features.array' => 'Features field should be an array',
                ]
            );
            if ($validator->fails()) {
                foreach ($validator->messages()->toArray() as $errorMessage) {
                    $errors[] = $errorMessage[0] . sprintf('. Feature index - %d.', $key);
                }
            }
        }
        if ($errors) {
            return $this->respondUnprocessableEntity(['message' => $errors]);
        }

        $productFeatures = [];

        foreach ($features as $testSuite) {
            $testSuiteEntry = LaravelTestSuite::findBySlug($testSuite['id']);
            foreach ($testSuite['features'] as $feature) {
                $testSuiteFeature = $testSuiteEntry->features->where('name', $feature)->first();
                if ($testSuiteFeature) {
                    $productFeatures[] = $testSuiteFeature->id;
                    $product->features()->updateOrCreate(['test_suites_feature_id' => $testSuiteFeature->id]);
                }
            }
        }
        if ($productFeatures) {
            $product->features()->whereNotIn('test_suites_feature_id', $productFeatures)->delete();
        }

        //delete old and create new test plans
        TestPlan::where(['product_id' => $product->id, 'role' => 'Application'])->delete();

        //generate new test plans
        $protocolVersion = $product->protocol_version;

        $user = Auth::user();
        foreach ($user->getUserTestPlans() as $suiteName => $suite) {
            if ($suite['testSuite']->product_type != 'Application' || !in_array($suite['testSuite']->slug, array_values(array_column($features, 'id')))) {
                continue;
            }
            $organisationSubscription = OrganisationSubscription::where(['user_id' => $user->ID, 'suite_minor_family_mark' => $suite['testSuite']->minor_family_mark])->first();

            /**
             * Skip test plan creation for a test suite if test suite doesnt support product's protocol version
             */
            $testSuiteSupportedProtocols = $suite['testSuite']->protocolVersions()->pluck('version')->toArray();

            if (!empty($testSuiteSupportedProtocols) && !in_array($protocolVersion, $testSuiteSupportedProtocols)) {
                continue;
            }
            foreach ($suite['testSuite']->conformanceLevels as $level) {
                $testPlan = TestPlan::create([
                    'organisation_subscription_id' => $organisationSubscription->id,
                    'product_id' => $product->id,
                    'suite_minor_family_mark' => $suite['testSuite']->minor_family_mark,
                    'creator_id' => $user->ID,
                    'level' => $level->code,
                    'role' => 'Application',
                ]);
                $testPlan->excludeTestCases('Application', $productFeatures);
            }
        }

        return $this->listFeatures($productSlug);
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
    private function _setProductTypeFields(&$product, $request, $jsonEntry, $isCreate = true)
    {
        if ($request->get('product_type') == 'DataSource') {
            $capabilities = $jsonEntry['Capabilities'];
            if (is_array($capabilities)) {
                $processedCapabilities = [];
                foreach ($capabilities as $capability) {
                    $product->capabilities()->updateOrCreate(['capability' => $capability]);
                    $processedCapabilities[] = $capability;
                }
                $product->capabilities()->whereNotIn('capability', $processedCapabilities)->delete();

            }
        } else {
            if ($isCreate) {
                $productFeatures = [];
                foreach (Auth::user()->suiteSubscriptions as $suite) {
                    if ($suite->testSuite->product_type !== 'DataSource') {
                        continue;
                    }
                    foreach ($suite->testSuite->features as $feature) {
                        $productFeatures[] = $feature->id;
                        $product->features()->updateOrCreate([
                            'test_suites_feature_id' => $feature->id
                        ]);
                    }
                }
                if (!empty($productFeatures)) {
                    $product->features()->whereNotIn('test_suites_feature_id', $productFeatures)->delete();
                }
            }
        }
    }

    /**
     * Set product visibility field
     * @param $request
     * @param $entity
     */
    private function _setProductVisibility(&$product, $request, $entity)
    {
        $organisation = Organisation::find($request->get('organisation_id'));
        $productsOrganisations = json_decode($organisation->products_organisations);
        if (!$productsOrganisations) {
            $productsOrganisations = [$organisation->organisation_name];
        }

        if (in_array($entity['Manufacturer'], $productsOrganisations)) {
            $product->visibility = 'Public';
        } else {
            $product->visibility = 'Private';
        }
    }

    /**
     * @api {get} /v2/products Get user organisation's products
     * @apiVersion 2.0.0
     *
     ** @apiParam {string} [product_type]  Optional - product type (either 'Application' or 'DataSource').
     *
     * @apiName getProducts
     * @apiGroup Products
     *
     * @apiSuccessExample {json} Products list:
     *   {
     * "data": [{
     * "id": "kv-s1026c-twain-driver",
     * "title": "KV-S1026C twain driver v15.0",
     * "link": "https://www-preproduction-twain.ct01.gosource.com.au/product/kv-s1026c-twain-driver",
     * "model": null,
     * "approved": false
     * }, {
     * "id": "6_panasonic-system-networks-co-lt_panasonic-kv-s1026c-kv-s1015c_v15-0",
     * "title": "Panasonic KV-S1026C KV-S1015C v15.0",
     * "link": "https://www-preproduction-twain.ct01.gosource.com.au/product/6_panasonic-system-networks-co-lt_panasonic-kv-s1026c-kv-s1015c_v15-0",
     * "model": null,
     * "approved": true
     * }, {
     * "id": "6_panasonic-system-networks-co-lt_panasonic-kv-s1026c-kv-s1015c_v15-0_kv-s1026c",
     * "title": "Panasonic KV-S1026C KV-S1015C v15.0 for KV-S1026C",
     * "link": "https://www-preproduction-twain.ct01.gosource.com.au/product/6_panasonic-system-networks-co-lt_panasonic-kv-s1026c-kv-s1015c_v15-0_kv-s1026c",
     * "model": "KV-S1026C",
     * "approved": false
     * }],
     * "status": "success",
     * "code": 200
     * }
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
     * @apiError 422 Invalid product_type value
     * @apiErrorExample {json} Invalid product_type value:
     *   {
     *     "messages": ["The selected product type is invalid."],
     *     "status": "error",
     *     "code": 422,
     *   }
     *
     * @apiHeader (Headers) {String} Authorization Authorization value Basic (base64_encode(login:password)).
     *
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

        $query = Product::where(['organisation_id' => $userOrganisationId]);
        if ($request->has('product_type')) {
            $query->where('type',  $request->get('product_type'));
        }

        $response = [];
        foreach ($query->get() as $product) {
            $response[] = [
                'id' => $product->slug,
                'title' => $product->full_name,
                'link' => getSiteUrl() . '/product/' . $product->slug,
                'model' => !empty($product->model) ? $product->model : null,
                'approved' => CommunityOrganisationsApprovedProducts::where('product_id', $product->id)->first() ? true : false,
            ];
        }
        return $this->respondWithData($response);
    }
}
