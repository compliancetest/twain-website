<?php

namespace App\Http\Controllers;

use App\Community;
use App\Post;
use App\TestOutcomeStatus;
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
         $userID = Auth::user()->ID;
         $data = [
            'userSuites' => VerifyRequest::getUserRequests(),
            'pageTitle' => 'Verify Requests',
            'isAdmin' => doesUserSupportInAnyCommunity($userID) || doesUserAdminInAnyCommunity($userID),
        ];
        return view('pages.my.verify_requests.index')->with($data);
    }

    /**
     * Render VerifyRequests form for Support user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateList(Request $request)
    {
        $userID = Auth::user()->ID;
        $data = [
            'userSuites' => VerifyRequest::getUserRequests($request->get('hideResolved'), $request->get('hideOthers')),
            'isAdmin' => doesUserSupportInAnyCommunity($userID) || doesUserAdminInAnyCommunity($userID),
        ];
        return response()->json(['html' => view('pages.my.verify_requests.list')->with($data)->render()]);
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
            $testPlans = TestPlan::where(['product_id' => $productId, 'suite_id' => $testSuiteId])
                ->whereNotIn('id', VerifyRequest::where(['test_suite_id' => $testSuiteId, 'product_id' => $productId])->get()
                ->pluck('test_plan_id'))->get();
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
            return response()->json($validator->messages(), 422);
        }

        $testPlan = TestPlan::find($request->get('test_plan_id'));
        $verifyRequest = VerifyRequest::create([
            'test_plan_id' => $testPlan->id,
            'community_id' => Post::find($request->get('suite_id'))->getMetaByKey('community_id'),
            'requestor_id' => Auth::user()->ID,
            'product_id' => $request->get('product_id'),
            'test_suite_id' => $request->get('suite_id'),
            'transactions' => json_encode($request->get('transactions')),
            'organisation_id' => Auth::user()->organisation[0]['id'],
        ]);

        $verifyRequest->sendVerifyRequestNotification('new_verify_request');

        $userSuites = VerifyRequest::getUserRequests();
        return response()->json(['html' => view('pages.my.verify_requests.list', compact('userSuites'))->render()]);
    }

    /**
     * Delete Verify Request entry
     * @param $verifyRequestId
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($verifyRequestId)
    {
        $verifyRequest = VerifyRequest::find($verifyRequestId);
        if (!$verifyRequest->canUserDelete()) {
            return response()->json(['messages' => "You can't delete this Verify Request"], 422);
        }
        $verifyRequest->delete();
        return response()->json(['status' => 'success']);
    }

    /**
     * Render assign moderator popup
     * @param $testSuiteId
     * @param $verifyRequestId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function assignPopup($testSuiteId, $verifyRequestId)
    {
        $communityId = Post::find($testSuiteId)->getMetaByKey('community_id');
        $moderators = Community::find($communityId)->getAdminsAndModerators();
        $verifyRequest = VerifyRequest::find($verifyRequestId);

        return view('pages.my.verify_requests.assign_popup', compact('moderators', 'testSuiteId', 'verifyRequest'));
    }

    /**
     * Save reassignment
     * @param $testSuiteId
     * @param $verifyRequestId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     * @throws \Throwable
     */
    public function assign($testSuiteId, $verifyRequestId, Request $request)
    {
        $verifyRequest = VerifyRequest::find($verifyRequestId);
        if ($verifyRequest->status == 'Resolved') {
            return response()->json(["You can't reassign resolved Verify Request"], 422);
        }
        if ($verifyRequest->status == 'New') {
            $verifyRequest->status = 'Assigned';
        }
        $verifyRequest->assignee_id = $request->get('user_id');
        $verifyRequest->save();

        $verifyRequest->sendVerifyRequestNotification('assigned_verify_request');

        $userID = Auth::user()->ID;
        $data = [
            'userSuites' => VerifyRequest::getUserRequests($request->get('hideResolved'), $request->get('hideOthers')),
            'isAdmin' => doesUserSupportInAnyCommunity($userID) || doesUserAdminInAnyCommunity($userID),
        ];
        return response()->json(['html' => view('pages.my.verify_requests.list')->with($data)->render()]);
    }

     /**
     * Render accept Verift Request popup
     * @param $testSuiteId
     * @param $verifyRequestId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function acceptPopup($testSuiteId, $verifyRequestId)
    {
        $verifyRequest = VerifyRequest::find($verifyRequestId);

        return view('pages.my.verify_requests.accept_popup', compact('testSuiteId', 'verifyRequest'));
    }

    /**
     * Save accept
     * @param $testSuiteId
     * @param $verifyRequestId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     * @throws \Throwable
     */
    public function accept($testSuiteId, $verifyRequestId, Request $request)
    {
        $verifyRequest = VerifyRequest::find($verifyRequestId);
        if ($verifyRequest->status == 'Resolved') {
            return response()->json(["You can't reassign resolved Verify Request"], 422);
        }

        $userID = Auth::user()->ID;

        $requestAlreadyAssigned = $verifyRequest->assignee_id ? true : false;

        $verifyRequest->assignee_id = $userID;
        $verifyRequest->status = 'In Progress';
        $verifyRequest->is_accepted = true;
        $verifyRequest->save();

        if ($requestAlreadyAssigned) {
            $verifyRequest->sendVerifyRequestNotification('assigned_accepted_verify_request');
        } else {
            $verifyRequest->sendVerifyRequestNotification('accepted_verify_request');
        }

        $data = [
            'userSuites' => VerifyRequest::getUserRequests($request->get('hideResolved'), $request->get('hideOthers')),
            'isAdmin' => doesUserSupportInAnyCommunity($userID) || doesUserAdminInAnyCommunity($userID),
        ];
        return response()->json(['html' => view('pages.my.verify_requests.list')->with($data)->render()]);
    }

    /**
     * Render unassign Verift Request popup
     * @param $testSuiteId
     * @param $verifyRequestId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function unassignPopup($testSuiteId, $verifyRequestId)
    {
        $verifyRequest = VerifyRequest::find($verifyRequestId);
        return view('pages.my.verify_requests.unassign_popup', compact('testSuiteId', 'verifyRequest'));
    }

     /**
     * Save unassign
     * @param $testSuiteId
     * @param $verifyRequestId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     * @throws \Throwable
     */
    public function unassign($testSuiteId, $verifyRequestId, Request $request)
    {
        $verifyRequest = VerifyRequest::find($verifyRequestId);
        if ($verifyRequest->status == 'Resolved') {
            return response()->json(["You can't reassign resolved Verify Request"], 422);
        }

        $userID = Auth::user()->ID;

        if ($verifyRequest->is_accepted) {
            $verifyRequest->sendVerifyRequestNotification('accepted_unasigned_verify_request');
        } else {
            $verifyRequest->sendVerifyRequestNotification('assigned_unassigned_verify_request');
        }

        $verifyRequest->assignee_id = 0;
        $verifyRequest->status = 'New';
        $verifyRequest->is_accepted = false;
        $verifyRequest->save();

        $data = [
            'userSuites' => VerifyRequest::getUserRequests($request->get('hideResolved'), $request->get('hideOthers')),
            'isAdmin' => doesUserSupportInAnyCommunity($userID) || doesUserAdminInAnyCommunity($userID),
        ];
        return response()->json(['html' => view('pages.my.verify_requests.list')->with($data)->render()]);
    }

    /**
     * Render Resolve VerifyRequest
     * @param $testSuiteId
     * @param $verifyRequestId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function resolvePopup($testSuiteId, $verifyRequestId)
    {
        $verifyRequest = VerifyRequest::find($verifyRequestId);
        $communityId = Post::find($testSuiteId)->getMetaByKey('community_id');
        $transactions = Transaction::find(json_decode($verifyRequest->transactions, true));
        return view('pages.my.verify_requests.resolve_popup', compact('transactions', 'testSuiteId', 'verifyRequest', 'communityId'));
    }

    /**
     * Confirm resolving Verify Request. Transactions included to VerifyRequest will
     * have 'PASS' status and VerifyRequest status will be changed to 'Resolved'
     * @param $communityId
     * @param $verifyRequestId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resolve($communityId, $verifyRequestId, Request $request)
    {
        $community = Community::find($communityId);
        if (!$community->isModerator() && !$community->isAdmin()) {
            return response()->json(["Permissions error"], 422);
        }

        $verifyRequest = VerifyRequest::find($verifyRequestId);

        if (!$verifyRequest->canBeResolved(Auth::user())) {
            return response()->json(["VerifyRequest could be resolved only by assignee user if it doesn't contain Pending transactions"], 422);
        }

        $verifyRequest->status = 'Resolved';
        $verifyRequest->save();

        $verifyRequest->sendVerifyRequestNotification('resolved_verify_request');

        $userID = Auth::user()->ID;
        $data = [
            'userSuites' => VerifyRequest::getUserRequests($request->get('hideResolved'), $request->get('hideOthers')),
            'isAdmin' => doesUserSupportInAnyCommunity($userID) || doesUserAdminInAnyCommunity($userID),
        ];
        return response()->json(['html' => view('pages.my.verify_requests.list')->with($data)->render()]);
    }

    /**
     * Update transactions from VerifyRequest
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateTransactions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'verify_request_id' => 'required|exists:verify_requests,id',
            'transactions' => 'array|required',
            'outcome_code' => 'required|in:Pass,Fail,Skip',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }
        $verifyRequest = VerifyRequest::find($request->get('verify_request_id'));
        if ($verifyRequest->assignee_id != Auth::user()->ID) {
            return response()->json(["Only assignee can edit Verify Request"], 422);
        }
        $verifyRequestTransactions = json_decode($verifyRequest->transactions, true);
        foreach ($verifyRequestTransactions as $verifyRequestTransaction) {
            if (in_array($verifyRequestTransaction, $request->get('transactions'))) {
                $transaction = Transaction::find($verifyRequestTransaction);
                $transaction->test_outcome_status_id = TestOutcomeStatus::getIdByCode(strtoupper($request->get('outcome_code')));
                if ($request->get('reason')) {
                    $transaction->reason = $request->get('reason');
                }
                $transaction->save();
            }
        }
        $userID = Auth::user()->ID;
        $data = [
            'userSuites' => VerifyRequest::getUserRequests($request->get('hideResolved'), $request->get('hideOthers')),
            'isAdmin' => doesUserSupportInAnyCommunity($userID) || doesUserAdminInAnyCommunity($userID),
        ];
        return response()->json(['html' => view('pages.my.verify_requests.list')->with($data)->render()]);
    }
}
