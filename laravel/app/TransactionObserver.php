<?php

namespace App;

use Illuminate\Support\Facades\Auth;

class TransactionObserver
{
    /**
     * Listen to the Transaction saved event.
     * @param Transaction $transaction
     */
    public function saved(Transaction $transaction)
    {
        if ($transaction->test_outcome_status_id == TestOutcomeStatus::getIdByCode('SKIP')) {
            $organisationSubscriptions = OrganisationSubscription::where([
                'organisation_id' => User::find($transaction->customer_id)->organisation[0]->id
            ])->get();
            foreach ($organisationSubscriptions as $organisationSubscription) {
                $testPlans = TestPlan::where([
                    'is_claimed' => false,
                    'organisation_subscription_id' => $organisationSubscription->id,
                    'suite_minor_family_mark' => $organisationSubscription->suite_minor_family_mark
                ])->get();
                foreach ($testPlans as $testPlan) {
                    if ($transaction->audit_record) {
                         /**
                         * SKIP + audit transactions should be marked as excluded
                         */
                        foreach ($testPlan->getSkippedTransactions() as $skippedCase) {
                            $testPlan->excludedCases()->updateOrCreate(
                                [
                                    'test_case_id' => $skippedCase,
                                ],
                                [
                                    'reason' => 'Test execution was skipped.',
                                    'excluded_by_user_id' => Auth::user()->ID,
                                    'is_skipped' => true
                                ]
                            );
                        }
                    } else {
                         /**
                         * remove excluded flag when audit_record value was changed to false
                         */
                        $testPlan->excludedCases()->where(['test_case_id' => $transaction->test_case_id, 'is_skipped' => true])->delete();
                    }
                }
            }
        }
    }

    public function deleting(Transaction $transaction)
    {
        TransactionChangeLog::addLog($transaction, Auth::user() ? Auth::user()->ID : 0, 'DELETED', false, Auth::user() ? Auth::user()->ID : 0);
    }
}
