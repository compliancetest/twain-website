<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class CheckRoleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     * Injecting isAnyCommunityAdmin / isAnyCommunitySupport variables to all views
     *
     * @return void
     */
    public function boot()
    {
        $isAnyCommunityAdmin = $isAnyCommunitySupport = $supportOrAdmin = false;
        $user = Auth::user();
        if ($user) {
            $isAnyCommunityAdmin = doesUserAdminInAnyCommunity($user->ID);
            $isAnyCommunitySupport = doesUserSupportInAnyCommunity($user->ID);
            $supportOrAdmin = $isAnyCommunityAdmin || $isAnyCommunitySupport;
        }
        view()->share('isAnyCommunityAdmin', $isAnyCommunityAdmin);
        view()->share('isAnyCommunitySupport', $isAnyCommunitySupport);
        view()->share('supportOrAdmin', $supportOrAdmin);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
