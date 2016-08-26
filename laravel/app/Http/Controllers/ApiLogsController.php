<?php

namespace App\Http\Controllers;

use App\ApiLog;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

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
        return view('pages.api-logs.popups.request', compact('data', 'apiLog'));
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
        return view('pages.api-logs.popups.response', compact('data', 'apiLog'));
    }

    public function downloadRequest($logId)
    {
        $apiLog = ApiLog::find($logId);
        $fileData = '';
        $request = json_decode($apiLog->request, true);
        foreach($request as $key => $value){
            $fileData .= $key . ':' . $value . PHP_EOL;
        }
        return response($fileData, 200)->withHeaders([
            'Content-Type' => 'plain/txt',
            'Content-Disposition' => 'attachment; filename="requestData.txt"'
        ]);
    }

    public function downloadResponse($logId)
    {
        $apiLog = ApiLog::find($logId);
        return response($apiLog->response, 200)->withHeaders([
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="responseData.json"'
        ]);
    }
}
