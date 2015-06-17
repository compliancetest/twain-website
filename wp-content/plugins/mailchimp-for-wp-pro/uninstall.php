<?php

//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) { exit(); }

// define content dir if not defined
if (!defined('WP_CONTENT_DIR')) { define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' ); }

// delete options
$option_names = array('mc4wp', 'mc4wp_checkbox', 'mc4wp_form', 'mc4wp_form_css');
foreach($option_names as $option_name) {
	delete_option($option_name);
}

// delete transients
delete_transient('mc4wp_mailchimp_lists');
delete_transient('mc4wp_mailchimp_lists_fallback');
delete_transient('mc4wp_list_counts');
delete_transient('mc4wp_list_counts_fallback');

// delete custom tables
global $wpdb;
$table_name = $wpdb->prefix . 'mc4wp_log';
$wpdb->query("DROP TABLE IF EXISTS {$table_name}");

// delete custom stylesheet
if(file_exists(WP_CONTENT_DIR . '/mc4wp-custom-styles.css')) {
	unlink(WP_CONTENT_DIR . '/mc4wp-custom-styles.css');
}
