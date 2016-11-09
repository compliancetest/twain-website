<?php

namespace App\Http\Middleware;

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
            return response()->json(['messages' => ['The product type field is required.'], 'status' => 'error', 'code' => 422], 422);
        }
        foreach (Auth::user()->suiteSubscriptions as $suiteSubscription) {
            if ($suiteSubscription->testSuite->product_type == $request->get('product_type')) {
                return $next($request);
            }
        }
        return response()->json(['messages' => [sprintf("Please subscribe to Test Suite with '%s' Product Type", $request->get('product_type'))], 'status' => 'error' , 'code' => 403], 403);
    }
}
