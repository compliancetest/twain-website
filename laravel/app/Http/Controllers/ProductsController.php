<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class ProductsController extends Controller
{
    public function view($productSlug)
    {
        return view('pages.products.view');
    }

    public function edit($productSlug)
    {
        return view('pages.products.edit');
    }
}
