<?php

namespace App\Http\Controllers;

use App\Community;
use App\LaravelTestSuite;
use App\Post;
use App\TransactionChangeLog;
use App\WpOptions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;
use App\Transaction;
use App\Http\Requests;
use App\TestOutcomeStatus;
use Illuminate\Http\Request;

class TransactionsController extends Controller
{

    /**
     * Display transactions list
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $perPage = Auth::user()->getTransactionsPerPage();
        $transactions = Transaction::getUserTransactionLog($request, $perPage);
        $filters = Transaction::getFilters($request);
        $explainRequestsEnabled = WpOptions::where(['option_name' => 'explain_requests', 'option_value' => 'yes'])->first();
        $pageTitle = 'My Test Results';
        return view('pages.transactions.index', compact('transactions', 'filters', 'request', 'pageTitle', 'perPage', 'explainRequestsEnabled'));
    }

    /**
     * Render filters view
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function filters(Request $request)
    {
        $perPage = $this->getItemsPerPage($request);
        $data = [
            'filters' => Transaction::getFilters($request),
            'request' => $request,
            'perPage' => $perPage
        ];
        return response()->json(['html' => view('pages.transactions.filters')->with($data)->render()]);
    }

    /**
     * Render transactions list based on filters
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function transactionsList(Request $request)
    {
        $perPage = $this->getItemsPerPage($request);
        $data = [
            'transactions' => Transaction::getUserTransactionLog($request, $perPage),
            'request' => $request,
            'perPage' => $perPage,
            'explainRequestsEnabled' => WpOptions::where(['option_name' => 'explain_requests', 'option_value' => 'yes'])->first(),
        ];
        return response()->json(['html' => view('pages.transactions.transactions')->with($data)->render()]);
    }

     /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function migrate(Request $request)
    {
        $suitesFrom = Auth::user()->suiteSubscriptions;
        $suitesTo = Auth::user()->suiteSubscriptions;
        $data = [
            'suitesFrom' => $suitesFrom,
            'suitesTo' => $suitesTo,
            'selectedSuiteFrom' => $request->get('suiteFrom'),
            'selectedSuiteTo' => $request->get('suiteTo'),
            'selectedProduct' => $request->get('product_id'),
            'transactions' => false,
            'products' => false,
        ];
        if($data['selectedSuiteFrom'] || $data['selectedSuiteTo']){
            if($data['selectedSuiteFrom']){
                $data['suitesTo'] = $data['suitesTo']->reject(function ($value, $key) use ($data) {
                    return $value->suite_minor_family_mark == $data['selectedSuiteFrom'];
                });
            }
            if($data['selectedSuiteFrom'] && $data['selectedSuiteTo']){
                $transactionsWhere = [
                    'suite_minor_family_mark' => $data['selectedSuiteFrom'],
                    'audit_record' => 1,
                ];
                if ($request->get('product_id')) {
                    $transactionsWhere['product_id'] = $request->get('product_id');
                }
                $transactions = Transaction::where($transactionsWhere)
                    ->join('test_cases', 'transactions.test_case_id', '=', 'test_cases.id')
                    ->with(['testCase'])
                    ->whereIn('subscription_id', Transaction::getUserSubscriptions())
                    ->orderBy('test_cases.full_name')
                    ->get();
                $selectedSuiteToEntry = LaravelTestSuite::getLatestSuiteForMinorFamilyMark($data['selectedSuiteTo']);
                $cases = $selectedSuiteToEntry->testCases()->pluck('test_cases.id')->toArray();
                foreach($transactions as $transaction){
                    if(in_array($transaction->test_case_id, $cases)){
                        $data['transactions'][$transaction->test_case_id][] = $transaction;
                    }
                }
            }
            $data['products'] = $transactions = Transaction::where([
                'suite_minor_family_mark' => $data['selectedSuiteFrom'],
                'audit_record' => 1,
            ])->whereIn('subscription_id', Transaction::getUserSubscriptions())
                ->join('products', 'transactions.product_id', '=', 'products.id')
                ->orderBy('full_name')
                ->groupBy('product_id')
                ->with('product')
                ->get();
            return response()->json(['html' => view('pages.transactions.popups.migrate')->with($data)->render()]);
        }
        return view('pages.transactions.popups.migrate')->with($data);
    }

    /**
     * Copy transactions data for new test suite
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function migrateTransactions(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'suiteTo' => 'required',
            'suiteFrom' => 'required',
            'transactions' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        $transactions  = Transaction::whereIn('id', $request->get('transactions'))->with('logs')->get();
        foreach($transactions as $transaction){
            $newTransaction = $transaction->replicate();
            $newTransaction->suite_minor_family_mark = $request->get('suiteTo');
            $newTransaction->subscription_id = Auth::user()->suiteSubscriptions->where('suite_minor_family_mark', $request->get('suiteTo'))->first()->id;
            $newTransaction->save();
            foreach ($transaction->getRelations() as $relation => $items) {
                foreach ($items as $item) {
                    unset($item->id);
                    $newTransaction->{$relation}()->create($item->toArray());
                }
            }
        }
         return response(['status' => 'success']);
    }

    /**
     * Change audit_record flag for transaction entry
     * @param $transactionId
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function updateauditrecord($transactionId, Request $request)
    {
        $transaction = Transaction::find($transactionId);
        if (TestOutcomeStatus::find($transaction->test_outcome_status_id)->code == 'PENDING') {
            return response(['message' => "You can mark Pending transaction as audit record"], 422);
        }
        $transaction->audit_record = $request->get('audit_record') === "true" ? true : false ;
        $transaction->save();
        return response(['status' => 'success']);
    }

    /**
     * Update transactions from VerifyRequest
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateTransactions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transactions' => 'array|required',
            'outcome_code' => 'required|in:Pass,Fail,Skip',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }
        foreach ($request->get('transactions') as $transactionToUpdate) {
            $transaction = Transaction::find($transactionToUpdate);
            if ($transaction->test_outcome_status_id != TestOutcomeStatus::getIdByCode(strtoupper($request->get('outcome_code')))) {
                TransactionChangeLog::addLog($transaction, Auth::user()->ID, strtoupper($request->get('outcome_code')));
            }
            $transaction->test_outcome_status_id = TestOutcomeStatus::getIdByCode(strtoupper($request->get('outcome_code')));
            if (boolval($request->get('reason'))) {
                $transaction->reason = $request->get('reason');
            }
            $transaction->save();
        }
        return response()->json(['success']);
    }

     /**
     * Delete transactions
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function batchDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transactions' => 'array|required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        foreach ($request->get('transactions') as $transactionToDelete) {
            $transaction = Transaction::find($transactionToDelete);
            if ($transaction->canBeDeleted()) {
                $transaction->delete();
            }
        }
        return response()->json(['success']);
    }

    /**
     * Mark records as Audit
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkAudit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transactions' => 'array|required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        foreach ($request->get('transactions') as $transactionToDelete) {
            $transaction = Transaction::find($transactionToDelete);
            if ($transaction->userHasAccess()) {
                $transaction->audit_record = true;
                $transaction->save();
            }
        }
        return response()->json(['success']);
    }

    public function getItemsPerPage($request)
    {
        $itemsPerPage = in_array($request->get('itemsCount'), [10, 25, 50, 100]) ? $request->get('itemsCount') : 25;
        Auth::user()->meta()->updateOrCreate(['meta_key' => 'transactions_per_page'], ['meta_value' => $itemsPerPage]);
        return $itemsPerPage;
    }

    /**
     * Show messages logs
     * @param $transactionId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function explanationLogs($transactionId)
    {
        $transaction = Transaction::find($transactionId);
        $logs = $transaction->explanationLogs;
        $community = Community::find(LaravelTestSuite::find($transaction->suite_minor_family_mark)->community_id);
        $isSupport = $community->isModerator() || $community->isAdmin() ? true : false;
        return view('pages.transactions.popups.explanation-logs', compact('logs', 'transactionId', 'isSupport'));
    }

    /**
     * Add new message and send email to users
     * @param $transactionId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     * @throws \Throwable
     */
    public function addExplanationLog($transactionId, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'string|required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        $transaction = Transaction::find($transactionId);
        $testSuite = LaravelTestSuite::getLatestSuiteForMinorFamilyMark($transaction->suite_minor_family_mark);

        $community = Community::find($testSuite->community_id);
        $isSupport = $community->isModerator() || $community->isAdmin() ? true : false;
        $transaction->explanationLogs()->create([
            'message' => $request->get('message'),
            'user_id' => Auth::user()->ID,
            'is_support' => $isSupport,
            'created_at' => getUTCTimeStamp(date('Y-m-d H:i:s')),
            'updated_at' => getUTCTimeStamp(date('Y-m-d H:i:s')),
        ]);

        $logs = Transaction::find($transactionId)->explanationLogs;

        $userId = Auth::user()->ID;
        $emailData = [
            '[name]' => cp_get_user_fullname($logs[0]->user_id),
            '[transaction_url]' => getSiteUrl() . '/my-transaction-log/?execution_id=' . $transaction->execution_id,
            '[env]' => get_option('env'),
            '[message_author_name]' => cp_get_user_fullname($userId),
            '[message]' => $request->get('message'),
            '[community]' => $community->title,
            '[website_url]' => getSiteUrl(),
            '[test_suite]' => $testSuite->full_name,
        ];
        if (!$isSupport) {
            sendEmails($community->getAdminsAndModerators(), 'send_explain_message_to_admin', $emailData);
        } else {
            sendEmails([['user_id' => $logs[0]->user_id]], 'send_explain_message_to_user', $emailData);
        }
        return response()->json(['html' => view('pages.transactions.popups.explanation-logs', compact('logs', 'transactionId'))->render()]);
    }
}
