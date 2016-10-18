<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class TestCasesController extends Controller
{

    public function view($testCaseSlug)
    {
        return view('pages.test-cases.view');
    }

    public function edit($testCaseSlug)
    {
        return view('pages.test-cases.edit');
    }

}
