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
        'product_id', 'suite_minor_family_mark', 'community_id', 'organisation_id'
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

            $isAdministrator = $community->isModerator() || $community->isAdmin() ;

            //Community Support users can see all community suites
            if ($isAdministrator) {
                $userTestSuites = $community->getCommunityTestSuites();
            } else {
                $userTestSuites = $user->suiteSubscriptions()->where(['status' => 'Active'])->get();
            }
            foreach ($userTestSuites as $userTestSuite) {
                if ($isAdministrator) {
                    $minorFamilyMark = $userTestSuite->minor_family_mark;
                } else {
                    $minorFamilyMark = $userTestSuite->suite_minor_family_mark;
                }
                if (!isset($result[$minorFamilyMark])) {
                    $result[$minorFamilyMark] = [
                        'testSuite' => LaravelTestSuite::getLatestSuiteForMinorFamilyMark($minorFamilyMark),
                        'data' => [],
                    ];
                }

                if ($userCommunity->is_admin || $userCommunity->is_mod) {
                    $query = VerifyRequest::where([
                        'community_id' => $userCommunity->community_id,
                        'suite_minor_family_mark' => $minorFamilyMark
                    ]);
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
                        'suite_minor_family_mark' => $minorFamilyMark
                    ])->get();
                }

                if (is_object($requests) && !$requests->isEmpty()) {
                    foreach ($requests as $request) {
                        $testCases = Transaction::find(json_decode($request->transactions, true))->map(function ($item, $key) {
                            return LaravelTestCase::find($item->test_case_id);
                        });
                        $result[$minorFamilyMark]['data'][] = [
                            'verifyRequest' => $request,
                            'requestor' => User::find($request->requestor_id),
                            'assignee' => $request->assignee_id ? User::find($request->assignee_id) : false,
                            'product' => Product::find($request->product_id),
                            'testPlan' => TestPlan::find($request->test_plan_id),
                            'testCases' => $testCases->sortBy(function($item){
                                return $item->full_name;
                            }),
                            'transactions' => Transaction::find(json_decode($request->transactions, true))->sortBy(function ($item, $key) {
                                return LaravelTestCase::find($item->test_case_id)->full_name;
                            })
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
     * Check that transaction ID is used in any verify request
     * @param $transactionId
     * @return mixed
     */
    public static function doesTrunsactionUsedInVerifyRequests($transactionId)
    {
        return self::where('transactions', 'LIKE', '%'.$transactionId.'%')->get();
    }

    /**
     * Send email notification about VerifyRequest action (add / assign / resolve)
     */
    public function sendVerifyRequestNotification($emailtemplateName)
    {
        $testSuite = LaravelTestSuite::getLatestSuiteForMinorFamilyMark($this->suite_minor_family_mark);
        $community = Community::find($testSuite->community_id);
        $testPlan = TestPlan::find($this->test_plan_id);
        $product = Product::find($this->product_id);
        $data = [
            '[requestor_name]' => cp_get_user_fullname($this->requestor_id),
            '[assignee_name]' => cp_get_user_fullname($this->assignee_id),
            '[verify_request_id]' => $this->id,
            '[website_url]' => getSiteUrl(),
            '[community]' => $community->title,
            '[test_suite]' => $testSuite->full_name,
            '[level]' => $testPlan->level,
            '[product]' => $product->full_name,
        ];
        $community->sendEmailsToSupportUsers( $emailtemplateName . '_to_support', $data);
        $community->sendEmailsToAdminUsers( $emailtemplateName . '_to_support', $data);

        $requestorUser = User::find($this->requestor_id);
        cp_send_email(['name' => cp_get_user_fullname($requestorUser->ID), 'email' => $requestorUser->user_email], $emailtemplateName . '_to_user', $data);
    }
}
