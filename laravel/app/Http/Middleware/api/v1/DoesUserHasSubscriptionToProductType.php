<?php

namespace App\Http\Middleware\api\v1;

use Closure;
use App\Post;
use Illuminate\Support\Facades\Auth;

class DoesUserHasSubscriptionToProductType
{
    /**
     * Check that user has subscription at least to one test suites with product type provided via API request
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!in_array($request->get('product_type'), ['DataSource', 'Application'])) {
            return response()->json(['errors' => ['message' => ['The product type field is required.']], 'code' => 422], 422);
        }
        $suiteSubscriptions = \App\OrganisationSubscription::where(['user_id' => Auth::user()->ID])->get();
        foreach ($suiteSubscriptions as $suiteSubscription) {
            $type = Post::find($suiteSubscription->suite_family_mark)->meta()->where(['meta_key' => 'ts_tester_role'])->first()->meta_value;
            if ($type == $request->get('product_type')) {
                return $next($request);
            }
        }
        return response()->json(['errors' => ['message' => [sprintf("Please subscribe to Test Suite with '%s' Product Type", $request->get('product_type'))]], 'code' => 403], 403);
    }
}
