<?php

namespace App\Api\Controllers;

use App\Api\Transformers\ProfileTransformer;
use App\Profile;
use App\TestCase;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpKernel;
use Dingo\Api\Routing\Helpers;

class TestCasesController extends Controller
{

    use Helpers;

    /**
     * @api {get} testcase/:id
     * @apiDescription Method used to get Execution Profile data
     * @apiError 404 Test Case not found
     * @apiErrorExample {json} Error-Response:
                 {"message":"Test Case not found","status_code":404}
     * @apiError 400 product_id field is required
     * @apiErrorExample {json} Error-Response:
                 {"message":"product_id field is required","status_code":400}
     * @apiError 404 Test Case don't have test execution profile
     * @apiErrorExample {json} Error-Response:
                 {"message":"Test Case don't have test execution profile","status_code":404}
     * @apiParam Parameter {Number} product_id Mandatory product_id value.
     * @apiParamExample {json} Request-Example:
                 { "product_id": 125 }
     * @apiPermission user
     * @apiSampleRequest http://hostname/api/testcases/123
     * @apiSuccessExample {json} Success-Response:
                   {"data":{"id":21,"type_name":"TCEF v1.1","profile_name":"CAP-01a_v1.0 TEFC v1.0","profile_description":"Test Case Execution Flow for CAP-01a test case","purpose":"TCEF for Application test case","token":"ef77b24465e975582c65b93c69326c70134bf0e0","content":{"Profile":{"Type":"TCEF","Purpose":"TCEF for Application test case","Title":"CAP-01a_v1.0 TEFC","Description":"Test Case Execution Flow for CAP-01a test case","Version":{"Major":1,"Minor":0}},"Meta":{"SystemUnderTest":"Application","Capabilities":[{"Cap":"CAP_SUPPORTEDCAPS"}],"InitialState":4},"TestSteps":[[{"Optional":false,"Triplet":{"From":"APP","To":"DS","DataGroup":"DG_CONTROL","DataArgumentType":"DAT_USERINTERFACE","Messages":"MSG_ENABLEDS","pUserinterface":{"ShowUI":true}},"PassConditions":[{"ItemType":"ReturnCode","Operator":"EQ","Value":"TWRC_SUCCESS","Step":1}]}]]}}}
     * @apiVersion 1.0.0
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show($id)
    {
        $rules = [
            'product_id' => ['required']
        ];

        $payload = app('request')->only('product_id');
        $validator = app('validator')->make($payload, $rules);
        if ($validator->fails()) {
            throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException('product_id field is required');
        }
        $testCase = TestCase::find($id);
        if (!$testCase) {
            throw new HttpKernel\Exception\NotFoundHttpException('Test Case not found');
        }
        $profileId = $testCase->getTestExecutionProfileId();
        if ($profileId) {
            return $this->response->item(Profile::find($profileId), new ProfileTransformer())->setStatusCode(200);
        }
        throw new HttpKernel\Exception\NotFoundHttpException("Test Case don't have test execution profile");
    }
}
