<?php

namespace App\Http\Middleware;

use Closure;

class WordpressAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (is_user_logged_in()) {
            return $next($request);
        } else {
            return redirect('/login');
        }
    }
}
