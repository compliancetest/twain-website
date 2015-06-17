<?php
/*
Plugin Name: MailChimp for WordPress Pro
Plugin URI: http://dannyvankooten.com/wordpress-plugins/mailchimp-for-wordpress/
Description: Complete MailChimp newsletter sign-up integration for WordPress.
Version: 1.92
Author: Danny van Kooten
Author URI: http://dannyvanKooten.com

MailChimp for WordPress
*/

defined( 'ABSPATH' ) OR exit;

// define some constant we need. probably already defined.
if (!defined('WP_CONTENT_DIR')) { define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' ); }
if (!defined('WP_CONTENT_URL') ) { define( 'WP_CONTENT_URL', site_url( 'wp-content') ); }

define('MC4WP_VERSION_NUMBER', "1.92");
define('MC4WP_ITEM_NAME', 'MailChimp for WordPress Pro');
define('MC4WP_PLUGIN_FILE', __FILE__);
define("MC4WP_PLUGIN_DIR", plugin_dir_path(__FILE__));
define('MC4WP_SHOP_URL', 'https://dannyvankooten.com/mailchimp-for-wordpress/');

 // frontend AND backend
include_once MC4WP_PLUGIN_DIR . 'includes/MC4WP.php';
new MC4WP();