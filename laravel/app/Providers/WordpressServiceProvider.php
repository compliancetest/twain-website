<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class WordpressServiceProvider extends ServiceProvider
{


    public function boot() {
        if(function_exists('get_current_user_id') && get_current_user_id()){
            Auth::loginUsingId(get_current_user_id());
        } else {
            Auth::logout();
        }
    }

    public function register() {
        //loading wordpress files only for web requests
        //we dont need wordpress for php artisan requests
        if(!App::runningInConsole()) {
            // Load wordpress bootstrap file
            $GLOBALS['loadFromLaravel'] = 'yes';
            require_once __DIR__ . '/../../../wp-load.php';
        }
    }

}
