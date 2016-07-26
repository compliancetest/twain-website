<?php

namespace App\Http\Controllers;

use App\Post;
use App\TestPlan;
use App\Transaction;
use App\VerifyRequest;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Validator;

class VerifyRequestsController extends Controller
{
    /**
     * Render list of requests for user
     * @return $this
     */
    public function index()
    {
         $data = [
            'userSuites' => VerifyRequest::getUserRequests(),
            'pageTitle' => 'Verify Requests',
        ];
        return view('pages.my.verify_requests.index')->with($data);
    }

    /**
     * Render create verify request popup
     * @param $testSuiteId
     * @param bool $productId
     * @param bool $testPlanId
     * @return $this
     */
    public function create($testSuiteId, $productId = false, $testPlanId = false)
    {
        $testSuite = Post::find($testSuiteId);
        $products = $testSuite->getProductsForNewVerifyRequest();
        $transactions = $testPlans = [];
        if($productId){
            $testPlans = TestPlan::where(['product_id' => $productId, 'suite_id' => $testSuiteId])->get();
        }
        if($testPlanId){
            $transactions = Transaction::getTransactionsForVerifyRequest($productId, $testSuiteId);
        }
        $data = [
            'testSuiteId' => $testSuiteId,
            'products' => $products,
            'selectedProductId' => $productId,
            'testPlans' => $testPlans,
            'selectedTestPlanId' => $testPlanId,
            'transactions' => $transactions,

        ];
        return view('pages.my.verify_requests.create')->with($data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'suite_id' => 'required|exists:wp_posts,ID',
            'product_id' => 'required|exists:wp_posts,ID',
            'test_plan_id' => 'required|exists:test_plans,id',
            'transactions' => 'required|array',
        ]);

        if ($validator->fails()) {
            return $this->respondUnprocessableEntity($validator->messages());
        }

        $testPlan = TestPlan::find($request->get('test_plan_id'));
        VerifyRequest::create([
            'test_plan_id' => $testPlan->id,
            'requestor_id' => Auth::user()->ID,
            'product_id' => $request->get('product_id'),
            'test_suite_id' => $request->get('suite_id'),
            'transactions' => json_encode($request->get('transactions'))
        ]);

        $userSuites = Auth::user()->suiteSubscriptions;
        return response()->json(['html' => view('pages.my.verify_requests.list', compact('userSuites'))->render()]);
    }
}
