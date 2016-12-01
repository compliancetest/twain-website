<?php

namespace App\Http\Controllers;

use App\Community;
use App\LaravelTestCase;
use App\LaravelTestSuite;
use App\Profile;
use App\TestCase;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class TestCasesController extends Controller
{

    /**
     * Create new test suite page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $pageTitle = 'Create Test Case';
        $isAdmin = doesUserAdminInAnyCommunity() || is_super_admin();
        return view('pages.test-cases.edit', compact('pageTitle', 'isAdmin'));
    }

    public function view($testCaseSlug, Request $request)
    {
        $testCase = LaravelTestCase::findBySlugOrFail($testCaseSlug);

        if (Gate::denies('viewTestCase', $testCase)) {
            addMessage('You do not have enough permissions to see this test case. Please contact your organisation administrator for the ' . getSiteUrl() . ' site.', 'error');
            return redirect()->to('/communities');
        }
        
        $testSuites = $testCase->testSuites;
        if ($request->get('suite_minor_family_mark')) {
            $suiteExist = $testCase->testSuites()->where('minor_family_mark', $request->get('suite_minor_family_mark'))->orderBy('created_at', 'DESC')->limit(1)->get();
            if (count($suiteExist)) {
                $testSuites = $suiteExist;
            }
        }
        $pageTitle = 'View Test Case | ' . $testCase->full_name;
        return view('pages.test-cases.view', compact('testCase', 'pageTitle', 'testSuites', 'request'));
    }

    public function edit($testCaseSlug)
    {
        $testCase = LaravelTestCase::findBySlugOrFail($testCaseSlug);
        if (Gate::denies('changeTestCase', $testCase)) {
            addMessage('You do not have enough permissions for this action. Please contact your organisation administrator for the ' . getSiteUrl() . ' site.', 'error');
            return response()->redirectTo('/test-case/' . $testCaseSlug);
        }
        $pageTitle = 'Edit Test Case | ' . $testCase->full_name;
        $testSuites = LaravelTestSuite::orderBy('name')->orderBy('version_major')->orderBy('version_minor')->get();
        return view('pages.test-cases.edit', compact('testCase', 'pageTitle', 'profiles', 'testSuites'));
    }

    public function store(Requests\TestCaseRequest $request)
    {
        if (!$this->hasAccess(Auth::user(), $request->get('community_id'))) {
            return response()->json(['messages' => ['You do not have enough permissions for this action. Please contact your organisation administrator for the ' . getSiteUrl() . ' site.']], 403);
        }

        $fullName = LaravelTestCase::generateCaseSuiteFullName($request);
        $slug = LaravelTestCase::generateSlug($fullName);

        if (LaravelTestCase::findBySlug($slug)) {
            return response()->json(['messages' => ['Test case with provided versions already exist']], 422);
        }

        $testCase = LaravelTestCase::create($request->all());
        $testCase->updateRelations($request);

        $testCase->full_name = $fullName;
        $testCase->slug = $slug;

        $testCase->save();
        return response()->json(['status' => 'success', 'redirect_to' => '/test-case/' . $testCase->slug]);
    }

    public function update($testCaseSlug, Requests\TestCaseRequest $request)
    {
        $oldTestCase = LaravelTestCase::findBySlug($testCaseSlug);

        if (!$this->hasAccess(Auth::user(), $request->get('community_id'))) {
            return response()->json(['messages' => ['You do not have enough permissions for this action. Please contact your organisation administrator for the ' . getSiteUrl() . ' site.']], 403);
        }

        if ($oldTestCase->isVersionUpdated($request)) {
            $fullName = LaravelTestCase::generateCaseSuiteFullName($request);
            $slug = LaravelTestCase::generateSlug($fullName);
            if (LaravelTestCase::findBySlug($slug)) {
                return response()->json(['messages' => ['Test case with provided versions already exist']], 422);
            }
            $testCase = LaravelTestCase::create($request->all());
            $testCase->full_name = $fullName;
            $testCase->slug = $slug;
        } else {
            $testCase = $oldTestCase;
            $oldTestCase->status = 'Obsolete';
            $testCase->full_name = LaravelTestCase::generateCaseSuiteFullName($request);
            $oldTestCase->save();
        }
        $testCase->fill($request->all());
        $testCase->updateRelations($request);
        $testCase->save();

        if ($request->get('send-notification')) {
            foreach ($testCase->testSuites as $testSuite) {
                $testSuite->notifySubscribers();
            }
        }

        return response()->json(['status' => 'success', 'redirect_to' => '/test-case/' . $testCase->slug]);
    }

    /**
     * Render test suites list few for selected community
     * @param $testCaseSlug
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function testSuitesList($testCaseSlug, Request $request)
    {
        $testCase = null;
        if ($testCaseSlug != 'create') {
            $testCase = LaravelTestCase::findBySlug($testCaseSlug);
        }
        return response()->json(['html' => view('pages.test-cases.partials.test-suites-list')->with(['testCase' => $testCase, 'testSuites' => Community::find($request->community_id)->testSuites])->render()]);
    }

    public function testSuitesData($testCaseSlug, Request $request)
    {
        $testCase = null;
        if ($testCaseSlug != 'create') {
            $testCase = LaravelTestCase::findBySlug($testCaseSlug);
        }
        $testSuites = LaravelTestSuite::whereIn('id', $request->get('test_suite_ids'))->get();
        $profiles = Profile::getSuitesProfiles((array)$request->get('test_suite_ids'));
        return response()->json(['html' => [
            'conformanceLevels' => view('pages.test-cases.partials.conformance-levels')->with(['testCase' => $testCase, 'testSuites' => $testSuites])->render(),
            'scenarios' => view('pages.test-cases.partials.scenarios')->with(['testCase' => $testCase, 'testSuites' => $testSuites])->render(),
            'features' => view('pages.test-cases.partials.features')->with(['testCase' => $testCase, 'testSuites' => $testSuites])->render(),
            'configuration_profile' => view('pages.test-cases.partials.configuration-profile')->with(['testCase' => $testCase, 'profiles' => $profiles])->render(),
            'execution_profile' => view('pages.test-cases.partials.execution-profile')->with(['testCase' => $testCase, 'profiles' => $profiles])->render(),
        ]
        ]);
    }

    public function hasAccess($user, $communityId)
    {
        $community = Community::find($communityId);
        if (!$community) {
            return false;
        }
        return is_super_admin() || $community->members()->where(['user_id' => $user->ID, 'is_admin' => true])->first();
    }

    /**
     * Render delete test case popup
     * @param $testCaseSlug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function deletePopup($testCaseSlug, $notRedirect = false)
    {
        $testCase = LaravelTestCase::findBySlug($testCaseSlug);
        return view('pages.test-cases.partials.confirm-delete-popup', compact('testCase', 'notRedirect'));
    }

    /**
     * Delete test case
     * @param $testCaseSlug
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($testCaseSlug, $notRedirect = false)
    {
        $testCase = LaravelTestCase::findBySlug($testCaseSlug);
        $community = Community::find($testCase->community_id);
        if (!$community->isAdmin(Auth::user()->ID) && !is_super_admin()) {
            return response()->json(['messages' => ['You do not have enough permissions for this action.']], 403);
        }

        $messages = [];
        if (count($testCase->transactions)) {
            $messages[] = "The test case can't be deleted because " . count($testCase->transactions) . " transactions still reference it.";
        }

        if ($messages) {
            return response()->json(['messages' => $messages], 422);
        }

        $testCase->delete();
        $rsp = ['status' => 'success'];
        if(!$notRedirect){
            $rsp['redirect_to'] = 'test-suites';
        }
        return response()->json($rsp);
    }
}
