<?php

namespace App\Http\Controllers;

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
        $transactions = Transaction::getUserTransactionLog($request);
        $filters = Transaction::getFilters($request);
        return view('pages.transactions.index', compact('transactions', 'filters', 'request'));
    }

    /**
     * Render filters view
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function filters(Request $request)
    {
        $data = [
            'filters' => Transaction::getFilters($request),
            'request' => $request,
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
        $data = [
            'transactions' => Transaction::getUserTransactionLog($request),
            'request' => $request,
        ];
        return response()->json(['html' => view('pages.transactions.transactions')->with($data)->render()]);
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
            $transaction->test_outcome_status_id = TestOutcomeStatus::getIdByCode(strtoupper($request->get('outcome_code')));
            if (boolval($request->get('reason'))) {
                $transaction->reason = $request->get('reason');
            }
            $transaction->save();
        }
        return response()->json(['success']);
    }
}
