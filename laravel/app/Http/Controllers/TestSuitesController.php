<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class TestSuitesController extends Controller
{
    public function view($testSuiteSlug)
    {
        return view('pages.test-suites.view');
    }

    public function edit($testSuiteSlug)
    {
        return view('pages.test-suites.edit');
    }
}
