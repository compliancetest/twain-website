<?php

namespace App\Api\v2\Controllers;

use App\Jobs\ProcessTransactionLog;
use Aws\Laravel\AwsFacade as AWS;
use Symfony\Component\HttpKernel;
use Validator;

class TransactionsController extends BaseApiController
{
    /**
     * @api {post} /v2/transactions Create transaction
     * @apiVersion 2.0.0
     * 
     * @apiParam {file} file  Mandatory - zip file.
     * @apiParam {string} test_case_id  Mandatory - test case id string.
     * @apiParam {string} product_id  Mandatory - product id string.
     * @apiParam {string} test_suite_id  Mandatory - test suite id string.
     * @apiParam {string} execution_id  Mandatory - execution id string.
     * @apiParam {string} test_outcome  Optional - allowed values: Fail, Pass, Skip, Pending.
     * @apiParam {string} reason  Optional - a reason of fail or skip.
     *
     * @apiName createTansaction
     * @apiGroup Transactions
     *
     * @apiSuccessExample {json} Success Response:
     *   {
     *     "data": {
     *       "status": "File Uploaded",
     *       "url": "https://s3-us-west-2.amazonaws.com/captures.integration.twain.gosource.com.au/20/ca-02-v1-0/aff8f0fa-e201-432e-a086-397875dd5219/2_fujitsu_paperstream-ip-fi-7160_v1-42_2016-10-11_000147.zip"
     *     },
     *     "status": "success",
     *     "code": 201
     *   }
     *
     * @apiError 403 Forbidden
     * @apiErrorExample {json} Not organization member:
     *   {
     *     "messages": ["Only organization member can perform testing"],
     *     "status": "error",
     *     "code": 403
     *   }
     *
     * @apiError 403 Forbidden
     * @apiErrorExample {json} Organization is not approved yet:
     *   {
     *     "messages": ["Your organization can't perform testing."],
     *     "status": "error",
     *     "code": 403
     *   }
     *
     * @apiError 403 Forbidden
     * @apiErrorExample {json} Organization doesn't have access to test suite:
     *    {
     *     "messages": ["Your organisation doesn't have access to this test suite."],
     *     "status": "error",
     *     "code": 403
     *   }
     *
     *
     * @apiError 422 Required field missed
     * @apiErrorExample {json} Validation error:
     *
     *   {
     *     "messages": [
     *         "The file field is required."
     *         "The test case id field is required."
     *         "The test suite id field is required."
     *         "The execution id field is required."
     *         "The product id field is required."
     *         "The selected test outcome is invalid."
     *     ],
     *     "status": "error",
     *     "code": 422
     *   }
     *
     * @apiHeader (Headers) {String} Authorization Authorization value Basic (base64_encode(login:password)).
     *
     */
    public function create(\Illuminate\Http\Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:zip',
            'test_case_id' => 'required|exists:test_cases,slug',
            'test_suite_id' => 'required|exists:test_suites,slug',
            'product_id' => 'required|exists:products,slug',
            'execution_id' => 'required',
            'reason' => 'string',
            'test_outcome' => 'in:Pass,Fail,Skip,Pending',
        ]);

        if ($validator->fails()) {
            return $this->respondUnprocessableEntity($validator->messages());
        }

        $hasAccessToTestSuite = $this->doesOrganisationHasAccessToTestSuite($request->get('test_suite_id'));
        if(!$hasAccessToTestSuite){
            return $this->respondForbiddenError("Your organisation doesn't have access to this test suite.");
        }

        $fileName = \Auth::user()->ID . '/' . $request->get('test_case_id') . '/' . $request->get('execution_id') . '/' . $request->file('file')->getClientOriginalName();

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
            'test_outcome' => strtoupper($request->get('test_outcome')),
            'reason' => $request->get('reason'),
            'transaction_key' => \Auth::user()->ID . '/' . $request->get('test_case_id') . '/' . $request->get('execution_id'),
            'user_id' => \Auth::user()->ID,
        ];
        //adding entry to sqs. it will be processed in background
        $this->dispatch(new ProcessTransactionLog($fileName, $data));

         return $this->setStatusCode(201)->respondWithData([
            'status' => 'File Uploaded',
            'url' => 'https://s3-'.config('env.bucket.region').'.amazonaws.com/'.config('env.bucket.transactions').'/' . $fileName
        ]);
    }
}
