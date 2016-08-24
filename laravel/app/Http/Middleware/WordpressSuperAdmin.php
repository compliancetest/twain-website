<?php

namespace App\Http\Middleware;

use Closure;

class WordpressSuperAdmin
{
    /**
     * Check that user is wordpress super admin
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (is_super_admin()) {
            return $next($request);
        }
        addMessage("You don't have access to that page", 'error');
        return redirect('/');
    }
}
