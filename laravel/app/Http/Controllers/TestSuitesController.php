<?php

namespace App\Http\Controllers;

use App\Community;
use App\Http\Requests;
use App\LaravelTestSuite;
use App\OrganisationMember;
use App\OrganisationSubscription;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'pageTitle' => 'View Test Suite | ' . $testSuite->full_name,
            'isAdmin' => $isAdmin,
            'isSupport' => $isAdmin || $community->isModerator(),
            'installer' => $community->getTestTool($testSuite->product_type),
            'installerX64' => $community->getTestTool($testSuite->product_type, true),
        ];

        return view('pages.test-suites.view')->with($data);
    }

    /**
     * Create new test suite page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $pageTitle = 'Create Test Suite';
        $isAdmin = doesUserAdminInAnyCommunity() || is_super_admin();
        return view('pages.test-suites.edit', compact('pageTitle', 'isAdmin'));
    }

    public function store(Requests\TestSuiteRequest $request)
    {
        if (!(is_super_admin() || doesUserCommunityAdmin(Auth::user()->ID, $request->get('community_id')))) {
            return response()->json(['messages' => ['You do not have enough permissions for this action. Please contact your organisation administrator for the ' . getSiteUrl() . ' site.']], 403);
        }

        $testSuite = LaravelTestSuite::create($request->all());
        $testSuite->updateRelations($request);
        $testSuite->save();
        return response()->json(['status' => 'success', 'redirect_to' => '/laravel-test-suite/' . $testSuite->slug]);
    }

    /**
     * Edit test suite page
     * @param $testSuiteSlug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($testSuiteSlug)
    {
        $testSuite = LaravelTestSuite::findBySlug($testSuiteSlug);

        if (Gate::denies('changeTestSuite', $testSuite)) {
            addMessage('You do not have enough permissions for this action. Please contact your organisation administrator for the ' . getSiteUrl() . ' site.', 'error');
            return redirect()->to('/');
        }

        $pageTitle = 'Edit Test Suite | ' . $testSuite->full_name;
        $suiteCommunity = Community::find($testSuite->community_id);
        $isAdmin = $suiteCommunity->isAdmin() || is_super_admin();
        return view('pages.test-suites.edit', compact('testSuite', 'pageTitle', 'isAdmin', 'suiteCommunity'));
    }

    /**
     * Load community profile types
     * @param $testSuiteSlug
     * @param $communityId
     * @return \Illuminate\Http\JsonResponse
     */
    public function communityProfileTypes($testSuiteSlug, $communityId)
    {
        $data = [
            'suiteCommunity' => Community::find($communityId),
            'testSuite' => LaravelTestSuite::findBySlug($testSuiteSlug)
        ];
        return response()->json(['html' => view('pages.test-suites.partials.community-profile-types')->with($data)->render()]);
    }

    /**
     * Community test suites dropdown
     * @param $testSuiteSlug
     * @param $communityId
     * @return \Illuminate\Http\JsonResponse
     */
    public function communityTestSuites($testSuiteSlug, $communityId)
    {
        $data = [
            'suiteCommunity' => Community::find($communityId),
            'testSuite' => LaravelTestSuite::findBySlug($testSuiteSlug)
        ];
        return response()->json(['html' => view('pages.test-suites.partials.community-test-suites')->with($data)->render()]);
    }

    /**
     * Update test suite data
     * @param $testSuiteSlug
     * @param Requests\TestSuiteRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($testSuiteSlug, Requests\TestSuiteRequest $request)
    {
        $oldTestSuite = LaravelTestSuite::findBySlug($testSuiteSlug);

        if (Gate::denies('change', $oldTestSuite)) {
            return response()->json(['messages' => ['You do not have enough permissions for this action. Please contact your organisation administrator for the ' . getSiteUrl() . ' site.']], 403);
        }

        if ($oldTestSuite->isVersionUpdated($request)) {
            $testSuite = LaravelTestSuite::create($request->all());
            if ($oldTestSuite->version_major < $request->get('version_major')) {

            } else {
                if ($oldTestSuite->version_minor < $request->get('version_minor')) {
                    $testSuite->major_family_mark = $oldTestSuite->major_family_mark;
                } else if ($oldTestSuite->version_patch < $request->get('version_patch')) {
                    $testSuite->major_family_mark = $oldTestSuite->major_family_mark;
                    $testSuite->version_patch = $oldTestSuite->version_patch;
                }
            }
        } else {
            $testSuite = $oldTestSuite;
        }
        $testSuite->fill($request->all());
        $testSuite->updateRelations($request);
        $testSuite->save();
        return response()->json(['status' => 'success' , 'redirect_to' => '/laravel-test-suite/' . $testSuite->slug]);
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

    /**
     * Manage user subscription to test suite
     * @param $testSuiteSlug
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscription($testSuiteSlug, Request $request)
    {
        $testSuite = LaravelTestSuite::findBySlug($testSuiteSlug);
        if ($request->get('status')) {
            $existingSubscription = OrganisationSubscription::where([
                'organisation_id' => Auth::user()->organisation[0]->id,
                'suite_minor_family_mark' => $testSuite->minor_family_mark,
                'user_id' => 0,
            ])->first();
            if ($existingSubscription) {
                $existingSubscription->update(['user_id' => Auth::user()->ID]);
            } else {
                $nickname = $testSuite->short_name . '_v' . $testSuite->version_major . '_' . $testSuite->version_minor;
                $existingSubscription = OrganisationSubscription::create([
                    'nickname' => OrganisationSubscription::getUniqueSlug($nickname),
                    'organisation_id' => Auth::user()->organisation[0]->id,
                    'purchaser_id' => Auth::user()->ID,
                    'purchased_date' => date('Y-m-d H:i:s'),
                    'status' => 'Active',
                    'user_id' => Auth::user()->ID,
                    'suite_minor_family_mark' => $testSuite->minor_family_mark,
                ]);
            }
            //Send Email
            $emailData = array(
                '[name]'            => cp_get_user_fullname(Auth::user()->ID),
                '[email]'           => Auth::user()->user_email,
                '[suite_name]'      => $testSuite->full_name,
                '[nickname]'        => $existingSubscription->nickname,
                '[organisation]'    => Auth::user()->organisation[0]->organisation_name,
                '[community_url]'   => getSiteUrl() . '/communities/' . Community::find($testSuite->community_id)->slug
            );

            cp_send_email(array('name' => $emailData['[name]'], 'email' => $emailData['[email]']), 'allocate_subscription_to_user', $emailData);
            cp_send_email_to_admin('allocate_subscription_to_user_admin', $emailData);

        } else {
            $subscription = OrganisationSubscription::where([
                'organisation_id' => Auth::user()->organisation[0]->id,
                'suite_minor_family_mark' => $testSuite->minor_family_mark,
                'user_id' => Auth::user()->ID,
            ])->first();

            $orgAdmin = User::find(OrganisationMember::where([
                'organisation_id' => Auth::user()->organisation[0]->id,
                'is_admin' => 1,
            ])->first()->user_id);
            $emailData = array(
                '[name]' => cp_get_user_fullname($orgAdmin->ID),
                '[email]' => $orgAdmin->user_email,
                '[nickname]' => $subscription->nickname,
                '[organisation]' => Auth::user()->organisation[0]->organisation_name,
                '[suite_name]' => $testSuite->full_name
            );

            $subscription->user_id = 0;
            $subscription->save();

            cp_send_email(array('name' => $emailData['[name]'], 'email' => $emailData['[email]']), 'cancel_subscription', $emailData);
            cp_send_email_to_admin('cancel_subscription_admin', $emailData);
        }
        return response()->json(['html' => view('pages.test-suites.partials.subscriptions-section')->with(['testSuite' => $testSuite])->render()]);
    }

    public function userTestSuites()
    {
        $userSubscriptions = Auth::user()->suiteSubscriptions;
        return view('pages.my.test-suites.index', compact('userSubscriptions'));
    }
}
