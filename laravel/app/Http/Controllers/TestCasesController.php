<?php

namespace App\Http\Controllers;

use App\LaravelTestCase;
use App\LaravelTestSuite;
use App\Profile;
use Illuminate\Http\Request;

use App\Http\Requests;

class TestCasesController extends Controller
{

    public function view($testCaseSlug, Request $request)
    {
        $testCase = LaravelTestCase::findBySlug($testCaseSlug);
        $testSuites = $testCase->testSuites;
        if($request->get('suite_minor_family_mark')){
            $suiteExist = $testCase->testSuites()->where('minor_family_mark', $request->get('suite_minor_family_mark'))->orderBy('created_at', 'DESC')->limit(1)->get();
            if(count($suiteExist)){
                $testSuites = $suiteExist;
            }
        }
        $pageTitle = 'View Test Case | ' . $testCase->full_name;
        return view('pages.test-cases.view', compact('testCase', 'pageTitle', 'testSuites'));
    }

    public function edit($testCaseSlug)
    {
        $testCase = LaravelTestCase::findBySlug($testCaseSlug);
        $pageTitle = 'Edit Test Case | ' . $testCase->full_name;
        $testSuites = LaravelTestSuite::orderBy('name')->orderBy('version_major')->orderBy('version_minor')->get();
        $profiles = Profile::getSuitesProfiles(array_keys($testCase->testSuites->keyBy('id')->toArray()));
        return view('pages.test-cases.edit', compact('testCase', 'pageTitle', 'profiles', 'testSuites'));
    }

}
