<?php

namespace App\Api\v2\Controllers;

use App\Jobs\ProcessTransactionLog;
use App\OrganisationMember;
use App\User;
use App\UserSubscription;
use Aws\Laravel\AwsFacade as AWS;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel;
use Validator;

class EchoController extends BaseApiController
{

    /**
     * @api {post} /v2/echo Validate credentials
     * @apiVersion 2.0.0
     * @apiParam {string} username  Mandatory - username / email.
     * @apiParam {string} password  Mandatory - password.
     *
     * @apiName validateCredentials
     * @apiGroup Helpers
     *
     * @apiSuccessExample {json} Success-Response:
     *   {
     *     "data": {
     *       "organisation_id": 4
     *     },
     *     "status": "success",
     *     "code": 200
     *   }
     * @apiError 422 Validation error
     * @apiErrorExample {json} Validation error:
     *   {
     *     "messages": [
     *       "The username field is required.",
     *       "The password field is required."
     *     ],
     *     "status": "error",
     *     "code": 422
     *   }
     *
     * @apiError 401 Unauthorized
     * @apiErrorExample {json} Unauthorized:
     *  {
     *     "messages": ["Unauthorized!"],
     *     "status": "error",
     *     "code": 401
     *   }
     *
     *
     * @apiError 403 Forbidden
     * @apiErrorExample {json} Forbidden:
     *   {
     *     "messages": [
     *       "Only organisation member can perform testing"
     *     ],
     *     "status": "error",
     *     "code": 403
     *   }
     * 
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
        if (\Auth::attempt($credentailsEmail, false) || \Auth::attempt($credentailsLogin, false)){
            $user = User::where(['user_login' => $request->get('username')])->orWhere(['user_email' => $request->get('username')])->first();

            $organisation = OrganisationMember::where(['user_id' => $user->ID])->first();

            if ($organisation) {
                return $this->respondWithData([
                    'organisation_id' => $organisation->organisation_id
                ]);
            } else {
                return $this->respondForbiddenError('Only organisation member can perform testing');
            }
        }
        return $this->respondUnauthorizedError();
    }
}
