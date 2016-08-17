<?php

namespace App\Http\Middleware;

use Closure;
use App\ApiLog;
use Illuminate\Support\Facades\Auth;

class LogAfterRequest
{

    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    public function terminate($request, $response)
    {
        $userInfo = get_browser(null, true);
        if (isset($userInfo['browser_name_regex'])) unset($userInfo['browser_name_regex']);
        if (isset($userInfo['browser_name_pattern'])) unset($userInfo['browser_name_pattern']);

        ApiLog::create([
            'user_id' => Auth::user()->ID,
            'ip_address' => $request->ip(),
            'request_type' => $request->method(),
            'uri' => $request->path(),
            'system_info' => json_encode($userInfo, JSON_PRETTY_PRINT),
            'request' => json_encode($request->all(), JSON_PRETTY_PRINT),
            'response' => json_encode($response->getData(), JSON_PRETTY_PRINT),
        ]);
    }
}
