<?php

namespace App\Http\Middleware;

use App\Product;
use Closure;

class PostExists
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
        $product = Product::findBySlug($request->route()->parameters()['productId']);
        if (!$product) {
            return response()->json(['messages' => ['Product id is invalid'], "status" => "error", 'code' => 404], 404);
        }
        return $next($request);
    }
}
