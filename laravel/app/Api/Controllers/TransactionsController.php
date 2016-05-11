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
     *   {
     *     "errors": {
     *       "file": [
     *         "The file field is required."
     *       ],
     *       "test_case_id": [
     *         "The test case id field is required."
     *       ],
     *       "execution_id": [
     *         "The execution id field is required."
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

        //adding entry to sqs. it will be processed in background
        $this->dispatch(new ProcessTransactionLog($fileName, $request->get('execution_id')));

        return $this->respondCreated('File Uploaded');
    }
}
