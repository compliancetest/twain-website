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
        // Load wordpress bootstrap file
        if(!App::runningInConsole()) {
            require __DIR__ . '/../../../wp-load.php';
        }
    }

}
