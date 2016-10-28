<?php

namespace App\Http\Middleware;

use Closure;
use App\CommunityOrganisationsApprovedTestSuites;
use Illuminate\Support\Facades\Auth;

class OrganisationCanPerformTesting
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
        $organisation = \App\OrganisationMember::where(['user_id' => Auth::user()->ID])->first();
        if (!CommunityOrganisationsApprovedTestSuites::where(['organisation_id' => $organisation->organisation_id])->first()) {
            return response()->json(['messages' => ["Your organisation can't perform testing."], "status" => "error", 'code' => 403], 403);
        }
        return $next($request);
    }
}
