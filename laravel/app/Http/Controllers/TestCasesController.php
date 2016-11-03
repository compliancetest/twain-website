<?php

namespace App\Http\Controllers;

use App\LaravelTestCase;
use Illuminate\Http\Request;

use App\Http\Requests;

class TestCasesController extends Controller
{

    public function view($testCaseSlug)
    {
        $testCase = LaravelTestCase::findBySlug($testCaseSlug);
        $pageTitle = 'View Test Case | ' . $testCase->full_name;
        return view('pages.test-cases.view', compact('testCase', 'pageTitle'));
    }

    public function edit($testCaseSlug)
    {
        return view('pages.test-cases.edit');
    }

}
