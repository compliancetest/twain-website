<?php

namespace App\Http\Controllers;

use App\OrganisationSubscription;
use App\Post;
use App\PricingPlan;
use App\TestPlan;
use App\TestPlanExcludedCases;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;

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

        $data = [
            'products' => Auth::user()->getProducts(),
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
        $testPlan = TestPlan::create($request->all());
        $testPlan->creator_id = Auth::user()->ID;

        $organisationSubscription = OrganisationSubscription::where(['user_id' => Auth::user()->ID, 'suite_family_mark' => $request->get('suite_id')])->first();
        $testPlan->organisation_subscription_id = $organisationSubscription->id;

        $testPlan->save();

        return JsonResponse::create(['status' => 'success']);
    }

    /**
     * Render edit test plan form
     * @param $testPlanId
     * @return mixed
     */
    public function edit($testPlanId)
    {
        $testPlan = TestPlan::find($testPlanId);
        $subscription = OrganisationSubscription::where(['user_id' => Auth::user()->ID])->first();
        $pricingPlan = PricingPlan::where(['id' => $subscription->pricing_plan_id])->with('attributes')->first();
        $attributes = $pricingPlan->attributes->keyBy('type')->get('role');

        $data = [
            'products' => Auth::user()->getProducts(),
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
        return JsonResponse::create(['status' => 'success']);
    }


    public function update($testPlanId, Requests\TestPlanRequest $request)
    {
        $testPlan = TestPlan::find($testPlanId);
        if (OrganisationSubscription::find($testPlan->organisation_subscription_id)->organisation_id ==
            OrganisationSubscription::where(['user_id' => Auth::user()->ID, 'suite_family_mark' => $testPlan->suite_id])->first()->organisation_id
        ) {
            $testPlan = $testPlan->fill($request->all());
            $testPlan->save();
             return JsonResponse::create(['status' => 'success']);
        }
        return JsonResponse::create(['status' => 'Forbidden!'], 403);
    }

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
}
