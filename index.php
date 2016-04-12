<?php

$segments = ( isset($_SERVER['REQUEST_URI']) ? explode('/', trim($_SERVER['REQUEST_URI'],'/')) : array('/') );

$urls = ['api', 'testingdetails'];

if ( in_array($segments[0], $urls) !== false ) {
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
