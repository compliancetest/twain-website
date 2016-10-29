<?php

namespace App\Providers;

use App\LaravelTestCase;
use App\LaravelTestSuite;
use App\Policies\TestCasePolicy;
use App\Policies\TestSuitePolicy;
use App\Product;
use App\Policies\ProductPolicy;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
        Product::class => ProductPolicy::class,
        LaravelTestSuite::class => TestSuitePolicy::class,
        LaravelTestCase::class => TestCasePolicy::class,
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate $gate
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);

        //
    }
}
