<?php

namespace App\Policies;

use App\Community;
use App\LaravelTestSuite;
use App\User;
use App\LaravelTestCase;
use Illuminate\Auth\Access\HandlesAuthorization;

class TestCasePolicy
{
    use HandlesAuthorization;

    /**
     * Ensure that user can edit test case
     * @param User $user
     * @param LaravelTestCase $testCase
     * @param LaravelTestSuite $testSuite
     * @return bool
     */
    public function changeTestCase(User $user, LaravelTestCase $testCase)
    {
        $community = Community::find($testCase->community_id);
        if (!$community) {
            return false;
        }
        return (boolean) is_super_admin() || $community->members()->where(['user_id' => $user->ID, 'is_admin' => true])->first();
    }

    /**
     * Check that user can view test case
     * @param User $user
     * @param LaravelTestCase $testCase
     * @return bool
     */
    public function view(User $user, LaravelTestCase $testCase)
    {
        return true;
    }
}
