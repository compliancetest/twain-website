<?php

namespace App\Policies;

use App\User;
use App\Community;
use App\LaravelTestSuite;
use Illuminate\Auth\Access\HandlesAuthorization;

class TestSuitePolicy
{
    use HandlesAuthorization;

    /**
     * Ensure that user can edit test suite
     * @param User $user
     * @param LaravelTestSuite $testSuite
     * @return bool
     */
    public function changeTestSuite(User $user, LaravelTestSuite $testSuite = null, $communityId = false)
    {
        if(!$communityId){
            $communityId = $testSuite->community_id;
        }
        return (boolean) Community::find($communityId)->members()->where(['user_id' => $user->ID, 'is_admin' => true])->first();
    }

    /**
     * Check that user can view Test Suite
     * @param User $user
     * @param LaravelTestSuite $testSuite
     * @return bool
     */
    public function viewTestSuite(User $user, LaravelTestSuite $testSuite)
    {
        $community = Community::find($testSuite->community_id);
        if ($community->visibility_status == 'private') {
            if ($testSuite->status == 'Active' || $community->isAdmin()) {
                return true;
            }
            if (($community->isModerator() || $community->getMember($user->ID)) && in_array($testSuite->status, ['Active', 'Partial', 'Obsolete', 'Deprecated'])) {
                return true;
            }
        }
        if ($community->visibility_status == 'public') {
            if ($testSuite->status == 'Active' || $community->isAdmin() || in_array($testSuite->status, ['Active', 'Partial', 'Obsolete', 'Deprecated'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check that user can subscribe to Test Suite
     * @param User $user
     * @param LaravelTestSuite $testSuite
     * @return bool
     */
    public function subscribeToTestSuite(User $user, LaravelTestSuite $testSuite)
    {
        if ($testSuite->status == 'Active') {
            return true;
        }
        return false;
    }

    /**
     * Wordpress super admin can view / edit all Test Suites
     * @param $user
     * @param $ability
     * @return bool
     */
    public function before($user, $ability)
    {
        if (is_super_admin()) {
            return true;
        }
    }
}
