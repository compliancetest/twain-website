<?php

namespace App\Providers;

use App\Claim;
use App\ClaimObserver;
use App\Community;
use App\CommunityDownloads;
use App\CommunityMembers;
use App\CommunityMeta;
use App\LaravelTestCase;
use App\LaravelTestSuite;
use App\Post;
use App\PostObserver;
use App\TestCaseObserver;
use App\TestPlan;
use App\TestPlanObserver;
use App\TestSuiteObserver;
use App\Transaction;
use App\TransactionObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        TestPlan::observe(TestPlanObserver::class);
        Claim::observe(ClaimObserver::class);
        Post::observe(PostObserver::class);
        Transaction::observe(TransactionObserver::class);

        LaravelTestSuite::observe(TestSuiteObserver::class);
        LaravelTestCase::observe(TestCaseObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
