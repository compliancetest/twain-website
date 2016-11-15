<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'transactions/*',
        'search-results/delete-all-site-data',
        'search-results/upload-all-site-data',
        'products-and-services/delete-all-registry-data',
        'products-and-services/upload-all-registry-data',
    ];
}
