<?php

namespace App\Http\Middleware;

use App\Post;
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
        $product = Post::where(['post_name' => $request->route()->parameters()['productId'], 'post_type' => 'product-service'])->first();
        if (!$product) {
            return response()->json(['errors' => ['message' => 'Product id is invalid'], 'code' => 404], 404);
        }
        return $next($request);
    }
}
