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
use App\Product;
use App\ProductObserver;
use App\TestCaseObserver;
use App\TestPlan;
use App\TestPlanObserver;
use App\TestSuiteObserver;
use App\Transaction;
use App\TransactionObserver;
use Illuminate\Support\ServiceProvider;
use Validator;

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
        Transaction::observe(TransactionObserver::class);

        LaravelTestSuite::observe(TestSuiteObserver::class);
        LaravelTestCase::observe(TestCaseObserver::class);
        Product::observe(ProductObserver::class);
        Post::observe(PostObserver::class);

        Validator::extend('phone_number', function ($attribute, $value, $parameters, $validator) {
            $value = str_replace(array('+', ' ', '(', ')', '-'), '', $value);
            return is_numeric($value);
        });

        Validator::replacer('phone_number', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute', $attribute, 'Phone number is invalid');
        });
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
