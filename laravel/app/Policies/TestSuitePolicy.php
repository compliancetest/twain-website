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
        if ($testSuite->status == 'Active') {
            return true;
        }
        if ($community->isAdmin()) {
            return true;
        }
        if (($community->isModerator() || $community->getMember($user)) && in_array($testSuite->status, ['Active', 'Partial'])) {
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
