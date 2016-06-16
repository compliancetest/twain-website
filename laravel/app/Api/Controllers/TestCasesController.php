<?php

namespace App\Api\Controllers;

use App\Profile;
use App\TestCase;
use App\TestingDetail;
use App\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Validator;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel;

class TestCasesController extends BaseApiController
{

    /**
     * @api {get} /v1/testcase Request Test Execution profile
     *
     * @apiName getExecutionProfile
     * @apiGroup Test Cases
     *
     * @apiDescription Method used to get Execution Profile data
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
     * @apiError 404 Not Found
     * @apiErrorExample {json} TestCase not found error:
     *   {
     *     "errors": {
     *       "message": [
     *          "Test Case not found"
     *       ]
     *     },
     *     "code": 404
     *   }
     *
     * @apiError 422 Unprocessable entity
     * @apiErrorExample {json} TetsCase not configured:
     *   {
     *     "error": {
     *       "message": [
     *          "Please set testing details"
     *       ]
     *     },
     *     "code": 422
     *   }
     *
     * @apiErrorExample {json} No profile error response:
     *   {
     *     "errors": {
     *       "message": [
     *          "Test Case doesn't have any profiles"
     *       ]
     *     },
     *     "code": 404
     *   }
     *
     * @apiSuccessExample {json} Success-Response:
     * {"data":{"test_case_id":"SC-01 v1.0","test_suite_id":"TWAINDS v1.0","product_id":"123 v123","profile":{"Profile":{"Type":"TCEF","Purpose":"TCEF for Application test case","Title":"CN-01a_v1.0 TEFC","Description":"Test Case Execution Flow for CN-01a test case","Version":{"Major":1,"Minor":0}},"Meta":{"SystemUnderTest":"Application","Capabilities":[{"Cap":"ICAP_AUTOSIZE"}],"InitialState":2},"TestSteps":[[{"Optional":false,"Triplet":{"From":"APP","To":"DSM","DataGroup":"DG_CONTROL","DataArgumentType":"DAT_PARENT","Messages":"MSG_OPENDSM"},"PassConditions":[{"ItemType":"ReturnCode","Operator":"EQ","Value":"TWRC_SUCCESS","Step":1}]}],[{"Optional":false,"Triplet":{"From":"APP","To":"DSM","DataGroup":"DG_CONTROL","DataArgumentType":"DAT_IDENTITY","Messages":"MSG_USERSELECT"},"PassConditions":[{"ItemType":"ReturnCode","Operator":"EQ","Value":"TWRC_SUCCESS","Step":1}]}],[{"Optional":false,"Triplet":{"From":"APP","To":"DSM","DataGroup":"DG_CONTROL","DataArgumentType":"DAT_IDENTITY","Messages":"MSG_OPENDS"},"PassConditions":[{"ItemType":"ReturnCode","Operator":"EQ","Value":"TWRC_SUCCESS","Step":1}]}],[{"Optional":false,"Triplet":{"From":"APP","To":"DS","DataGroup":"DG_CONTROL","DataArgumentType":"DAT_CAPABILITY","Messages":"MSG_SET","pCapability":{"Cap":"ICAP_AUTOSIZE","ConType":"TWON_ONEVALUE","hContainer":{"ItemType":"TWTY_UINT32","Item":"TWAS_AUTO"}}},"PassConditions":[{"ItemType":"ReturnCode","Operator":"EQ","Value":"TWRC_SUCCESS","Step":3}]}],[{"Optional":false,"Triplet":{"From":"APP","To":"DS","DataGroup":"DG_CONTROL","DataArgumentType":"DAT_USERINTERFACE","Messages":"MSG_ENABLEDS"},"PassConditions":[{"ItemType":"ReturnCode","Operator":"EQ","Value":"TWRC_SUCCESS","Step":4}]}],[{"Optional":false,"Triplet":{"From":"DS","To":"APP","DataGroup":"DG_CONTROL","DataArgumentType":"DAT_NULL","Messages":"MSG_XFERREADY"},"PassConditions":[{"ItemType":"ReturnCode","Operator":"EQ","Value":"TWRC_SUCCESS","Step":4}]}],[{"Optional":false,"Triplet":{"From":"APP","To":"DS","DataGroup":"DG_IMAGE","DataArgumentType":"DAT_IMAGEMEMXFER","Messages":"MSG_GET"},"PassConditions":[{"ItemType":"ReturnCode","Operator":"EQ","Value":"TWRC_XFERDONE","Step":4}]},{"Optional":false,"Triplet":{"From":"APP","To":"DS","DataGroup":"DG_IMAGE","DataArgumentType":"DAT_IMAGEMEMFILEXFER","Messages":"MSG_GET"},"PassConditions":[{"ItemType":"ReturnCode","Operator":"EQ","Value":"TWRC_XFERDONE","Step":4}]},{"Optional":false,"Triplet":{"From":"APP","To":"DS","DataGroup":"DG_IMAGE","DataArgumentType":"DAT_IMAGENATIVEXFER","Messages":"MSG_GET"},"PassConditions":[{"ItemType":"ReturnCode","Operator":"EQ","Value":"TWRC_XFERDONE","Step":4}]},{"Optional":false,"Triplet":{"From":"APP","To":"DS","DataGroup":"DG_IMAGE","DataArgumentType":"DAT_IMAGEFILEXFER","Messages":"MSG_GET"},"PassConditions":[{"ItemType":"ReturnCode","Operator":"EQ","Value":"TWRC_XFERDONE","Step":4}]}],[{"Optional":false,"Triplet":{"From":"APP","To":"DS","DataGroup":"DG_CONTROL","DataArgumentType":"DAT_PENDINGXFERS","Messages":"MSG_ENDXFER"},"PassConditions":[{"ItemType":"ReturnCode","Operator":"EQ","Value":"TWRC_SUCCESS","Step":4}]}],[{"Optional":false,"Triplet":{"From":"APP","To":"DS","DataGroup":"DG_CONTROL","DataArgumentType":"DAT_USERINTERFACE","Messages":"MSG_DISABLEDS"},"PassConditions":[{"ItemType":"ReturnCode","Operator":"EQ","Value":"TWRC_SUCCESS"}]}],[{"Optional":false,"Triplet":{"From":"APP","To":"DSM","DataGroup":"DG_CONTROL","DataArgumentType":"DAT_IDENTITY","Messages":"MSG_CLOSEDS"},"PassConditions":[{"ItemType":"ReturnCode","Operator":"EQ","Value":"TWRC_SUCCESS"}]}],[{"Optional":false,"Triplet":{"From":"APP","To":"DSM","DataGroup":"DG_CONTROL","DataArgumentType":"DAT_PARENT","Messages":"MSG_CLOSEDSM"},"PassConditions":[{"ItemType":"ReturnCode","Operator":"EQ","Value":"TWRC_SUCCESS"}]}]]}},"code":200}
     *
     * @apiHeader (Headers) {String} Authorization Authorization value Basic (base64_encode(login:password)).
     *
     * @apiVersion 1.0.0
     */
    public function show()
    {
        $testingDetails = TestingDetail::where(['is_running' => 1, 'user_id' => \Auth::user()->ID])->first();
        if (!count($testingDetails)) {
            return $this->respondUnprocessableEntity("Please set testing details");
        }

        $testCase = TestCase::find($testingDetails->test_case_id);
        if (!$testCase) {
            return $this->respondNotFound("Test Case not found");
        }

        $profileId = $testCase->getTestExecutionProfileId();
        $profile = Profile::find($profileId);
        if ($profile) {
            return $this->respondWithData([
                'test_case_id' => Post::find($testCase->case_id)->post_name,
                'test_suite_id' => Post::find($testingDetails->test_suite_id)->post_name,
                'product_id' => Post::find($testingDetails->product_id)->post_name,
                'profile' => $profile->getProfileFromS3(),
            ]);
        }

        return $this->respondNotFound("Test Case don't have test execution profile");
    }

    /**
     * @api {get} /v1/testcases/:test_case_id/profiles Request Test Case profiles
     *
     * @apiName getProfiles
     * @apiGroup Test Cases
     *
     * @apiDescription Method used to get all test case profiles
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
     * @apiError 404 Test Case not found
     * @apiErrorExample {json} TestCase not found error:
     *   {
     *     "errors": {
     *       "message": [
     *          "Test Case not found"
     *       ]
     *     },
     *     "code": 404
     *   }
     *
     * @apiError 404 Test Case doesn't have any profiles
     * @apiErrorExample {json} No profiles error response:
     *   {
     *     "errors": {
     *       "message": [
     *          "Test Case doesn't have any profiles"
     *       ]
     *     },
     *     "code": 404
     *   }
     *
     * @apiSuccessExample {json} Success-Response:
     * {"data":[{"Capabilities":[{"Cap":"CAP_SUPPORTEDCAPS","AllowedValues":{"ConType":"TWON_ARRAY","hContainer":{"ItemType":"TWTY_UINT16","ArrayItemList":["CAP_SUPPORTEDCAPS","CAP_UICONTROLLABLE","CAP_XFERCOUNT"]}},"CurrentValue":{"ConType":"TWON_ARRAY","hContainer":{"ItemType":"TWTY_UINT16","ArrayItemList":["CAP_SUPPORTEDCAPS","CAP_UICONTROLLABLE","CAP_XFERCOUNT"]}},"DefaultValue":{"ConType":"TWON_ARRAY","hContainer":{"ItemType":"TWTY_UINT16","ArrayItemList":["CAP_SUPPORTEDCAPS","CAP_UICONTROLLABLE","CAP_XFERCOUNT"]}},"Operations":["GET","GETCURRENT","GETDEFAULT"]},{"Cap":"CAP_UICONTROLLABLE","AllowedValues":{"ConType":"TWON_ONEVALUE","hContainer":{"ItemType":"TWTY_BOOL","Item":"TRUE"}},"CurrentValue":{"ConType":"TWON_ONEVALUE","hContainer":{"ItemType":"TWTY_BOOL","Item":"TRUE"}},"DefaultValue":{"ConType":"TWON_ONEVALUE","hContainer":{"ItemType":"TWTY_BOOL","Item":"TRUE"}},"Operations":["GET","GETCURRENT","GETDEFAULT"]},{"Cap":"CAP_XFERCOUNT","AllowedValues":{"ConType":"TWON_RANGE","hContainer":{"ItemType":"TWTY_INT16","MinValue":-1,"MaxValue":32767,"StepSize":1,"CurrentValue":-1,"DefaultValue":-1}},"CurrentValue":{"ConType":"TWON_ONEVALUE","hContainer":{"ItemType":"TWTY_INT16","Item":"-1"}},"DefaultValue":{"ConType":"TWON_ONEVALUE","hContainer":{"ItemType":"TWTY_INT16","Item":"-1"}},"Operations":["GET","SET","GETCURRENT","GETDEFAULT","RESET","SETCONSTRAINT"]},{"Cap":"ICAP_COMPRESSION","AllowedValues":{"ConType":"TWON_ENUMERATION","hContainer":{"ItemType":"TWTY_UINT16","EnumerationItemList":["TWCP_NONE","TWCP_PACKBITS","TWCP_GROUP31D","TWCP_GROUP31DEOL","TWCP_GROUP32D","TWCP_GROUP4","TWCP_JPEG","TWCP_LZW","TWCP_JBIG","TWCP_PNG","TWCP_RLE4","TWCP_RLE8","TWCP_BITFIELDS","TWCP_ZIP","TWCP_JPEG2000"],"CurrentItem":"TWCP_NONE","DefaultItem":"TWCP_NONE"}},"CurrentValue":{"ConType":"TWON_ONEVALUE","hContainer":{"ItemType":"TWTY_UINT16","Item":"TWCP_NONE"}},"DefaultValue":{"ConType":"TWON_ONEVALUE","hContainer":{"ItemType":"TWTY_UINT16","Item":"TWCP_NONE"}},"Operations":["GET","SET","GETCURRENT","GETDEFAULT","RESET","SETCONSTRAINT"]},{"Cap":"ICAP_BITORDER","AllowedValues":{"ConType":"TWON_ENUMERATION","hContainer":{"ItemType":"TWTY_UINT16","EnumerationItemList":["TWBO_LSBFIRST","TWBO_MSBFIRST"],"CurrentItem":"TWBO_MSBFIRST","DefaultItem":"TWBO_MSBFIRST"}},"CurrentValue":{"ConType":"TWON_ONEVALUE","hContainer":{"ItemType":"TWTY_UINT16","Item":"TWBO_MSBFIRST"}},"DefaultValue":{"ConType":"TWON_ONEVALUE","hContainer":{"ItemType":"TWTY_UINT16","Item":"TWBO_MSBFIRST"}},"Operations":["GET","SET","GETCURRENT","GETDEFAULT","RESET","SETCONSTRAINT"]},{"Cap":"ICAP_PLANARCHUNKY","AllowedValues":{"ConType":"TWON_ENUMERATION","hContainer":{"ItemType":"TWTY_UINT16","EnumerationItemList":["TWPC_CHUNKY","TWPC_PLANAR"]}},"CurrentValue":{"ConType":"TWON_ONEVALUE","hContainer":{"ItemType":"TWTY_UINT16","Item":"TWPC_PLANAR"}},"DefaultValue":{"ConType":"TWON_ONEVALUE","hContainer":{"ItemType":"TWTY_UINT16","Item":"TWPC_PLANAR"}},"Operations":["GET","SET","GETCURRENT","GETDEFAULT","RESET","SETCONSTRAINT"]},{"Cap":"ICAP_PHYSICALHEIGHT","AllowedValues":{"ConType":"TWON_RANGE","hContainer":{"ItemType":"TWTY_FIX32","MinValue":0,"MaxValue":65535,"StepSize":1,"CurrentValue":0}},"Operations":["GET","GETCURRENT","GETDEFAULT"]},{"Cap":"ICAP_PHYSICALWIDTH","AllowedValues":{"ConType":"TWON_RANGE","hContainer":{"ItemType":"TWTY_FIX32","MinValue":0,"MaxValue":65535,"StepSize":1,"CurrentValue":0}},"Operations":["GET","GETCURRENT","GETDEFAULT"]},{"Cap":"ICAP_PIXELFLAVOR","AllowedValues":{"ConType":"TWON_ENUMERATION","hContainer":{"ItemType":"TWTY_UINT16","EnumerationItemList":["TWPF_CHOCOLATE","TWPF_VANILLA"]}},"CurrentValue":{"ConType":"TWON_ONEVALUE","hContainer":{"ItemType":"TWTY_UINT16","Item":"TWPF_CHOCOLATE"}},"DefaultValue":{"ConType":"TWON_ONEVALUE","hContainer":{"ItemType":"TWTY_UINT16","Item":"TWPF_CHOCOLATE"}},"Operations":["GET","SET","GETCURRENT","GETDEFAULT","RESET","SETCONSTRAINT"]},{"Cap":"ICAP_PIXELTYPE","AllowedValues":{"ConType":"TWON_ENUMERATION","hContainer":{"ItemType":"TWTY_UINT16","EnumerationItemList":["TWPT_BW","TWPT_GRAY","TWPT_RGB","TWPT_PALETTE","TWPT_CMY","TWPT_CMYK","TWPT_YUV","TWPT_YUVK","TWPT_CIEXYZ","TWPT_LAB","TWPT_SRGB","TWPT_SRGB64","TWPT_BGR","TWPT_CIELAB","TWPT_CIELUV","TWPT_YCBCR","TWPT_INFRARED"]}},"CurrentValue":{"ConType":"TWON_ONEVALUE","hContainer":{"ItemType":"TWTY_UINT16","Item":"TWPT_BW"}},"DefaultValue":{"ConType":"TWON_ONEVALUE","hContainer":{"ItemType":"TWTY_UINT16","Item":"TWPT_BW"}},"Operations":["GET","SET","GETCURRENT","GETDEFAULT","RESET","SETCONSTRAINT"]},{"Cap":"ICAP_UNITS","AllowedValues":{"ConType":"TWON_ENUMERATION","hContainer":{"ItemType":"TWTY_UINT16","EnumerationItemList":["TWUN_INCHES","TWUN_CENTIMETERS","TWUN_PICAS","TWUN_POINTS","TWUN_TWIPS","TWUN_PIXELS","TWUN_MILLIMETERS"]}},"CurrentValue":{"ConType":"TWON_ONEVALUE","hContainer":{"ItemType":"TWTY_UINT16","Item":"TWUN_INCHES"}},"DefaultValue":{"ConType":"TWON_ONEVALUE","hContainer":{"ItemType":"TWTY_UINT16","Item":"TWUN_INCHES"}},"Operations":["GET","SET","GETCURRENT","GETDEFAULT","RESET","SETCONSTRAINT"]},{"Cap":"ICAP_XFERMECH","AllowedValues":{"ConType":"TWON_ENUMERATION","hContainer":{"ItemType":"TWTY_UINT16","EnumerationItemList":["TWSX_NATIVE","TWSX_FILE","TWSX_MEMORY","TWSX_MEMFILE"]}},"CurrentValue":{"ConType":"TWON_ONEVALUE","hContainer":{"ItemType":"TWTY_UINT16","Item":"TWSX_NATIVE"}},"DefaultValue":{"ConType":"TWON_ONEVALUE","hContainer":{"ItemType":"TWTY_UINT16","Item":"TWSX_NATIVE"}},"Operations":["GET","SET","GETCURRENT","GETDEFAULT","RESET","SETCONSTRAINT"]},{"Cap":"ICAP_XRESOLUTION","AllowedValues":{"ConType":"TWON_RANGE","hContainer":{"ItemType":"TWTY_FIX32","MinValue":1,"MaxValue":65536,"StepSize":1,"CurrentValue":0}},"Operations":["GET","SET","GETCURRENT","GETDEFAULT","RESET","SETCONSTRAINT"]},{"Cap":"ICAP_YRESOLUTION","AllowedValues":{"ConType":"TWON_RANGE","hContainer":{"ItemType":"TWTY_FIX32","MinValue":1,"MaxValue":65536,"StepSize":1,"CurrentValue":0}},"Operations":["GET","SET","GETCURRENT","GETDEFAULT","RESET","SETCONSTRAINT"]},{"Cap":"CAP_SUPPORTEDDATS","AllowedValues":{"ConType":"TWON_ARRAY","hContainer":{"ItemType":"TWTY_UINT32","ArrayItemList":["DAT_NULL","DAT_CUSTOMBASE","DAT_CAPABILITY","DAT_EVENT","DAT_IDENTITY","DAT_PARENT","DAT_PENDINGXFERS","DAT_SETUPMEMXFER","DAT_SETUPFILEXFER","DAT_STATUS","DAT_USERINTERFACE","DAT_XFERGROUP","DAT_CUSTOMDSDATA","DAT_DEVICEEVENT","DAT_FILESYSTEM","DAT_PASSTHRU","DAT_CALLBACK","DAT_STATUSUTF8","DAT_CALLBACK2","DAT_ENTRYPOINT","DAT_IMAGEINFO","DAT_IMAGELAYOUT","DAT_IMAGEMEMXFER","DAT_IMAGENATIVEXFER","DAT_IMAGEFILEXFER","DAT_CIECOLOR","DAT_GRAYRESPONSE","DAT_RGBRESPONSE","DAT_JPEGCOMPRESSION","DAT_PALETTE8","DAT_EXTIMAGEINFO","DAT_FILTER","DAT_AUDIOFILEXFER","DAT_AUDIOINFO","DAT_AUDIONATIVEXFER","DAT_ICCPROFILE","DAT_IMAGEMEMFILEXFER"]}}}],"Identity":{"Version":"CN-01a_v1.0","Protocol":{"Major":2,"Minor":3},"Manufacturer":"Drummond Group","Product":{"Name":"CN-01a DS","Family":"Virtual Data Source"},"SupportedGroups":"DF_DSM2"},"Profile":{"Type":"DataSource","Purpose":"Virtual Data Source","Title":"CN-01a DS","Description":"Virtual Data Source configuration for CN-01a test case.","Version":{"Major":1,"Minor":0}}},{"Profile":{"Type":"TCEF","Purpose":"TCEF for Application test case","Title":"CAP-01a_v1.0 TEFC","Description":"Test Case Execution Flow for CAP-01a test case","Version":{"Major":1,"Minor":0}},"Meta":{"SystemUnderTest":"Application","Capabilities":[{"Cap":"CAP_SUPPORTEDCAPS"}],"InitialState":4},"TestSteps":[[{"Optional":false,"Triplet":{"From":"APP","To":"DS","DataGroup":"DG_CONTROL","DataArgumentType":"DAT_USERINTERFACE","Messages":"MSG_ENABLEDS","pUserinterface":{"ShowUI":true}},"PassConditions":[{"ItemType":"ReturnCode","Operator":"EQ","Value":"TWRC_SUCCESS","Step":1}]}]]}],"code":200}
     *
     * @apiHeader (Headers) {String} Authorization Authorization value Basic (base64_encode(login:password)).
     *
     * @apiVersion 1.0.0
     */

    public function profiles($testCaseId)
    {
        $testCase = TestCase::find($testCaseId);
        if (!$testCase) {
            return $this->respondNotFound("Test Case not found");
        }
        $profiles = Profile::where('id', $testCase->getTestExecutionProfileId())->orWhere('id', $testCase->getTestDataProfileId())->get();
        if (!$profiles->count()) {
            return $this->respondNotFound("Test Case doesn't have any profiles");
        }
        $responseData = [];
        foreach ($profiles as $profile) {
            $responseData[] = $profile->getProfileFromS3();
        }
        return $this->respondWithData($responseData);
    }

    /**
     * @api {post} /v1/testcase/start Set testing details
     *
     * @apiParam {string} test_suite_id  Mandatory - test suite string ID.
     * @apiParam {string} test_case_id  Mandatory - test case string ID.
     * @apiParam {string} product_id  Mandatory - product string ID.
     *
     * @apiName setTestingDetails
     * @apiGroup Test Cases
     *
     * @apiDescription Method used to configure testing details
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
     * @apiError 422 Required field missed
     * @apiErrorExample {json} Validation error:
     *   {
     *     "errors": {
     *       "test_suite_id": [
     *         "The selected test suite id is invalid."
     *       ],
     *       "test_case_id": [
     *         "The selected test case id is invalid."
     *       ],
     *       "product_id": [
     *         "The selected product id is invalid."
     *       ]
     *     },
     *     "code": 422
     *   }
     *
     *
     * @apiError 400 User already has running test case
     * @apiErrorExample {json} Please stop running case before start:
     *   {
     *     "errors": {
     *       "message": [
     *          "Please stop running case before start"
     *       ]
     *     },
     *     "code": 400
     *   }
     *
     * @apiSuccessExample {json} Success Response:
     * {"data":{"ExecutionId":"026d9d68-eb09-41be-af73-ab3e0db971c9","TestSuite":{"id":"twain-compliance-technical-app-v1-0","title":"TWAIN Compliance Technical - App v1.0"},"TestCase":{"id":"vv-01-v1-0","title":"VV-01 v1.0"},"Product":{"id":"test","title":"Test"},"ExecutionProfile":{"Profile":{"Type":"TCEF","Purpose":"TCEF for DS test case","Title":"VV-01_v1.0 TEFC","Description":"Test Case Execution Flow for VV-01 test case","Version":{"Major":1,"Minor":0}},"Meta":{"SystemUnderTest":"DataSource","Capabilities":[{"Cap":"ACAP_XFERMECH"}],"InitialState":4},"TestSteps":[[{"Optional":false,"Triplet":{"From":"APP","To":"DS","DataGroup":"DG_CONTROL","DataArgumentType":"DAT_CAPABILITY","Messages":"MSG_RESETALL"},"PassConditions":[{"ItemType":"ReturnCode","Operator":"EQ","Value":"TWRC_SUCCESS","Step":2}]}],[{"Optional":false,"Triplet":{"From":"APP","To":"DS","DataGroup":"DG_CONTROL","DataArgumentType":"DAT_CAPABILITY","Messages":"MSG_GETCURRENT","pCapability":{"Cap":"ACAP_XFERMECH"}},"PassConditions":[{"ItemType":"ReturnCode","Operator":"EQ","Value":"TWRC_SUCCESS","Step":3},{"ItemType":"Property","Operator":"EQ","Value":"ACAP_XFERMECH","Step":3,"Path":"pCapability.Cap"},{"ItemType":"Property","Operator":"EQ","Value":"TWON_ONEVALUE","Step":4,"Path":"pCapability.ConType"},{"ItemType":"Property","Operator":"EQ","Value":"TWTY_UINT16","Path":"pCapability.hContainer.ItemType","Step":5},{"ItemType":"Property","Operator":"EQ","Value":"TWSX_NATIVE","Path":"pCapability.hContainer.Item","Step":6}],"SkipConditions":[{"ItemType":"ReturnCode","Operator":"NOT_EQ","Value":"TWRC_SUCCESS"}]}],[{"Optional":false,"Triplet":{"From":"APP","To":"DS","DataGroup":"DG_CONTROL","DataArgumentType":"DAT_CAPABILITY","Messages":"MSG_RESET","pCapability":{"Cap":"ACAP_XFERMECH"}},"PassConditions":[{"ItemType":"ReturnCode","Operator":"EQ","Value":"TWRC_SUCCESS","Step":7},{"ItemType":"Property","Operator":"EQ","Value":"ACAP_XFERMECH","Step":7,"Path":"pCapability.Cap"},{"ItemType":"Property","Operator":"EQ","Value":"TWON_ONEVALUE","Step":8,"Path":"pCapability.ConType"},{"ItemType":"Property","Operator":"EQ","Value":"TWTY_UINT16","Path":"pCapability.hContainer.ItemType","Step":9},{"ItemType":"Property","Operator":"EQ","Value":"TWSX_NATIVE","Path":"pCapability.hContainer.Item","Step":10}]}]]}},"code":200}
     *
     * @apiHeader (Headers) {String} Authorization Authorization value Basic (base64_encode(login:password)).
     *
     * @apiVersion 1.0.0
     */

    public function start(\Illuminate\Http\Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_suite_id' => 'required|exists:wp_posts,post_name',
            'test_case_id' => 'required|exists:wp_posts,post_name',
            'product_id' => 'required|exists:wp_posts,post_name',
        ]);
        if ($validator->fails()) {
            return $this->respondUnprocessableEntity($validator->messages());
        }
        $model = TestingDetail::where(['user_id' => Auth::user()->ID, 'is_running' => true])->first();
        if ($model) {
            return $this->respondBadRequest('Please stop running case before start');
        }


        $product = Post::where(['post_type' => 'product-service', 'post_name' => $request->get('product_id')])->first();
        $testSuite = Post::where(['post_type' => 'test-suite', 'post_name' => $request->get('test_suite_id')])->first();
        $testCase = Post::where(['post_type' => 'test-case', 'post_name' => $request->get('test_case_id')])->first();

        $model = TestingDetail::firstOrNew(['user_id' => Auth::user()->ID]);
        $model->product_id = $product->ID;
        $model->test_case_id = $testCase->ID;
        $model->test_suite_id = $testSuite->ID;
        $model->start_time = Carbon::now();
        $model->is_running = true;
        $model->save();

        $testConfigurationProfile = TestCase::find($testCase->ID)->getTestDataProfileId();

        $response = [
            'ExecutionId' => $model->id,
            'TestSuite' => [
                'id' => $testSuite->post_name,
                'title' => $testSuite->post_title,
            ],
            'TestCase' => [
                'id' => $testCase->post_name,
                'title' => $testCase->post_title,
            ],
            'Product' => [
                'id' => $product->post_name,
                'title' => $product->post_title,
            ],
            'ExecutionProfile' => Profile::find(TestCase::find($testCase->ID)->getTestExecutionProfileId())->getProfileFromS3(),
            'ConfigurationProfile' => $testConfigurationProfile ? Profile::find($testConfigurationProfile)->getProfileFromS3() : false,
            'images' => $this->_getTestCaseImages($testCase)
        ];
        return $this->respondWithData($response);
    }

    /**
     * Get test case images data
     * @param $testCase
     * @return array
     */
    private function _getTestCaseImages($testCase)
    {
        $images = [];
        $imagesData = $testCase->postmeta()->where('meta_key', 'imagesData')->first();
        if ($imagesData) {
            $imagesData = json_decode($imagesData->meta_value);
            foreach ($imagesData as $imageData) {
                $images[] = [
                    'link' => Storage::disk('s3')->url(config('env.env') . '/case_images/' . $testCase->ID . '/' . $imageData->name, $imageData->name),
                    'description' => $imageData->description,
                ];
            }
        }
        return $images;
    }

    /**
     * @api {delete} /v1/testcase/stop Delete testing details
     *
     * @apiName deleteTestingDetails
     * @apiGroup Test Cases
     *
     * @apiDescription Method used to configure reset testing details
     *
     * @apiError 400 User didn't use start method yet
     * @apiErrorExample {json} Please use start method first:
     *   {
     *     "errors": {
     *       "message": [
     *          "Please use start method first"
     *       ]
     *     },
     *     "code": 400
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
     * @apiSuccessExample {json} Success-Response:
     *   {
     *     "message": "Ok",
     *     "code": 200
     *   }
     *
     * @apiHeader (Headers) {String} Authorization Authorization value Basic (base64_encode(login:password)).
     *
     * @apiVersion 1.0.0
     */

    public function stop()
    {
        if (!TestingDetail::where(['user_id' => Auth::user()->ID])->first()) {
            return $this->respondBadRequest('Please use start method first');
        }
        TestingDetail::where(['user_id' => Auth::user()->ID])->delete();
        return $this->respondSuccess('Ok');
    }

    /**
     * @api {get} /v1/testcase/status Get testing details
     *
     * @apiName getTestingDetails
     * @apiGroup Test Cases
     *
     * @apiDescription Method used to get testing details
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
     * @apiError 404 User didn't use start method yet
     * @apiErrorExample {json} You are not running any test case now:
     *
     *  {
     *     "errors": {
     *       "message": [
     *         "You are not running any test case now"
     *       ]
     *     },
     *     "code": 404
     *   }
     *
     * @apiSuccessExample {json} Success-Response:
     *   {
     *     "message": "Ok",
     *     "code": 200
     *   }
     *
     * @apiHeader (Headers) {String} Authorization Authorization value Basic (base64_encode(login:password)).
     *
     * @apiVersion 1.0.0
     */

    public function status()
    {
        $model = TestingDetail::where(['user_id' => Auth::user()->ID, 'is_running' => true])->first();
        if (!$model) {
            return $this->respondNotFound('You are not running any test case now');
        }

        $product = Post::find($model->product_id);
        $testSuite = Post::find($model->test_suite_id);
        $testCase = Post::find($model->test_case_id);

        $testConfigurationProfile = TestCase::find($testCase->ID)->getTestDataProfileId();

        $response = [
            'ExecutionId' => $model->id,
            'TestSuite' => [
                'id' => $testSuite->post_name,
                'title' => $testSuite->post_title,
            ],
            'TestCase' => [
                'id' => $testCase->post_name,
                'title' => $testCase->post_title,
            ],
            'Product' => [
                'id' => $product->post_name,
                'title' => $product->post_title,
            ],
            'ExecutionProfile' => Profile::find(TestCase::find($testCase->ID)->getTestExecutionProfileId())->getProfileFromS3(),
            'ConfigurationProfile' => $testConfigurationProfile ? Profile::find($testConfigurationProfile)->getProfileFromS3() : false,
            'images' => $this->_getTestCaseImages($testCase)
        ];
        return $this->respondWithData($response);
    }

}
