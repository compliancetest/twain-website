<?php

namespace App\Http\Controllers;

use App\Post;
use App\TestingDetail;
use App\Transaction;
use App\TransactionsLog;
use Aws\Laravel\AwsFacade;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;

class TestingDetailsController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $suites = getUserSubscriptions();
        if(!$suites){
            return view('pages.testingdetails.nosubscription', compact('cases', 'products', 'suites', 'currentTestingDetails'));
        }
        $userId = get_current_user_id();
        $products = getUserProductsAndServices($userId);
        $current_suite_id = $suites[0]->suite_id;

        $suiteObj = new \TestSuite($current_suite_id);
        $cases = $suiteObj->loadTestCases(array(), array(), 'Active');

        $currentTestingDetails = TestingDetail::where(['user_id' => $userId, 'is_running' => 1])->first();

        $isReadOnly = (boolean) $currentTestingDetails;

        return view('pages.testingdetails.show', compact('cases', 'products', 'suites', 'currentTestingDetails', 'isReadOnly'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $model = TestingDetail::firstOrNew(['user_id' => get_current_user_id()]);
        $model->fill($request->all());
        if($request->get('is_running')){
            $model->start_time = Carbon::now();
        } else {
            $model->end_time = Carbon::now();
        }
        $model->save();
        addMessage('Testing details has been saved successfully.', 'success');
        return redirect()->secure('my-transaction-log');
    }

    /**
     * Show testing output popup
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function output($id, $laravel = false)
    {
        $entry = TransactionsLog::find($id);
        $s3 = AwsFacade::createClient('s3');
        $data = (string) $s3->getObject(array(
            'Bucket' => config('env.bucket.transactions'),
            'Key' => $entry['log_output'],
        ))['Body'];
        $link = $s3->getObjectUrl(config('env.bucket.transactions'), $entry['log_output'], '1 hour');
        if (!$laravel) {
            return view('pages.testingdetails.output', compact('data', 'link'));
        }
        return view('pages.testingdetails.output_laravel', compact('data', 'link'));
    }

    /**
     * Show Fail / Skip reason popup
     * @param $id
     * @return $this
     */
    public function reason($id)
    {
        $entry = TransactionsLog::find($id);
        $reason = $entry->reason;
        return view('pages.testingdetails.reason', compact('reason'));
    }

    /**
     * Show Fail / Skip reason popup
     * @param $id
     * @return $this
     */
    public function transactionReason($transactionId, $laravel = false)
    {
        $entry = Transaction::find($transactionId);
        $reason = $entry->reason;
        if($laravel){
            return view('pages.testingdetails.transaction_reason_laravel', compact('reason'));
        }
        return view('pages.testingdetails.transaction_reason', compact('reason'));
    }

    /**
     * Render logs for a given transaction
     * @param $transactionId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function logs($transactionId)
    {
        $logs = Transaction::find($transactionId)->logs;
        $transaction = Transaction::find($transactionId);
        $testCase = Post::where(['ID' => $transaction->test_case_id])->first();
        return view('pages.testingdetails.logs', compact('logs', 'testCase'));
    }
}
