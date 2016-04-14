<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class SimpleAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $field = filter_var($request->getUser(), FILTER_VALIDATE_EMAIL) ? 'user_email' : 'user_login';
        return Auth::onceBasic($field) ?: $next($request);
    }
}
