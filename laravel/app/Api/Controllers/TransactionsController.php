<?php

namespace App\Api\Controllers;

use Symfony\Component\HttpKernel;
use Validator;

class TransactionsController extends BaseApiController
{

    /**
     * @api {post} /v1/transactions Create transaction
     *
     * @apiName createTansaction
     * @apiGroup Transactions
     *
     * @apiSampleRequest /api/v1/transactions
     * @apiSuccessExample {json} Success-Response:
     * {
     *       "message": "File Uploaded",
     *       "code": 201
     *   }
     * @apiError 422 Required field missed
     * @apiErrorExample {json} Error-Response:
     * {"errors":{"file":["The file field is required."],"test_case_id":["The test case id field is required."],"execution_id":["The execution id field is required."]},"code":422}
     *
     * @apiHeader (Headers) {String} Authorization Authorization value Basic (base64_encode(login:password)).
     *
     * @apiVersion 1.0.0
     */
    public function create(\Illuminate\Http\Request $request)
    {

        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:zip',
            'test_case_id' => 'required|exists:wp_test_cases,case_id',
            'execution_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->respondUnprocessableEntity($validator->messages());
        }
        $s3 = new \S3Wrapper;
        $s3->putObject('transactions/' . $request->get('test_case_id') . '/' . $request->get('execution_id') . '/' . $request->file('file')->getClientOriginalName(),
            file_get_contents($request->file('file')),
            $request->file('file')->getClientMimeType(),
            'data.twain.gosource.com.au');
        return $this->respondCreated('File Uploaded');
    }
}
