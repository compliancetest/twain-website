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
        'product_id', 'test_suite_id', 'community_id', 'organisation_id'
    ];

    /**
     * Get list of requests for user. Requests are grouped by test suites
     * @return array
     */
    public function getUserRequests($hideResolved = true, $hideOthers = true)
    {
        $result = [];
        $user = Auth::user();

        $userCommunities = $user->subscriptions;
        foreach ($userCommunities as $userCommunity) {
            $community = Community::find($userCommunity->community_id);
            //Community Support users can see all community suites
            if ($community->isModerator() || $community->isAdmin()) {
                $userTestSuites = Post::getCommunityTestSuites($community->id);
                array_walk($userTestSuites, function ($entry, $key) use ($userTestSuites) {
                    $userTestSuites[$key]->suite_family_mark = TestSuite::find($entry->ID)->family_mark;
                });

                //get only last versions
                $tempTestSuites = [];
                foreach($userTestSuites as $userTestSuite){
                    $tempTestSuites[$userTestSuite->suite_family_mark] = $userTestSuite;
                }
                $userTestSuites = $tempTestSuites;
            } else {
                $userTestSuites = $user->suiteSubscriptions()->where(['status' => 'Active'])->get();
            }

            foreach ($userTestSuites as $userTestSuite) {
                if (!isset($result[$userTestSuite->suite_family_mark])) {
                    $result[$userTestSuite->suite_family_mark] = [
                        'testSuite' => Post::find(TestSuite::getLatestSuiteIdForFamilyMark($userTestSuite->suite_family_mark)),
                        'data' => [],
                    ];
                }

                if ($userCommunity->is_admin || $userCommunity->is_mod) {
                    $query = VerifyRequest::where([
                        'community_id' => $userCommunity->community_id
                    ])->whereIn('test_suite_id',TestSuite::getFamilyMarkSuitesIds($userTestSuite->suite_family_mark));
                    if ($hideResolved) {
                        $query->where('status', '<>', 'Resolved');
                    }
                    if ($hideOthers) {
                        $query->whereIn('assignee_id', [0, $user->ID]);
                    }
                    $requests = $query->get();
                } else {
                    $requests = VerifyRequest::where([
                        'community_id' => $userCommunity->community_id,
                        'organisation_id' => Auth::user()->organisation[0]['id'],
                    ])->whereIn('test_suite_id', TestSuite::getFamilyMarkSuitesIds($userTestSuite->suite_family_mark))->get();
                }
                if (is_object($requests) && !$requests->isEmpty()) {
                    foreach ($requests as $request) {
                        $result[$userTestSuite->suite_family_mark]['data'][] = [
                            'verifyRequest' => $request,
                            'requestor' => User::find($request->requestor_id),
                            'assignee' => $request->assignee_id ? User::find($request->assignee_id) : false,
                            'product' => Post::find($request->product_id),
                            'testPlan' => TestPlan::find($request->test_plan_id),
                            'testCases' => Transaction::find(json_decode($request->transactions, true))->map(function ($item, $key) {
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
        if ($user->ID != $this->requestor_id) {
            return false;
        }
        return true;
    }

    public function getTestCaseStatus($caseId)
    {
        return TestOutcomeStatus::find(Transaction::where(['test_case_id' => $caseId])->whereIn('id', json_decode($this->transactions, true))->first()->test_outcome_status_id)->name;
    }
    /**
     * Ensure that VerifyRequest can be resolved.
     * VerifyRequest could be resolved only by assignee user and if it doesn't
     * contain Pending transactions
     * @param User $user
     * @return bool
     */
    public function canBeResolved(User $user)
    {
        //only assignee user can resolve VerifyRequest
        if ($user->ID != $this->assignee_id || $this->status != 'In Progress') {
            return false;
        }
        $transactionIds = json_decode($this->transactions, true);
        $pendingTransactions = Transaction::whereIn('id', $transactionIds)->where('test_outcome_status_id', TestOutcomeStatus::getIdByCode('PENDING'))->get();
        if (!$pendingTransactions->isEmpty()) {
            return false;
        }
        return true;
    }

    /**
     * Send email notification about VerifyRequest action (add / assign / resolve)
     */
    public function sendVerifyRequestNotification($emailtemplateName)
    {
        $testSuite = Post::find($this->test_suite_id);
        $community = Community::find($testSuite->getMetaByKey('community_id'));
        $testPlan = TestPlan::find($this->test_plan_id);
        $product = Post::find($this->product_id);
        $data = [
            '[requestor_name]' => cp_get_user_fullname($this->requestor_id),
            '[assignee_name]' => cp_get_user_fullname($this->assignee_id),
            '[verify_request_id]' => $this->id,
            '[website_url]' => getSiteUrl(),
            '[community]' => $community->title,
            '[test_suite]' => $testSuite->post_title,
            '[level]' => $testPlan->level,
            '[product]' => $product->post_title,
        ];
        $community->sendEmailsToSupportUsers( $emailtemplateName . '_to_support', $data);
        $community->sendEmailsToAdminUsers( $emailtemplateName . '_to_support', $data);

        $requestorUser = User::find($this->requestor_id);
        cp_send_email(['name' => cp_get_user_fullname($requestorUser->ID), 'email' => $requestorUser->user_email], $emailtemplateName . '_to_user', $data);
    }
}
