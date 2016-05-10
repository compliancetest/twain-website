<?php

namespace App\Api\Controllers;

use App\Jobs\ProcessTransactionLog;
use App\Post;
use App\PostMeta;
use Aws\Laravel\AwsFacade as AWS;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
     * @apiParamExample {json} Example 'identity' value
     *
     *   {
     *       "Identity": {
     *         "Version": "CN-02a_v1.0",
     *         "Protocol": {
     *           "Major": 2,
     *           "Minor": 3
     *         },
     *         "Manufacturer": "Drummond Group",
     *         "Product": {
     *           "Name": "CN-02a DS",
     *           "Family": "Virtual Data Source"
     *         },
     *         "SupportedGroups": "DF_DSM2"
     *       },
     *       "Capabilities": [
     *         "CAP_DEVICEONLINE",
     *         "CAP_SUPPORTEDCAPS",
     *         "CAP_UICONTROLLABLE",
     *         "CAP_XFERCOUNT",
     *         "ICAP_BITDEPTH",
     *         "ICAP_BITORDER",
     *         "ICAP_COMPRESSION",
     *         "ICAP_PHYSICALHEIGHT",
     *         "ICAP_PHYSICALWIDTH",
     *         "ICAP_PIXELFLAVOR",
     *         "ICAP_PIXELTYPE",
     *         "ICAP_PLANARCHUNKY",
     *         "ICAP_UNITS",
     *         "ICAP_XFERMECH",
     *         "ICAP_XNATIVERESOLUTION",
     *         "ICAP_XRESOLUTION",
     *         "ICAP_YNATIVERESOLUTION",
     *         "ICAP_YRESOLUTION"
     *       ]
     *   }
     *
     * @apiName createProduct
     * @apiGroup Products
     *
     * @apiSuccessExample {json} Product created:
     *  {
     *     "data": {
     *       "id": "cn-01a-ds",
     *       "title": "CN-01a DS",
     *       "link": "http:\/\/twain.my\/cn-01a-ds"
     *     },
     *     "code": 201
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
        $entity = json_decode($request->get('identity'), true)['Identity'];
        $productName = htmlspecialchars($entity['Product']['Name']);
        $productId = sanitize_title($entity['Manufacturer']) . "_" . sanitize_title($productName) . "_v" . $entity['Version'];

        $databaseEntry = PostMeta::where(['meta_key' => 'product_id', 'meta_value' => $productId])->first();

        if ($databaseEntry) {
            $product = Post::where(['ID' => $databaseEntry->post_id])->first();
            if ($product->post_author == \Auth::user()->ID) {
                $response = [
                    'id' => $product->post_name,
                    'title' => $product->post_title,
                    'link' => URL::to('/') . '/product/' . $product->post_name,
                ];
                return $this->respondWithData($response);
            } else {
                return $this->respondForbiddenError('This product was created by another user!');
            }
        }

        $product = Post::create([
            'post_title' => $productName,
            'post_name' => sanitize_title($productName),
            'post_type' => 'product-service',
            'post_status' => 'publish',
            'post_author' => \Auth::user()->ID,
            'post_date' => Carbon::now(),
            'comment_status' => 'closed',
            'ping_status' => 'closed',
        ]);

        $userOrganisation = \Auth::user()->organisation[0];

        $product->meta()->create(['meta_key' => 'product_id', 'meta_value' => $productId]);
        $product->meta()->create(['meta_key' => 'product_name', 'meta_value' => sanitize_title($productName)]);
        $product->meta()->create(['meta_key' => 'product_version', 'meta_value' => htmlspecialchars($entity['Version'])]);
        $product->meta()->create(['meta_key' => 'product_visibility', 'meta_value' => 'Public']);
        $product->meta()->create(['meta_key' => 'product_organisation_id', 'meta_value' => $userOrganisation->id]);

        $response = [
            'id' => $product->post_name,
            'title' => $product->post_title,
            'link' => URL::to('/') . '/product/' . $product->post_name,
        ];
        return $this->setStatusCode(201)->respondWithData($response);
    }

    /**
     * @api {get} /v1/products Get user's products
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
     * @apiError 404 Products not found
     * @apiErrorExample {json} Products not found error:
     *  {
     *     "error": {
     *       "message": "No products were found for this user!"
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

        if($request->has('product_type')){
            $type = $request->get('product_type');
            $products = DB::table('wp_posts')
            ->join('wp_postmeta AS pm1', function ($join) use ($type) {
                    $join->on('pm1.post_id', '=', 'wp_posts.ID')
                        ->where('pm1.meta_value', '=', $type)
                        ->where('pm1.meta_key', '=', 'product_type');
                })
            ->where('wp_posts.post_type', '=', 'product-service')
            ->where('wp_posts.post_author', '=', \Auth::user()->ID)
            ->get();
            if(empty($products)){
                 return $this->respondNotFound('No products were found with '.$type.' type for this user!');
            }
        } else {
            $products = Post::where(['post_author' => \Auth::user()->ID, 'post_type' => 'product-service'])->get();
            if($products->isEmpty()){
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
