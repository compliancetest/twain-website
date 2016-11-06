<?php

namespace App\Http\Controllers;

use App\LaravelTestCase;
use App\LaravelTestSuite;
use App\Profile;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Gate;

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

    public function store(Requests\TestCaseRequest $request)
    {
//        if (!(is_super_admin() || doesUserCommunityAdmin(Auth::user()->ID, $request->get('community_id')))) {
//            return response()->json(['messages' => ['You do not have enough permissions for this action. Please contact your organisation administrator for the ' . getSiteUrl() . ' site.']], 403);
//        }

        $testCase = LaravelTestCase::create($request->all());
        $testCase->updateRelations($request);
        $testCase->save();
        return response()->json(['status' => 'success', 'redirect_to' => '/laravel-test-suite/' . $testCase->slug]);
    }

     public function update($testCaseSlug, Requests\TestCaseRequest $request)
    {
        $oldTestCase = LaravelTestCase::findBySlug($testCaseSlug);

        if (Gate::denies('change', $oldTestCase)) {
            return response()->json(['messages' => ['You do not have enough permissions for this action. Please contact your organisation administrator for the ' . getSiteUrl() . ' site.']], 403);
        }

        if ($oldTestCase->isVersionUpdated($request)) {
            $testCase = LaravelTestCase::create($request->all());
            if ($oldTestCase->version_major < $request->get('version_major')) {

            } else {
                if ($oldTestCase->version_minor < $request->get('version_minor')) {
                    $testCase->major_family_mark = $oldTestCase->major_family_mark;
                } else if ($oldTestCase->version_patch < $request->get('version_patch')) {
                    $testCase->major_family_mark = $oldTestCase->major_family_mark;
                    $testCase->version_patch = $oldTestCase->version_patch;
                }
            }
        } else {
            $testSuite = $oldTestCase;
        }
        $testSuite->fill($request->all());
        $testSuite->updateRelations($request);
        $testSuite->save();
        return response()->json(['status' => 'success' , 'redirect_to' => '/laravel-test-suite/' . $testSuite->slug]);
    }
}
