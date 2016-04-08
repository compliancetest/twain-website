<?php

namespace App\Providers;

use App\WordpressHasher;
use Illuminate\Hashing\HashServiceProvider;

class WordpressHashServiceProvider extends HashServiceProvider
{

    public function register()
    {
        $this->app->singleton('hash', function () {
            return new WordpressHasher();
        });
    }

}