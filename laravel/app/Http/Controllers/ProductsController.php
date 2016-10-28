<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Gate;
use Validator;

class ProductsController extends Controller
{

    public function index()
    {
        $pageTitle = 'My Products';
        return view('pages.products.index', compact('product', 'pageTitle'));
    }

    public function view($productSlug)
    {
        $product = Product::findBySlug($productSlug);
        $pageTitle = $product->full_name;
        return view('pages.products.view', compact('product', 'pageTitle'));
    }

    public function edit($productSlug)
    {
        $product = Product::findBySlug($productSlug);

        if (Gate::denies('change', $product)) {
            addMessage('You do not have enough permissions for this action. Please contact your organisation administrator for the ' . getSiteUrl() . ' site.', 'error');
            return redirect()->to('/');
        }

        $pageTitle = 'Edit ' . $product->full_name;
        return view('pages.products.edit', compact('product', 'pageTitle'));
    }

    /**
     * Update product
     * @param $productSlug
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($productSlug, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'visibility' => 'required|in:Public,Private,Community',
            'description' => 'required',
            'access_url' => 'url'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }
        $product = Product::findBySlug($productSlug);

        if (Gate::denies('change', $product)) {
            return response()->json(['message' => 'You do not have enough permissions for this action. Please contact your organisation administrator for the ' . getSiteUrl() . ' site.'], 422);
        }

        $product->access_url = $request->get('access_url');
        $product->released_at = getUTCTimeStamp($request->get('release_date'));
        $product->visibility = $request->get('visibility');
        $product->description = $request->get('description');
        $product->save();
        return response()->json(array('success' => true));
    }

    /**
     * Delete product
     * @param $productSlug
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($productSlug)
    {
        $product = Product::findBySlug($productSlug);

        if (Gate::denies('change', $product)) {
            return response()->json(['message' => 'You do not have enough permissions for this action. Please contact your organisation administrator for the ' . getSiteUrl() . ' site.'], 422);
        }
        $product->delete();
        return response()->json(array('success' => true));
    }
}
