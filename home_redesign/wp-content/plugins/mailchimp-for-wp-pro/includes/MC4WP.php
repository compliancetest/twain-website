<?php

class MC4WP {
	private $options = array();
	private static $api = null;
	private static $checkbox = null;
	private static $form = null;
	private static $instance = null;
	private static $log = null;
	private static $statistics = null;
	private static $admin = null;

	public static function instance() {
		return self::$instance;
	}

	public function __construct() {
		// store instance of self
		self::$instance = $this;

		// init checkbox
		self::checkbox();

		// init form
		self::form();

		// logging
		self::log();

		// init widget
		add_action( 'widgets_init', array( $this, 'register_widget' ) );

		// init logging, if enabled
		$opts = $this->get_options();

		if ( !defined( 'DOING_AJAX' ) || !DOING_AJAX ) {

			if ( is_admin() ) {
				// admin only
				self::admin();
			} else {
				// front-end only
				include_once MC4WP_PLUGIN_DIR . 'includes/template-functions.php';

				// load css
				add_action( 'wp_enqueue_scripts', array($this, 'load_stylesheets'), 90);
				add_action( 'login_enqueue_scripts',  array($this, 'load_stylesheets') );
			}

		}
	}

	public static function checkbox() {
		if ( !self::$checkbox ) {
			include_once MC4WP_PLUGIN_DIR . 'includes/MC4WP_Checkbox.php';
			self::$checkbox = new MC4WP_Checkbox();
		}

		return self::$checkbox;
	}

	public static function log() {
		if ( !self::$log ) {
			include_once MC4WP_PLUGIN_DIR . 'includes/MC4WP_Subscribers_Log.php';
			self::$log = new MC4WP_Subscribers_Log();
		}

		return self::$log;
	}

	public static function form() {
		if ( !self::$form ) {
			// load form functionality
			include_once MC4WP_PLUGIN_DIR . 'includes/MC4WP_Form.php';
			self::$form = new MC4WP_Form();
		}

		return self::$form;
	}

	public static function api() {
		if ( !self::$api ) {
			include_once MC4WP_PLUGIN_DIR . 'includes/MC4WP_API.php';
			$opts = self::$instance->get_option_group( 'general' );
			self::$api = new MC4WP_API( $opts['api_key'] );
		}

		return self::$api;
	}

	public static function statistics() {
		if ( !self::$statistics ) {
			include_once MC4WP_PLUGIN_DIR . 'includes/MC4WP_Statistics.php';
			self::$statistics = new MC4WP_Statistics();
		}

		return self::$statistics;
	}

	public static function admin() {
		if ( !self::$admin ) {
			include_once MC4WP_PLUGIN_DIR . 'includes/MC4WP_Admin.php';
			self::$admin = new MC4WP_Admin();
		}
		return self::$admin;
	}

	public function get_default_options() {
		$default_options = array();
		$default_options['general'] = array(
			'api_key' => '', 'license_key' => ''
		);
		$default_options['checkbox'] = array(
			'label' => 'Sign me up for the newsletter!', 'precheck' => 1, 'css' => 0,
			'show_at_comment_form' => 0, 'show_at_registration_form' => 0, 'show_at_multisite_form' => 0,
			'show_at_buddypress_form' => 0, 'show_at_other_forms' => 0, 'show_at_edd_checkout' => 0,
			'show_at_woocommerce_checkout' => 0, 'show_at_bbpress_forms' => 0,
			'lists' => array(), 'double_optin' => 1, 'send_welcome' => 0
		);
		$default_options['form'] = array(
			'css' => 0, 'custom_theme_color' => '#1af', 'ajax' => 1, 'double_optin' => 1, 'update_existing' => 0, 'replace_interests' => 1, 'send_welcome' => 0,
			'text_success' => 'Thank you, your sign-up request was successful! Please check your e-mail inbox.', 'text_error' => 'Oops. Something went wrong. Please try again later.',
			'text_invalid_email' => 'Please provide a valid email address.', 'text_already_subscribed' => "Given email address is already subscribed, thank you!",
			'redirect' => '', 'hide_after_success' => 0
		);
		return $default_options;
	}

	public function get_options( $group = null ) {
		if ( empty( $this->options ) ) {
			$default_options = $this->get_default_options();

			$option_keys_default_keys = array(
				'mc4wp' => 'general',
				'mc4wp_checkbox' => 'checkbox',
				'mc4wp_form' => 'form'
			);

			foreach ( $option_keys_default_keys as $option_key => $default_key ) {
				$option = get_option( $option_key );

				// add option to database to prevent query on every pageload
				if ( $option == false ) { add_option( $option_key, $default_options[$default_key] ); }

				$this->options[$default_key] = array_merge( $default_options[$default_key], (array) $option );
			}
		}

		if ( $group ) {
			return $this->options[$group];
		}

		return $this->options;
	}

	public function get_option_group( $group ) {
		return $this->get_options( $group );
	}

	public function has_valid_license() {
		$license_status = get_option( 'mc4wp_license_status' );
		return $license_status == 'valid' || $license_status == 'local';
	}

	public function register_widget() {
		include_once MC4WP_PLUGIN_DIR . 'includes/MC4WP_Widget.php';
		register_widget( 'MC4WP_Widget' );
	}

	public function load_stylesheets() 
	{
		$stylesheets = apply_filters('mc4wp_stylesheets', array());

		if(!empty($stylesheets)) {
			$stylesheet_url = add_query_arg($stylesheets, plugins_url('mailchimp-for-wp-pro/assets/css/css.php'));
			wp_enqueue_style( 'mailchimp-for-wp', $stylesheet_url, array(), MC4WP_VERSION_NUMBER);
		}
	}

}
