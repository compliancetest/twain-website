<?php

namespace App\Api\Controllers;

use App\CommunityDownloads;
use Symfony\Component\HttpKernel;
use Validator;

class VersionController extends BaseApiController
{

    /**
    * @api {get} /v1/version Get latest version
    * @apiParam {string} test_tool_for  Mandatory - either 'Application' or 'DataSource'.
    * @apiParam {boolean} [installer]  Optional - A flag indicates either an installer (true) or an archive (false) should be returned. By default - false.
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
    *       "test_tool_for": [
    *         "The test tool for field is required."
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
    *          "Downloads not found"
    *       ]
    *     },
    *     "code": 404
    *  }
    **
    * @apiHeader (Headers) {String} Authorization Authorization value Basic (base64_encode(login:password)).
    *
    * @apiVersion 1.0.0
    */
    public function index(\Illuminate\Http\Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_tool_for' => 'required|in:Application,DataSource'
        ]);

        if ($validator->fails()) {
            return $this->respondUnprocessableEntity($validator->messages());
        }

        $isInstaller = !$request->get('installer') || $request->get('installer') == 'false' ? false : true;

        $latestVersion = CommunityDownloads::where(['product_type' => $request->get('test_tool_for'), 'is_installer' => $isInstaller])->orderBy('created_at', 'DESC')->first();

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

        return $this->respondNotFound('Downloads not found');
    }
}
