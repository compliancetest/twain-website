<?php

namespace App\Api\Controllers;

use App\Jobs\ProcessTransactionLog;
use Aws\Laravel\AwsFacade as AWS;
use Symfony\Component\HttpKernel;
use Validator;

class TransactionsController extends BaseApiController
{
    /**
     * @api {post} /v1/transactions Create transaction
     * @apiParam {file} file  Mandatory - zip file.
     * @apiParam {string} test_case_id  Mandatory - test case id string.
     * @apiParam {string} product_id  Mandatory - product id string.
     * @apiParam {string} test_suite_id  Mandatory - test suite id string.
     * @apiParam {string} execution_id  Mandatory - execution id string.
     *
     * @apiName createTansaction
     * @apiGroup Transactions
     *
     * @apiSuccessExample {json} Success Response:
     *   {
     *       "message": "File Uploaded",
     *       "code": 201
     *   }
     * @apiError 422 Required field missed
     * @apiErrorExample {json} Validation error:
     *
     *   {
     *     "errors": {
     *       "file": [
     *         "The file field is required."
     *       ],
     *       "test_case_id": [
     *         "The test case id field is required."
     *       ],
     *       "test_suite_id": [
     *         "The test suite id field is required."
     *       ],
     *       "execution_id": [
     *         "The execution id field is required."
     *       ],
     *       "product_id": [
     *         "The product id field is required."
     *       ]
     *     },
     *     "code": 422
     *   }
     *
     * @apiHeader (Headers) {String} Authorization Authorization value Basic (base64_encode(login:password)).
     *
     * @apiVersion 1.0.0
     */
    public function create(\Illuminate\Http\Request $request)
    {

        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:zip',
            'test_case_id' => 'required|exists:wp_posts,post_name',
            'test_suite_id' => 'required|exists:wp_posts,post_name',
            'product_id' => 'required|exists:wp_posts,post_name',
            'execution_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->respondUnprocessableEntity($validator->messages());
        }

        $fileName = config('env.env') . '/transactions/' .\Auth::user()->ID . '/' . $request->get('test_case_id') . '/' . $request->get('execution_id') . '/' . $request->file('file')->getClientOriginalName();

        $s3 = Aws::createClient('s3');
        $s3->putObject(array(
            'Bucket' => config('env.bucket.transactions'),
            'Key' => $fileName,
            'Body' => file_get_contents($request->file('file')->getPath().'/'.$request->file('file')->getFilename()),
        ));

        $data = [
            'test_case_id' => $request->get('test_case_id'),
            'test_suite_id' => $request->get('test_suite_id'),
            'execution_id' => $request->get('execution_id'),
            'product_id' => $request->get('product_id'),
        ];
        //adding entry to sqs. it will be processed in background
        $this->dispatch(new ProcessTransactionLog($fileName, $data));

        return $this->respondCreated('File Uploaded');
    }
}
