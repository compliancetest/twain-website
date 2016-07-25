<?php

namespace App\Http\Middleware;

use Closure;

class CommunitySupport
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
        $community = \App\Community::findBySlug($request->route()->parameters()['community']);
        if ($community->isAdmin() || $community->isModerator()) {
            return $next($request);
        }
        return response('Unauthorized.', 401);
    }
}
