<?php

namespace App\Api\Controllers;

use App\Profile;
use App\TestCase;
use App\TestingDetail;
use App\TestSuite;
use App\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel;

class TestCasesController extends BaseApiController
{

    /**
     * @api {get} /v1/testcase Request Test Execution profile
     *
     * @apiName getExecutionProfile
     * @apiGroup TestCases
     *
     * @apiDescription Method used to get Execution Profile data
     *
     * @apiError 404 Not Found
     * @apiErrorExample {json} TestCase not found error:
     * {"error":{"message":"Test Case not found"},"code":404}
     *
     * @apiError 422 Unprocessable entity
     * @apiErrorExample {json} TetsCase not configured:
     * {"error":{"message":"Please set testing details"},"code":422}
     *
     * @apiErrorExample {json} No profile error response:
     * {"error":{"message":"Test Case doesn't have any profiles"},"code":404}
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
        $testingDetails = TestingDetail::where(['is_running' => 1, 'user_id' => Auth::user()->ID])->first();
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
                'test_case_id' => $testCase->getStringId(),
                'test_suite_id' => TestSuite::find($testingDetails->test_suite_id)->getStringId(),
                'product_id' => Post::find($testingDetails->product_id)->getProductStringId(),
                'profile' => $profile->getProfileFromS3(),
            ]);
        }

        return $this->respondNotFound("Test Case don't have test execution profile");
    }

    /**
     * @api {get} /v1/testcases/:test_case_id/profiles Request Test Case profiles
     *
     * @apiName getProfiles
     * @apiGroup TestCases
     *
     * @apiDescription Method used to get all test case profiles
     *
     * @apiError 404 Test Case not found
     * @apiErrorExample {json} TestCase not found error:
     * {"errors":{"message":"Test Case not found"},"code":404}
     *
     * @apiError 404 Test Case doesn't have any profiles
     * @apiErrorExample {json} No profiles error response:
     * {"errors":{"message":"Test Case doesn't have any profiles"},"code":404}
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
}
