<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class WordpressServiceProvider extends ServiceProvider
{


    public function boot() {
        // Load assets
//        wp_enqueue_style('app', '/app/public/app.css');
    }

    public function register() {
        // Load wordpress bootstrap file
        $wordpressBootFilePath = __DIR__ . '/../../../wp-load.php';
        if(file_exists($wordpressBootFilePath)) {
            require_once $wordpressBootFilePath;
        } else throw new \RuntimeException('WordPress Bootstrap file not found!');
    }

}
