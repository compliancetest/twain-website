<?php

namespace App\Http\Controllers;

use App\LaravelTestSuite;
use Illuminate\Http\Request;

use App\Http\Requests;

class TestSuitesController extends Controller
{
    /**
     * View / search test suites page
     */
    public function index()
    {
        $pageTitle = 'Search Test Suites';
        return view('pages.test-suites.index', compact('pageTitle'));
    }

    public function view($testSuiteSlug)
    {
        $testSuite = LaravelTestSuite::findBySlug($testSuiteSlug);
        $pageTitle = $testSuite->full_name;
        return view('pages.test-suites.view', compact('testSuite', 'pageTitle'));
    }

    public function edit($testSuiteSlug)
    {
        $testSuite = LaravelTestSuite::findBySlug($testSuiteSlug);
        $pageTitle = 'Edit ' . $testSuite->full_name;
        return view('pages.test-suites.edit', compact('testSuite', 'pageTitle'));
    }
}
