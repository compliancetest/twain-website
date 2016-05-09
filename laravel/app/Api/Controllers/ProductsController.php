<?php

namespace App\Api\Controllers;

use App\Jobs\ProcessTransactionLog;
use App\Post;
use App\PostMeta;
use Aws\Laravel\AwsFacade as AWS;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpKernel;
use Validator;

class ProductsController extends BaseApiController
{

    /**
     * @api {post} /v1/products Create product
     *
     * @apiParam {JSON} identity  Mandatory - product identity json.
     * @apiParamExample {json} App example
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
     *     "ProductType": "DataSource",
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
     * @apiParamExample {json} DS example
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
     *       },
     *       "ProductType": "Application"
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
     *  {
     *     "errors": {
     *       "identity": [
     *         "The identity must be a valid JSON string."
     *       ]
     *     },
     *     "code": 422
     *   }
     *
     * @apiError 403 Permissions error
     * @apiErrorExample {json} Permissions error:
     * {
     *    "error": {
     *      "message": "This product was created by another user!"
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
            'identity' => 'required|json'
        ]);

        if ($validator->fails()) {
            return $this->respondUnprocessableEntity($validator->messages());
        }
        $jsonEntry = json_decode($request->get('identity'), true);
        $entity = $jsonEntry['Identity'];
        $productName = htmlspecialchars($entity['ProductName']);
        $productVersion = $entity['Version']['MajorNum'] . '.' . $entity['Version']['MinorNum'];
        $productId = sanitize_title($entity['Manufacturer']) . "_" . sanitize_title($productName) . "_V" . $productVersion;

        $databaseEntry = PostMeta::where(['meta_key' => 'product_id', 'meta_value' => $productId])->first();

        if ($databaseEntry) {
            $product = Post::where(['ID' => $databaseEntry->post_id])->first();
            if ($product->post_author == \Auth::user()->ID) {
                $response = [
                    'id' => $product->post_name,
                    'title' => $product->post_title,
                    'link' => getSiteUrl() . '/product/' . $product->post_name,
                ];
                return $this->respondWithData($response);
            } else {
                return $this->respondForbiddenError('This product was created by another user!');
            }
        }

        $product = Post::create([
            'post_title' => $productName,
            'post_name' => sanitize_title($entity['ProductName'] .'_v_' . $entity['Version']['MajorNum'] . '.' . $entity['Version']['MinorNum']),
            'post_type' => 'product-service',
            'post_status' => 'publish',
            'post_author' => \Auth::user()->ID,
            'post_date' => Carbon::now(),
            'comment_status' => 'closed',
            'ping_status' => 'closed',
        ]);
        if (isset($jsonEntry['Capabilities'])) {
            $product->meta()->create(['meta_key' => 'capabilities', 'meta_value' => json_encode($jsonEntry['Capabilities'])]);
        }
        $product->post_name = Post::getUniquePostName($product, $product->post_name);
        $product->save();

        $userOrganisation = \Auth::user()->organisation[0];

        $product->meta()->create(['meta_key' => 'product_id', 'meta_value' => $productId]);
        $product->meta()->create(['meta_key' => 'product_type', 'meta_value' => $jsonEntry['ProductType']]);
        $product->meta()->create(['meta_key' => 'product_name', 'meta_value' => sanitize_title($productName)]);
        $product->meta()->create(['meta_key' => 'product_version', 'meta_value' => $productVersion]);
        $product->meta()->create(['meta_key' => 'product_visibility', 'meta_value' => 'Public']);
        $product->meta()->create(['meta_key' => 'product_organisation_id', 'meta_value' => $userOrganisation->id]);

        $response = [
            'id' => $product->post_name,
            'title' => $product->post_title,
            'link' => getSiteUrl() . '/product/' . $product->post_name,
        ];
        return $this->setStatusCode(201)->respondWithData($response);
    }

    /**
     * @api {get} /v1/products Get user's products
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
     * @apiError 404 Products not found
     * @apiErrorExample {json} Products not found error:
     *  {
     *     "error": {
     *       "message": "No products were found for this user!"
     *     },
     *     "code": 404
     *   }
     *
     * @apiHeader (Headers) {String} Authorization Authorization value Basic (base64_encode(login:password)).
     *
     * @apiVersion 1.0.0
     */

    public function get()
    {
        $products = Post::where(['post_author' => \Auth::user()->ID, 'post_type' => 'product-service'])->get();

        if($products->isEmpty()){
             return $this->respondNotFound('No products were found for this user!');
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
