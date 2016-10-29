<?php

namespace App\Http\Controllers;

use App\Community;
use App\Http\Requests;
use App\LaravelTestSuite;
use Illuminate\Support\Facades\Gate;

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

    /**
     * View test suite page
     * @param $testSuiteSlug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function view($testSuiteSlug)
    {
        $testSuite = LaravelTestSuite::findBySlug($testSuiteSlug);
        $pageTitle = $testSuite->full_name;
        $isAdmin = Community::find($testSuite->community_id)->isAdmin() || is_super_admin();
        return view('pages.test-suites.view', compact('testSuite', 'pageTitle', 'isAdmin'));
    }

    /**
     * Edit test suite page
     * @param $testSuiteSlug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($testSuiteSlug)
    {
        $testSuite = LaravelTestSuite::findBySlug($testSuiteSlug);

        if (Gate::denies('change', $testSuite)) {
            addMessage('You do not have enough permissions for this action. Please contact your organisation administrator for the ' . getSiteUrl() . ' site.', 'error');
            return redirect()->to('/');
        }

        $pageTitle = 'Edit ' . $testSuite->full_name;
        $isAdmin = Community::find($testSuite->community_id)->isAdmin() || is_super_admin();
        return view('pages.test-suites.edit', compact('testSuite', 'pageTitle', 'isAdmin'));
    }
}
