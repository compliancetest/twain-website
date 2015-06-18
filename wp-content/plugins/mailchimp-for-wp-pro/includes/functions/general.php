<?php

if( ! defined( 'MC4WP_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * Gets the MailChimp for WP options from the database
 * Uses default values to prevent undefined index notices.
 *
 * @param string $key
 * @return array
 */
function mc4wp_get_options( $key = '' ) {

	static $options = null;

	if( null === $options) {
		$defaults = include MC4WP_PLUGIN_DIR . '/includes/config/default-options.php';

		$keys_map = array(
			'mc4wp' => 'general',
			'mc4wp_checkbox' => 'checkbox',
			'mc4wp_form' => 'form'
		);

		$options = array();

		foreach ( $keys_map as $db_key => $opt_key ) {

			$option = (array) get_option( $db_key, array() );

			// add option to database to prevent query on every pageload
			if ( count( $option ) === 0 ) {
				add_option( $db_key, $defaults[$opt_key] );
			}

			$options[$opt_key] = array_merge( $defaults[$opt_key], $option );
		}

	}

	if( '' !== $key ) {
		return $options[$key];
	}

	return $options;
}

/**
 * Gets the MailChimp for WP API class and injects it with the given API key
 *
 * @return MC4WP_API
 */
function mc4wp_get_api() {
	return MC4WP::instance()->get_api();
}