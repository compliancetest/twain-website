<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class WordpressServiceProvider extends ServiceProvider
{


    public function boot() {
        // Load assets
//        wp_enqueue_style('app', '/app/public/app.css');
    }

    public function register() {
        //loading wordpress files only for web requests
        //we dont need wordpress for php artisan requests
        if(!App::runningInConsole()) {
            // Load wordpress bootstrap file
            $GLOBALS['loadFromLaravel'] = 'yes';
            require __DIR__ . '/../../../wp-load.php';
        }
    }

}
