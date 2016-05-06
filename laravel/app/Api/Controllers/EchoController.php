<?php

namespace App\Api\Controllers;

use App\Jobs\ProcessTransactionLog;
use Aws\Laravel\AwsFacade as AWS;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel;
use Validator;

class EchoController extends BaseApiController
{

    /**
     * @api {post} /v1/echo Validate credentials
     * @apiParam {string} username  Mandatory - username / email.
     * @apiParam {string} password  Mandatory - password.
     *
     * @apiName validateCredentials
     * @apiGroup Helpers
     *
     * @apiSuccessExample {json} Success-Response:
     *  {
     *       "message": "Valid Credentails!",
     *       "code": 200
     *  }
     * @apiError 422 Validation error
     * @apiErrorExample {json} Validation error:
     *  {
     *     "errors": {
     *       "username": [
     *         "The username field is required."
     *       ],
     *       "password": [
     *         "The password field is required."
     *       ]
     *     },
     *     "code": 422
     *  }
     *
     * @apiError 401 Unauthorized
     * @apiErrorExample {json} Unauthorized:
     *  {
     *     "error": {
     *       "message": "Unauthorized!"
     *     },
     *     "code": 401
     *  }
     *
     * @apiVersion 1.0.0
     */
    public function index(\Illuminate\Http\Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->respondUnprocessableEntity($validator->messages());
        }

        $credentailsEmail = [
            'user_email' => $request->get('username'),
            'password' => $request->get('password'),
        ];

        $credentailsLogin = [
            'user_login' => $request->get('username'),
            'password' => $request->get('password'),
        ];
        if ( \Auth::attempt($credentailsEmail, false) || \Auth::attempt($credentailsLogin, false)){
            return $this->respondSuccess('Valid Credentails!');
        }
        return $this->respondUnauthorizedError();
    }
}
