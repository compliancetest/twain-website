<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
        ],

        'api' => [
            'throttle:60,1',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'community.admin' => \App\Http\Middleware\CommunityAdmin::class,
        'community.mod' => \App\Http\Middleware\CommunitySupport::class,
        'community.user' => \App\Http\Middleware\CommunityUser::class,
        'api.logs' => \App\Http\Middleware\LogAfterRequest::class,

        'simpleauth' => \App\Http\Middleware\SimpleAuth::class,
        'organisation.member' => \App\Http\Middleware\OrganisationMember::class,
        'organisation.subscriptions.product_type' => \App\Http\Middleware\DoesUserHasSubscriptionToProductType::class,
        'post.product.exist' => \App\Http\Middleware\PostExists::class,
    ];
}
