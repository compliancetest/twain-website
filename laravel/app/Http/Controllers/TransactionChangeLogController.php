<?php

namespace App\Http\Controllers;

use App\TransactionChangeLog;
use Illuminate\Http\Request;

use App\Http\Requests;

class TransactionChangeLogController extends Controller
{
     public function index(Request $request)
    {
        $logs = TransactionChangeLog::getLogs($request->all());
        $filters = TransactionChangeLog::getFilters($request->all());
        $pageTitle = 'My Test Results Change Logs';
        return view('pages.transactions-change-logs.index', compact('logs', 'filters', 'request', 'pageTitle'));
    }

    /**
     * Render filters view
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function filters(Request $request)
    {
        $data = [
            'filters' => TransactionChangeLog::getFilters($request->all()),
            'request' => $request,
        ];
        return response()->json(['html' => view('pages.transactions-change-logs.filters')->with($data)->render()]);
    }

    /**
     * Render transaction change logs list based on filters
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logsList(Request $request)
    {
        $data = [
            'logs' => TransactionChangeLog::getLogs($request->all()),
            'request' => $request,
        ];
        return response()->json(['html' => view('pages.transactions-change-logs.logs')->with($data)->render()]);
    }
}
