<?php

namespace App\Http\Controllers;

use App\Transaction;
use Illuminate\Http\Request;

use App\Http\Requests;

class TransactionsController extends Controller
{

    public function updateauditrecord($transactionId, Request $request)
    {
        $transaction = Transaction::find($transactionId);
        $transaction->audit_record = $request->get('audit_record') === "true" ? true : false ;
        $transaction->save();
        return response(['status' => 'success']);
    }
}
