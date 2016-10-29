<?php

namespace App\Policies;

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
    public function change(User $user, LaravelTestCase $testCase, LaravelTestSuite $testSuite)
    {
        return (boolean) Community::find($testSuite->community_id)->members()->where(['user_id' => $user->ID, 'is_admin' => true])->first();
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

    /**
     * Wordpress super admin can view / edit all products
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
