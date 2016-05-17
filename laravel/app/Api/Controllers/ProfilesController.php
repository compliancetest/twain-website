<?php

namespace App\Api\Controllers;

use App\Profile;
use Symfony\Component\HttpKernel;

class ProfilesController extends BaseApiController
{

    /**
     * @api {get} /v1/profiles/:profile_id Request Profile Content
     *
     * @apiName getProfile
     * @apiGroup Profiles
     *
     * @apiSuccessExample {json} Success-Response:
     *   {
     *     "data": {
     *       "Profile": {
     *         "Type": "TCEF",
     *         "Purpose": "TCEF for Application test case",
     *         "Title": "CAP-01a_v1.0 TEFC",
     *         "Description": "Test Case Execution Flow for CAP-01a test case",
     *         "Version": {
     *           "Major": 1,
     *           "Minor": 0
     *         }
     *       },
     *       "Meta": {
     *         "SystemUnderTest": "Application",
     *         "Capabilities": [
     *           {
     *             "Cap": "CAP_SUPPORTEDCAPS"
     *           }
     *         ],
     *         "InitialState": 4
     *       },
     *       "TestSteps": [
     *         [
     *           {
     *             "Optional": false,
     *             "Triplet": {
     *               "From": "APP",
     *               "To": "DS",
     *               "DataGroup": "DG_CONTROL",
     *               "DataArgumentType": "DAT_USERINTERFACE",
     *               "Messages": "MSG_ENABLEDS",
     *               "pUserinterface": {
     *                 "ShowUI": true
     *               }
     *             },
     *             "PassConditions": [
     *               {
     *                 "ItemType": "ReturnCode",
     *                 "Operator": "EQ",
     *                 "Value": "TWRC_SUCCESS",
     *                 "Step": 1
     *               }
     *             ]
     *           }
     *         ]
     *       ]
     *     },
     *     "code": 200
     *   }
     *
     * @apiError 404 Profile not found
     * @apiErrorExample {json} Not Found error:
     *
     * {
     *     "errors": [
     *       "message": [
     *          "Profile not found"
     *       ]
     *     ],
     *     "code": 404
     *   }
     *
     * @apiHeader (Headers) {String} Authorization Authorization value Basic (base64_encode(login:password)).
     *
     * @apiVersion 1.0.0
     */
    public function show($profileId)
    {
        if ($profileId) {
            $profile = Profile::find($profileId);

            if ($profile) {
                return $this->respondWithData($profile->getProfileFromS3());
            }

            return $this->respondNotFound('Profile not found');

        }
    }
}
