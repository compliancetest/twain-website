<?php

namespace App\Http\Controllers;

use App\Claim;
use App\OrganisationSubscription;
use App\Post;
use App\PostMeta;
use App\PricingPlan;
use App\TestPlan;
use App\TestPlanExcludedCases;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TestPlansController extends Controller
{

    /**
     * Display all user's test plans
     * @return $this
     */
    public function index()
    {
        $data = [
            'userSuites' => Auth::user()->getUserTestPlans(),
            'pageTitle' => 'Test Suite Coverage',
        ];
        return view('pages.my.coverage.index')->with($data);
    }

    /**
     * Render create test plan form
     * @return mixed
     */
    public function create($suiteId)
    {
        $subscription = OrganisationSubscription::where(['suite_family_mark' => $suiteId])->first();
        $pricingPlan = PricingPlan::where(['id' => $subscription->pricing_plan_id])->with('attributes')->first();
        $attributes = $pricingPlan->attributes->keyBy('type')->get('role');

        $suiteType = PostMeta::where(['post_id' => $suiteId, 'meta_key' => 'ts_tester_role'])->first();
        $suiteProtocolVersions = PostMeta::where(['post_id' => $suiteId, 'meta_key' => 'protocol_versions'])->first();
        $data = [
            'products' => Auth::user()->getProducts($suiteType->meta_value, json_decode($suiteProtocolVersions->meta_value, true)),
            'levels' => explode(',', $attributes->value),
            'roles' => explode(',', $attributes->name),
            'suiteId' => $suiteId,
        ];

        return view('pages.my.coverage.create')->with($data)->render();
    }

    /**
     * Save new test plan
     * @param Requests\TestPlanRequest $request
     * @return \Symfony\Component\HttpFoundation\Response|static
     */
    public function store(Requests\TestPlanRequest $request)
    {
        $testPlanData = [
            'product_id' => $request->get('product_id'),
            'suite_id' => $request->get('suite_id'),
            'level' => $request->get('level'),
            'role' => $request->get('role'),
            'is_claimed' => false,
        ];
        if (TestPlan::where($testPlanData)->first()) {
            return JsonResponse::create(['message' => 'You already have test plan for this test suite'], 422);
        }

        if ($request->get('role') == 'Application') {
            $configuredTestSuites = json_decode(Post::find($request->get('product_id'))->getMetaByKey('product_suites'));
            if (!in_array($request->get('suite_id'), $configuredTestSuites)) {
                return JsonResponse::create(['message' => 'The product is not configured for the selected test suite. Please configure it in the test tool.'], 422);
            }
        }

        $testPlan = TestPlan::create($request->all());
        $testPlan->creator_id = Auth::user()->ID;

        $organisationSubscription = OrganisationSubscription::where(['user_id' => Auth::user()->ID, 'suite_family_mark' => $request->get('suite_id')])->first();
        $testPlan->organisation_subscription_id = $organisationSubscription->id;

        $testPlan->save();

        $productType = str_replace(' ', '', $request->get('role'));
        if ($productType == 'DataSource') {
            $testPlan->excludeTestCases();
        }

        return JsonResponse::create(['status' => 'success', 'html' => view('pages.my.coverage.test_plans_list', ['userSuites' => Auth::user()->getUserTestPlans()])->render()]);
    }

    /**
     * Render edit test plan form
     * @param $testPlanId
     * @return mixed
     */
    public function edit($testPlanId)
    {
        $testPlan = TestPlan::find($testPlanId);
        $subscription = OrganisationSubscription::where(['suite_family_mark' => $testPlan->suite_id])->first();
        $pricingPlan = PricingPlan::where(['id' => $subscription->pricing_plan_id])->with('attributes')->first();
        $attributes = $pricingPlan->attributes->keyBy('type')->get('role');

        $suiteType = PostMeta::where(['post_id' => $testPlan->suite_id, 'meta_key' => 'ts_tester_role'])->first()->meta_value;
        $suiteProtocolVersions = PostMeta::where(['post_id' => $testPlan->suite_id, 'meta_key' => 'protocol_versions'])->first();

        $data = [
            'products' => Auth::user()->getProducts($suiteType, json_decode($suiteProtocolVersions->meta_value, true)),
            'levels' => explode(',', $attributes->value),
            'roles' => explode(',', $attributes->name),
            'testPlan' => $testPlan,
        ];

        return view('pages.my.coverage.edit')->with($data)->render();
    }

    /**
     * @param $testPlanId
     * @param $testCaseId
     * @return mixed
     */
    public function view($testPlanId, $testCaseId)
    {
        $testCase = Post::find($testCaseId);
        $testPlan = TestPlan::find($testPlanId);
        $excludedCases = $testPlan->getExcludedCases();
        $isExcluded = array_key_exists($testCaseId, $excludedCases) ? $excludedCases[$testCaseId] : false;
        $data = [
            'testCase' => $testCase,
            'testPlan' => $testPlan,
            'isExcluded' => $isExcluded,
        ];
        return view('pages.my.coverage.view')->with($data)->render();
    }

    /**
     * @param $testPlanId
     * @param $testCaseId
     * @return mixed
     */
    public function exclude($testPlanId, $testCaseId, Request $request)
    {
        if($request->get('case_exclude') == 'on'){
            TestPlanExcludedCases::create([
                'test_case_id' => $testCaseId,
                'test_plan_id' => $testPlanId,
                'reason' => $request->get('reason'),
                'excluded_by_user_id' => \Auth::user()->ID,
            ]);
        } else {
            TestPlanExcludedCases::where(['test_case_id' => $testCaseId, 'test_plan_id' => $testPlanId])->delete();
        }
        return JsonResponse::create(['status' => 'success', 'html' => view('pages.my.coverage.test_plans_list', ['userSuites' => Auth::user()->getUserTestPlans()])->render()]);
    }


    public function update($testPlanId, Requests\TestPlanRequest $request)
    {
        $testPlan = TestPlan::find($testPlanId);
        if (OrganisationSubscription::find($testPlan->organisation_subscription_id)->organisation_id ==
            OrganisationSubscription::where(['user_id' => Auth::user()->ID, 'suite_family_mark' => $testPlan->suite_id])->first()->organisation_id
        ) {

            if ($testPlan->role == 'Application') {
                $configuredTestSuites = json_decode(Post::find($testPlan->product_id)->getMetaByKey('product_suites'));
                if (!in_array($testPlan->suite_id, $configuredTestSuites)) {
                    return JsonResponse::create(['message' => 'The product is not configured for the selected test suite. Please configure it in the test tool.'], 422);
                }
            }

            $testPlan = $testPlan->fill($request->all());
            $testPlan->save();
            return JsonResponse::create(['status' => 'success', 'html' => view('pages.my.coverage.test_plans_list', ['userSuites' => Auth::user()->getUserTestPlans()])->render()]);
        }
        return JsonResponse::create(['status' => 'Forbidden!'], 403);
    }

    /**
     * Delete test plan
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response|static
     */
    public function destroy($id)
    {
        $testPlan = TestPlan::find($id);
        if (OrganisationSubscription::find($testPlan->organisation_subscription_id)->organisation_id ==
            OrganisationSubscription::where(['user_id' => Auth::user()->ID, 'suite_family_mark' => $testPlan->suite_id])->first()->organisation_id
        ) {
            $testPlan->delete();
            return JsonResponse::create(['status' => 'success']);
        }
        return JsonResponse::create(['status' => 'Forbidden!'], 403);
    }

    /**
     * Generate claim for test plan
     * @param $testPlanId
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function claim($testPlanId)
    {
        $user = Auth::user();
        $testPlan = TestPlan::find($testPlanId);
        if (!$testPlan || !$testPlan->canBeClaimed()) {
            addMessage('You must complete the test plan before a claim can be made.', 'warning');
            return redirect('test-suite-coverage');
        }

        if (!empty($testPlan->claim)) {
            addMessage('An existing claim for this test plan already exists. Please delete it if you wish to update your claim for this test plan.', 'warning');
            return redirect('test-suite-coverage');
        }

        $claim = $testPlan->claim()->create([
            'product_id' => $testPlan->product_id,
            'creator_id' => $user->ID,
            'organisation_id' => $user->organisation[0]->id,
            'test_suite_id' => $testPlan->suite_id,
            'conformance_level' => $testPlan->level,
            'role' => $testPlan->role,
            'status' => 'Verified',
            'has_exclusions' => $testPlan->hasExclusions(),
        ]);

        $pdfString = $claim->generatePDF();

        Storage::put('claims/products/' . $claim->id . '.pdf', $pdfString);

        $claim->sendNewClaimNotification();

        $testPlan->is_claimed = true;
        $testPlan->save();

        addMessage('The plan was certified successfully');
        return redirect('test-suite-coverage');
    }
}
