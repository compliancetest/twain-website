<?php

namespace App\Http\Middleware;

use App\Community;
use Closure;

class CommunityUser
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
        if($community->hasAccess()) {
            return $next($request);
        }
        return response('Unauthorized.', 401);
    }
}
