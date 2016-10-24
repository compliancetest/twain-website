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
            return response()->json(['messages' => ['Only organisation member can perform testing'], "status" => "error", 'code' => 403], 403);
        }
        return $next($request);
    }
}
