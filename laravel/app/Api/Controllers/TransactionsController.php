<?php

namespace App\Api\Controllers;

use App\Profile;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpKernel;

class TransactionsController extends Controller
{

    /**
     * @api {post6} /v1/transactions Create / update transaction entry
     *
     * @apiName getProfile
     * @apiGroup Profiles
     *
     * @apiSampleRequest http://hostname/api/profiles/123
     * @apiSuccessExample {json} Success-Response:
     * {"data":{"Profile":{"Type":"TCEF","Purpose":"TCEF for Application test case","Title":"CAP-01a_v1.0 TEFC","Description":"Test Case Execution Flow for CAP-01a test case","Version":{"Major":1,"Minor":0}},"Meta":{"SystemUnderTest":"Application","Capabilities":[{"Cap":"CAP_SUPPORTEDCAPS"}],"InitialState":4},"TestSteps":[[{"Optional":false,"Triplet":{"From":"APP","To":"DS","DataGroup":"DG_CONTROL","DataArgumentType":"DAT_USERINTERFACE","Messages":"MSG_ENABLEDS","pUserinterface":{"ShowUI":true}},"PassConditions":[{"ItemType":"ReturnCode","Operator":"EQ","Value":"TWRC_SUCCESS","Step":1}]}]]},"code":200}
     * @apiError 404 Profile not found
     * @apiErrorExample {json} Error-Response:
     * {"message":"Profile not found","status_code":404}
     *
     * @apiHeader (Headers) {String} Authorization:Basic Authorization value (base64_encode(login:password)).
     *
     * @apiVersion 1.0.0
     */
    public function create()
    {
        return JsonResponse::create(
            [
                'data' => ['message' => 'POST'],
                'code' => 200
            ],
            200);
    }
}
