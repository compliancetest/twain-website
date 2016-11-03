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
    public function view(User $user, LaravelTestSuite $testSuite)
    {
        return true;
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
