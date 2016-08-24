<?php

namespace App\Http\Controllers;

use App\ApiLog;
use App\Http\Requests;
use Illuminate\Http\Request;

class ApiLogsController extends Controller
{

    public function index(Request $request)
    {
        $logs = ApiLog::getLogs($request->all());
        $filters = ApiLog::getFilters($request->all());
        return view('pages.api-logs.index', compact('logs', 'filters', 'request'));
    }

    /**
     * Render filters view
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function filters(Request $request)
    {
        $data = [
            'filters' => ApiLog::getFilters($request->all()),
            'request' => $request,
        ];
        return response()->json(['html' => view('pages.api-logs.filters')->with($data)->render()]);
    }

    /**
     * Render transactions list based on filters
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logsList(Request $request)
    {
        $data = [
            'logs' => ApiLog::getLogs($request->all()),
            'request' => $request,
        ];
        return response()->json(['html' => view('pages.api-logs.logs')->with($data)->render()]);
    }

    /**
     * ApiLog request popup
     * @param $logId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function requestData($logId)
    {
        $apiLog = ApiLog::find($logId);
        $data = $apiLog->request;
        return view('pages.api-logs.popups.request', compact('data'));
    }

    /**
     * ApiLog response content
     * @param $logId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function responseData($logId)
    {
        $apiLog = ApiLog::find($logId);
        $data = $apiLog->response;
        return view('pages.api-logs.popups.response', compact('data'));
    }
}
