<?php

namespace App\Providers;

use App\Community;
use App\CommunityDownloads;
use App\CommunityMembers;
use App\CommunityMeta;
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
        Community::creating(function ($entry) {
            $entry->id = \Webpatser\Uuid\Uuid::generate(4);;
        });
        CommunityMembers::creating(function ($entry) {
            $entry->id = \Webpatser\Uuid\Uuid::generate(4);;
        });
        CommunityMeta::creating(function ($entry) {
            $entry->id = \Webpatser\Uuid\Uuid::generate(4);;
        });

        CommunityDownloads::creating(function ($entry) {
            $entry->id = \Webpatser\Uuid\Uuid::generate(4);;
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
