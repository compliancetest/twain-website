<?php

namespace App\Http\Middleware;

use App\Community;
use Closure;

class CommunityAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $community = Community::findBySlug($request->route()->parameters()['community']);
        if($community->isAdmin()) {
            return $next($request);
        }
        return response('Unauthorized.', 401);
    }
}
