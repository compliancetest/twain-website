<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\TestSuiteSearch;
use Illuminate\Http\Request;

class TestSuitesSearchController extends Controller
{

    /**
     * Display search entries list
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $entries = TestSuiteSearch::getTestSuites($request);
        $filters = TestSuiteSearch::getFilters($request);
        $pageTitle = 'Test Suites';
        return view('pages.search.test-suites.index', compact('entries', 'filters', 'request', 'pageTitle'));
    }

    /**
     * Render filters view
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function filters(Request $request)
    {
        $data = [
            'filters' => TestSuiteSearch::getFilters($request),
            'request' => $request,
        ];
        return response()->json(['html' => view('pages.search.test-suites.filters')->with($data)->render()]);
    }

    /**
     * Render search entries list based on filters
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function entries(Request $request)
    {
        $results = TestSuiteSearch::getTestSuites($request);
        $data = [
            'entries' => $results,
            'request' => $request,
        ];
        return response()->json(['html' => view('pages.search.test-suites.list')->with($data)->render()]);
    }
}
