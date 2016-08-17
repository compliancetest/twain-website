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
        ApiLog::create([
            'user_id' => Auth::user()->ID,
            'ip_address' => $request->ip(),
            'request_type' => $request->method(),
            'uri' => $request->path(),
            'request' => json_encode($request->all(), JSON_PRETTY_PRINT),
            'response' => json_encode($response->getData(), JSON_PRETTY_PRINT),
        ]);
    }
}
