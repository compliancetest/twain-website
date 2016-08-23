<?php

if(strpos($_SERVER['REQUEST_URI'], '?') !== false){
   $segments = (isset($_SERVER['REQUEST_URI']) ? explode('/', trim(explode('?', $_SERVER['REQUEST_URI'])[0], '/')) : array('/'));
} else {
    $segments = (isset($_SERVER['REQUEST_URI']) ? explode('/', trim($_SERVER['REQUEST_URI'], '/')) : array('/'));
}

$urls = ['communities', 'membership', 'downloads', 'sso', 'api', 'testingdetails', 'articles', 'communityprofiles', 'profiletypes', 'forums',
    'communitysurveys', 'test-suite-coverage', 'testplan', 'transactions', 'verify-requests', 'my-transaction-log'];

if ( in_array($segments[0], $urls) || strpos($segments[0], 'communities?') === 0) {
    require_once __DIR__ . '/laravel/public/index.php';
    exit;
}
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
define('WP_USE_THEMES', true);

/** Loads the WordPress Environment and Template */
require( dirname( __FILE__ ) . '/wp-blog-header.php' );
