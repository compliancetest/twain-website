<?php

namespace App\Http\Controllers;

use App\TestOutcomeStatus;
use App\Transaction;
use Illuminate\Http\Request;

use App\Http\Requests;

class TransactionsController extends Controller
{

    public function index(Request $request)
    {
        list($filters, $transactions) = Transaction::getUserTransactionLog($request);
        return view('pages.transactions.index', compact('transactions', 'filters', 'request'));
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
}
