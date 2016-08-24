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
        if ($request->path() != 'api/v1/echo') {
            ApiLog::create([
                'user_id' => (integer)Auth::user()->ID,
                'ip_address' => $request->getClientIp(),
                'request_type' => $request->method(),
                'uri' => $request->path(),
                'request' => json_encode($request->all(), JSON_PRETTY_PRINT),
                'response' => json_encode($response->getData(), JSON_PRETTY_PRINT),
            ]);
        }
    }
}
