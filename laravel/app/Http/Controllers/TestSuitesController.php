<?php

namespace App\Http\Controllers;

use App\Community;
use App\Http\Requests;
use App\LaravelTestSuite;
use Illuminate\Http\Request;
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
        $community = Community::find($testSuite->community_id);
        $isAdmin = $community->isAdmin() || is_super_admin();

        $data = [
            'testSuite' => $testSuite,
            'community' => $community,
            'pageTitle' => $testSuite->full_name,
            'isAdmin' => $isAdmin,
            'isSupport' => $isAdmin || $community->isModerator(),
            'installer' => $community->getTestTool($testSuite->product_type),
            'installerX64' => $community->getTestTool($testSuite->product_type, true),
        ];

        return view('pages.test-suites.view')->with($data);
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

    public function getTestCasesList($testSuiteSlug, Request $request)
    {
        $testSuite = LaravelTestSuite::findBySlug($testSuiteSlug);
        $data = [
            'filters' => $request->all(),
            'testSuite' => $testSuite,
            'isAdmin' => Community::find($testSuite->community_id)->isAdmin() || is_super_admin(),
        ];
        return response()->json(['html' => view('pages.test-suites.partials/test-cases-list')->with($data)->render()]);
    }
}
