<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class VerifyRequest extends Model
{
    use UuidTrait;

    public $incrementing = false;

    protected $fillable = [
        'test_plan_id', 'requestor_id', 'transactions', 'assignee_id',
        'product_id', 'test_suite_id', 'community_id'
    ];

    /**
     * Get list of requests for user. Requests are grouped by test suites
     * @return array
     */
    public function getUserRequests()
    {
        $result = [];
        $user = Auth::user();
        $userTestSuites = $user->suiteSubscriptions;
        $userCommunities = $user->subscriptions;
        foreach ($userTestSuites as $userTestSuite) {
            if (!isset($result[$userTestSuite->suite_family_mark])) {
                $result[$userTestSuite->suite_family_mark] = [
                    'testSuite' => Post::find($userTestSuite->suite_family_mark),
                    'data' => [],
                ];
            }
            foreach ($userCommunities as $userCommunity) {
                if ($userCommunity->is_admin || $userCommunity->is_mod) {
                    $requests = VerifyRequest::where([
                        'community_id' => $userCommunity->community_id,
                        'test_suite_id' => $userTestSuite->suite_family_mark,
                    ])->get();
                } else {
                    $requests = VerifyRequest::where([
                        'community_id' => $userCommunity->community_id,
                        'test_suite_id' => $userTestSuite->suite_family_mark,
                        'requestor_id' => $user->ID,
                    ])->get();
                }
                if (is_object($requests) && !$requests->isEmpty()) {
                    foreach ($requests as $request) {
                        $result[$userTestSuite->suite_family_mark]['data'][] = [
                            'verifyRequest' => $request,
                            'product' => Post::find($request->product_id),
                            'testPlan' => TestPlan::find($request->test_plan_id),
                            'testCases' => Transaction::find(json_decode($request->transactions, true))->map(function($item, $key){
                                return Post::find($item->test_case_id);
                            }),
                        ];
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Ensure that Verify Request can be deleted by user
     * @return bool
     */
    public function canUserDelete()
    {
        $user = Auth::user();
        $community = Community::find($this->community_id);
        if ($user->ID != $this->requestor_id && !($community->isAdmin() || $community->isModerator())) {
            return false;
        }
        return true;
    }
}
