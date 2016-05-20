<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class OrganisationMember
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
        $organisation = \App\OrganisationMember::where(['user_id' => Auth::user()->ID])->first();
        if (!$organisation) {
            return response()->json(['errors' => ['message' => 'Only organisation member can perform testing'], 'code' => 403], 403);
        }
        return $next($request);
    }
}
