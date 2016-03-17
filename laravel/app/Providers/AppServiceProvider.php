<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Dingo\Api\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        app('Dingo\Api\Auth\Auth')->extend('basic', function ($app) {
            return new \Dingo\Api\Auth\Provider\Basic($app['auth'], 'user_email');
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
