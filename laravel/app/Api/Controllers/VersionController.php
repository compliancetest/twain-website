<?php

namespace App\Api\Controllers;

use App\CommunityDownloads;
use Symfony\Component\HttpKernel;
use Validator;

class VersionController extends BaseApiController
{

    /**
    * @api {post} /v1/version Get latest version
    * @apiParam {string} product_type  Mandatory - Product Type (either 'Application' or 'DataSource').
    *
    * @apiName Latest version
    * @apiGroup Helpers
    *
    * @apiSuccessExample {json} Success-Response:
    *   {
    *     "data": {
    *       "file_name": "file.zip",
    *       "product_type": "Application",
    *       "version": "v1",
    *       "description": "description",
    *       "size": 106876,
    *       "license_agreement": "License agreement",
    *       "last_updated": "2016-07-11 16:38:11",
    *       "s3_link": "https://s3-us-west-2.amazonaws.com"
    *     },
    *     "code": 200
    *   }
    * @apiError 422 Validation error
    * @apiErrorExample {json} Validation error
    *  {
    *     "errors": {
    *       "product_type": [
    *         "The product type field is required."
    *       ]
    *     },
    *     "code": 422
    *  }
    *
    * @apiError 404 Not found
    * @apiErrorExample {json} Not found
    *  {
    *     "errors": {
    *       "message": [
    *          "Downloads with such product type not found"
    *       ]
    *     },
    *     "code": 404
    *  }
    *
    * @apiVersion 1.0.0
    */
    public function index(\Illuminate\Http\Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_type' => 'required|in:Application,DataSource'
        ]);

        if ($validator->fails()) {
            return $this->respondUnprocessableEntity($validator->messages());
        }

        $latestVersion = CommunityDownloads::where(['product_type' => $request->get('product_type')])->orderBy('created_at', 'DESC')->first();

        if ($latestVersion) {
            return $this->respondWithData([
                'file_name' => $latestVersion->title,
                'product_type' => $latestVersion->product_type,
                'version' => $latestVersion->version,
                'description' => $latestVersion->description,
                'size' => $latestVersion->size,
                'license_agreement' => $latestVersion->license,
                'last_updated' => $latestVersion->updated_at->format('Y-m-d H:i:s'),
                's3_link' => $latestVersion->getS3Link(),
            ]);
        }

        return $this->respondNotFound();
    }
}
