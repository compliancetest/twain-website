<?php

class MC4WP_Admin {
	private static $instance;
	private $options = array();
	private $connected_to_mailchimp = null;
	private $transfered_settings = false;

	public function __construct() {
		$this->options = MC4WP::instance()->get_options();

		register_activation_hook( 'mailchimp-for-wp-pro/mailchimp-for-wp-pro.php', array( $this, 'on_activation' ) );
		register_deactivation_hook( 'mailchimp-for-wp-pro/mailchimp-for-wp-pro.php', array( $this, 'on_deactivation' ) );

		add_action( 'do_meta_boxes', array( $this, 'remove_meta_boxes' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 25 );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_assets' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_init', array( $this, 'toggle_license_status' ) );
		add_action( 'admin_menu', array( $this, 'build_menu' ) );
		add_action( 'admin_notices', array( $this, 'show_admin_notice' ) );
		add_action( 'admin_notices', array( $this, 'show_notice_to_deactivate_lite' ) );
		add_action( 'init', array( $this, 'load_auto_updater' ) );
		add_action( 'save_post', array( $this, 'save_form_data' ) );

		add_filter( 'default_content', array( $this, 'get_default_form_markup' ), 10, 2 );
		add_filter( "plugin_action_links_mailchimp-for-wp-pro/mailchimp-for-wp-pro.php", array( $this, 'add_settings_link' ) );
		add_filter( 'post_updated_messages', array( $this, 'set_form_updated_messages' ) );
		add_filter( 'quicktags_settings', array( $this, 'set_quicktags_buttons' ), 10, 2 );
		add_filter( 'user_can_richedit', array( $this, 'disable_visual_editor' ) );
		add_filter( 'gettext', array( $this, 'change_publish_button' ), 10, 2 );
	}

	public function add_settings_link( $links ) {
		$settings_link = '<a href="admin.php?page=mc4wp-pro">Settings</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	public function change_publish_button( $translation, $text ) {
		global $pagenow;
		if ( ( $pagenow == 'post.php' || $pagenow == 'post-new.php' ) && get_post_type() == 'mc4wp-form' ) {

			if ( $text == "Publish" ) {
				$translation = __( "Save Form", 'mailchimp-for-wp' );
			} elseif ( $text == "Update" ) {
				$translation = __( "Update Form", 'mailchimp-for-wp' );
			}

		}

		return $translation;
	}

	public function set_quicktags_buttons( $settings, $editor_id ) {
		global $pagenow;
		if ( ( $pagenow == 'post.php' || $pagenow == 'post-new.php' ) && get_post_type() == 'mc4wp-form' ) {
			$settings['buttons'] = 'strong,em,link,block,img,ul,ol,li,close';
		}
		return $settings;
	}

	public function register_settings() {
		register_setting( 'mc4wp_settings', 'mc4wp', array( $this, 'validate_settings' ) );
		register_setting( 'mc4wp_checkbox_settings', 'mc4wp_checkbox', array( $this, 'validate_checkbox_settings' ) );
		register_setting( 'mc4wp_form_settings', 'mc4wp_form', array( $this, 'validate_settings' ) );
		register_setting( 'mc4wp_form_css_settings', 'mc4wp_form_css', array( $this, 'validate_form_css_settings' ) );
	}

	public function get_default_form_markup( $content = '', $post = null ) {
		if ( ( $post && $post->post_type == 'mc4wp-form' ) || is_null( $post ) ) {
			return "<p>\n\t<label for=\"mc4wp_email\">Email address: </label>\n\t<input type=\"email\" id=\"mc4wp_email\" name=\"EMAIL\" required placeholder=\"Your email address\" />\n</p>\n\n<p>\n\t<input type=\"submit\" value=\"Sign up\" />\n</p>";
		}
	}

	public function set_form_updated_messages( $messages ) {
		$messages['mc4wp-form'] = $messages['post'];
		$messages['mc4wp-form'][1] = __( 'Form updated. <a href="'. admin_url( 'admin.php?page=mc4wp-pro-form-settings' ) .'">&laquo; Back to MailChimp for WP Pro form settings</a>' );
		$messages['mc4wp-form'][6] = __( 'Form saved. <a href="'. admin_url( 'admin.php?page=mc4wp-pro-form-settings' ) .'">&laquo; Back to MailChimp for WP Pro form settings</a>' );
		return $messages;
	}

	public function save_form_data( $post_ID ) {
		if ( !current_user_can( 'edit_post', $post_ID ) ) return;
		if ( !isset( $_POST['_mc4wp_nonce'] ) || ! wp_verify_nonce( $_POST['_mc4wp_nonce'], 'mc4wp_save_form' ) ) { return false; }

		$data = $_POST['mc4wp_form'];
		$meta = array(
			'lists' => $data['lists']
		);

		$optional_meta_keys = array( 'double_optin', 'update_existing', 'replace_interests', 'send_welcome', 'ajax', 'hide_after_success', 'redirect', 'text_success', 'text_error', 'text_invalid_email', 'text_already_subscribed' );
		foreach ( $optional_meta_keys as $meta_key ) {
			if ( isset( $data[$meta_key] ) ) {
				$meta[$meta_key] = $data[$meta_key];
			}
		}

		return update_post_meta( $post_ID, '_mc4wp_settings', $meta );
	}

	public function add_meta_boxes() {
		add_meta_box( 'mc4wp-form-settings', __( 'Form settings', 'mc4wp' ), array( $this, 'show_required_form_settings_metabox' ), 'mc4wp-form', 'side', 'high' );
		add_meta_box( 'mc4wp-optional-settings', __( 'Optional settings', 'mc4wp' ), array( $this, 'show_optional_form_settings_metabox' ), 'mc4wp-form', 'normal', 'high' );
		add_meta_box( 'mc4wp-form-variables', __( 'Form variables', 'mc4wp' ), array( $this, 'show_form_variables_metabox' ), 'mc4wp-form', 'side' );
	}

	/**
	 * Remove all metaboxes except "submitdiv".
	 * Also removes all metaboxes added by other plugins..
	 */
	public function remove_meta_boxes() {
		global $wp_meta_boxes;
		if ( isset( $wp_meta_boxes["mc4wp-form"] ) && is_array( $wp_meta_boxes["mc4wp-form"] ) ) {
			$meta_boxes = $wp_meta_boxes["mc4wp-form"];
			$allowed_meta_boxes = array( 'submitdiv' );

			foreach ( $meta_boxes as $context => $context_boxes ) {
				if ( ! is_array( $context_boxes ) ) { continue; }

				foreach ( $context_boxes as $priority => $priority_boxes ) {
					if ( !is_array( $priority_boxes ) ) { continue; }

					foreach ( $priority_boxes as $meta_box_id => $meta_box_args ) {
						if ( stristr( $meta_box_id, 'mc4wp' ) === false && !in_array( $meta_box_id, $allowed_meta_boxes ) ) {
							unset( $wp_meta_boxes["mc4wp-form"][$context][$priority][$meta_box_id] );
						}
					}
				}
			}
		}
	}

	public function show_admin_notice() {
		global $pagenow;

		// check if on form-edit page


		$opts = $this->options;

		if ( !$this->has_valid_license() ) {
			if ( empty( $opts['general']['license_key'] ) ) {
				$notice = "<strong>Almost there!</strong> Please enter the license key from your purchase receipt in the <a href=\"admin.php?page=mc4wp-pro\">MailChimp for WP settings</a>.";
				include MC4WP_PLUGIN_DIR . 'includes/views/parts/admin-notice.php';

			} elseif ( $pagenow == 'admin.php' && isset( $_GET['page'] ) && $_GET['page'] == 'mc4wp-pro' ) {
				// only show on settings page.

				$notice = "<strong>Almost there!</strong> Please activate your license by clicking the \"Activate License\" button.";
				include MC4WP_PLUGIN_DIR . 'includes/views/parts/admin-notice.php';
			}
		}


	}

	public function show_form_variables_metabox( $post ) {
		?><p>Use the following variables to add some dynamic content to your form.</p><?php
		include MC4WP_PLUGIN_DIR . 'includes/views/parts/admin-text-variables.php';
	}

	public function show_required_form_settings_metabox( $post ) {
		$lists = $this->get_mailchimp_lists();
		$form_settings = MC4WP::form()->get_form_settings( $post->ID );
		include MC4WP_PLUGIN_DIR . 'includes/views/metaboxes/required-form-settings.php';
	}

	public function show_optional_form_settings_metabox( $post ) {
		$form_settings = MC4WP::form()->get_form_settings( $post->ID );
		$inherited_settings = $this->options['form'];
		$final_settings = MC4WP::form()->get_form_settings( $post->ID, true );
		include MC4WP_PLUGIN_DIR . 'includes/views/metaboxes/optional-form-settings.php';
	}

	public function disable_visual_editor( $default ) {
		if ( get_post_type() == 'mc4wp-form' ) { return false; }
		return $default;
	}

	public function validate_settings( $settings ) {
		return $settings;
	}

	public function validate_checkbox_settings( $settings ) {
		// strip tags from general label
		$settings['label'] = strip_tags( $settings['label'], '<b><strong><i><em><a><span><strike><u>' );

		// strip tags from custom labels
		$checkbox_label_keys = array_keys( $this->get_checkbox_compatible_plugins() );
		foreach ( $checkbox_label_keys as $key ) {
			if ( isset( $settings['text_' . $key . '_label'] ) ) {
				$settings['text_' . $key . '_label'] = strip_tags( $settings['text_' . $key . '_label'], '<b><strong><i><em><a><span><strike><u>' );
			}
		}

		return $settings;
	}


	public function validate_form_css_settings( $settings ) {

		// make sure width fields end in 'px' or '%'
		$_width_fields = array('buttons_width', 'fields_width');
		foreach($_width_fields as $f) {
			if(!empty($settings[$f])) {
				$s = strtolower(trim($settings[$f]));
				if(substr($s, -1) != '%' && substr($s, -2) != 'px') {
					$settings[$f] .= 'px';
				}
			}
		}

		extract( $settings );
		// make sure selector prefix ends with space
		$selector_prefix = trim( $selector_prefix ) . ' ';

		// Build CSS String
		ob_start();
		include MC4WP_PLUGIN_DIR . 'includes/views/parts/css-styles.php';
		$css = ob_get_contents();
		ob_end_clean();

		// Check if we can write, otherwise output in notice.
		if ( !is_dir( WP_CONTENT_DIR ) || !is_writeable( WP_CONTENT_DIR ) ) {
			add_settings_error( 'mc4wp', 'mc4wp-cant-write-css', "Your wp-content directory is not writable. Manually add the generated CSS to your theme stylesheet by using the <a href=\"". admin_url( 'theme-editor.php' ) ."\">Theme Editor</a> or via FTP by browsing to <em>". get_stylesheet_directory() ."/style.css</em>. <a class=\"mc4wp-show-css\" href=\"javascript:void(0);\">Show generated CSS</a>"
				."<div id=\"mc4wp_generated_css\" style=\"display:none;\">"
				."<pre style=\"white-space: pre-wrap; \">{$css}</pre>"
				."</div>" );
		} else {
			// write the string to a file in the wp-content directory
			$bytes_written = file_put_contents( WP_CONTENT_DIR . '/mc4wp-custom-styles.css', $css );

			// add a success message
			if ( $bytes_written ) {
				$enqueue_text = ( $this->options['form']['css'] == 'custom' ) ? "You selected \"load custom form styles\" so these styles will be applied on your website." : "To apply these styles on your website, select \"load custom form styles\" in the <a href=\"admin.php?page=mc4wp-pro-form-settings\">form settings</a>.";
				add_settings_error( 'mc4wp', 'mc4wp-css-built', 'CSS File has been written to <a href="'. WP_CONTENT_URL . '/mc4wp-custom-styles.css">/' . basename( WP_CONTENT_DIR ) . "/mc4wp-custom-styles.css</a>. {$enqueue_text}", 'updated' );
			}
		}

		return $settings;
	}

	public function build_menu() {
		add_menu_page( 'MailChimp for WP Pro', 'MailChimp for WP', 'manage_options', 'mc4wp-pro', array( $this, 'show_general_settings_page' ), plugins_url( 'mailchimp-for-wp-pro/assets/img/menu-icon.png' ) );

		// only add admin pages to menu if license is active and valid.
		if ( $this->has_valid_license() ) {
			add_submenu_page( 'mc4wp-pro', 'License & API Settings - MailChimp for WP Pro', 'General Settings', 'manage_options', 'mc4wp-pro', array( $this, 'show_general_settings_page' ) );
			add_submenu_page( 'mc4wp-pro', 'Checkboxes - MailChimp for WP Pro', 'Checkboxes', 'manage_options', 'mc4wp-pro-checkbox-settings', array( $this, 'show_checkbox_settings_page' ) );
			add_submenu_page( 'mc4wp-pro', 'Forms - MailChimp for WP Pro', 'Forms', 'manage_options', 'mc4wp-pro-form-settings', array( $this, 'show_form_settings_page' ) );
			add_submenu_page( 'mc4wp-pro', 'Reports - MailChimp for WP Pro', 'Reports', 'manage_options', 'mc4wp-pro-reports', array( $this, 'show_reports_page' ) );
		}

	}

	public function load_assets( $hook = '' ) {
		global $pagenow;

		if ( isset( $_GET['page'] ) && stristr( $_GET['page'], 'mc4wp-pro' ) ) {
			/* Any Settings Page */
			wp_enqueue_style( 'mc4wp-admin-styles', plugins_url( 'mailchimp-for-wp-pro/assets/css/admin-styles.css' ), array('wp-color-picker') );

			wp_register_script( 'mc4wp-admin-settings',  plugins_url( 'mailchimp-for-wp-pro/assets/js/admin-settings.js' ), array( 'jquery', 'wp-color-picker' ), false, true );
			wp_enqueue_script( array( 'jquery', 'mc4wp-admin-settings' ) );

			/* Reports page */
			if ( $_GET['page'] == 'mc4wp-pro-reports' ) {
				// load flot
				wp_register_script( 'mc4wp-flot', plugins_url( 'mailchimp-for-wp-pro/assets/js/third-party/jquery.flot.min.js' ), array( 'jquery' ), "0.8.1", true );
				wp_register_script( 'mc4wp-flot-time', plugins_url( 'mailchimp-for-wp-pro/assets/js/third-party/jquery.flot.time.min.js' ), array( 'jquery' ), "0.8.1", true );
				wp_register_script( 'mc4wp-statistics', plugins_url( 'mailchimp-for-wp-pro/assets/js/admin-statistics.js' ), array( 'mc4wp-flot-time' ), "1.0", true );

				wp_enqueue_script( array( 'jquery', 'mc4wp-flot', 'mc4wp-statistics' ) );

				// print ie excanvas script in footer
				add_action( 'admin_print_footer_scripts', array( $this, 'print_excanvas_script' ), 1 );
			}

			/* CSS Edit Page */
			if ( $_GET['page'] == 'mc4wp-pro-form-settings' && isset( $_GET['tab'] ) && $_GET['tab'] == 'form-css' ) {
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'mc4wp-form-css', plugins_url( 'mailchimp-for-wp-pro/assets/js/admin-form-css.js' ), array( 'wp-color-picker', 'jquery-ui-accordion' ), '', true );
			}

		} elseif ( ( $pagenow == 'post.php' || $pagenow == 'post-new.php' ) && get_post_type() == 'mc4wp-form' ) {
			// edit form post type pages
			wp_enqueue_style( 'mc4wp-admin-styles', plugins_url( 'mailchimp-for-wp-pro/assets/css/admin-styles.css' ) );

			//wp_register_script('mc4wp-suggest', plugins_url('mailchimp-for-wp-pro/assets/js/third-party/suggest.js'), array('jquery'), false, true);
			wp_register_script( 'mc4wp-admin-formhelper',  plugins_url( 'mailchimp-for-wp-pro/assets/js/admin-formhelper.js' ), array( 'jquery' ), false, true );

			wp_enqueue_script( array( 'jquery', 'mc4wp-suggest', 'mc4wp-admin-formhelper' ) );

			// we don't need the following scripts
			wp_dequeue_script( 'autosave', 'suggest' );

		}

	}

	public function get_checkbox_compatible_plugins() {
		$checkbox_plugins = array(
			'comment_form' => "Comment form",
			"registration_form" => "Registration form"
		);

		if ( is_multisite() ) $checkbox_plugins['multisite_form'] = "MultiSite forms";
		if ( class_exists( 'Easy_Digital_Downloads' ) ) $checkbox_plugins['edd_checkout'] = "Easy Digital Downloads checkout";
		if ( class_exists( "BuddyPress" ) ) $checkbox_plugins['buddypress_form'] = "BuddyPress registration";
		if ( class_exists( 'Woocommerce' ) ) $checkbox_plugins['woocommerce_checkout'] = "WooCommerce checkout";
		if ( class_exists( 'bbPress' ) ) $checkbox_plugins['bbpress_forms'] = "bbPress";

		return $checkbox_plugins;
	}

	public function get_selected_checkbox_hooks() {
		$checkbox_plugins = $this->get_checkbox_compatible_plugins();
		$selected_checkbox_hooks = array();
		$checkbox_opts = $this->options['checkbox'];

		// check which checkbox hooks are selected
		foreach ( $checkbox_plugins as $code => $name ) {
			if ( isset( $checkbox_opts['show_at_'.$code] ) && $checkbox_opts['show_at_'.$code] ) { $selected_checkbox_hooks[$code] = $name; }
		}

		return $selected_checkbox_hooks;
	}

	public function show_general_settings_page() {
		$active_tab = 'general-settings';
		$opts = $this->options['general'];

		$connected = ( empty( $opts['api_key'] ) ) ? false : ( MC4WP::api()->is_connected() );
		$lists = $this->get_mailchimp_lists();

		if ( !empty( $opts['license_key'] ) && $this->has_valid_license() && !$connected ) {
			add_settings_error( "mc4wp", "invalid-api-key", 'Please make sure the plugin is connected to MailChimp. <a href="?page=mc4wp-pro">Provide a valid API key.</a>', 'updated' );
		}

		include_once MC4WP_PLUGIN_DIR . 'includes/views/admin-general-settings.php';
	}

	public function show_checkbox_settings_page() {
		$opts = $this->options['checkbox'];
		$lists = $this->get_mailchimp_lists();

		$checkbox_plugins = $this->get_checkbox_compatible_plugins();
		$selected_checkbox_hooks = $this->get_selected_checkbox_hooks();

		include_once MC4WP_PLUGIN_DIR . 'includes/views/admin-checkbox-settings.php';
	}

	public function show_form_settings_page() {
		$tab = ( isset( $_GET['tab'] ) ) ? $_GET['tab'] : 'forms-settings';
		$opts = $this->options['form'];

		if ( $tab == 'forms-settings' ) {

			include 'tables/MC4WP_Forms_Table.php';
			$table = new MC4WP_Forms_Table( $this );
		} else {
			// forms css page
			$css_keys = array(
				'form_background_color', 'form_font_color', 'form_border_color', 'form_border_width', 'form_horizontal_padding', 'form_vertical_padding', 'form_text_align',
				'paragraphs_font_size', 'paragraphs_font_color', 'paragraphs_vertical_margin',
				'labels_font_color', 'labels_font_style', 'labels_font_size', 'labels_display', 'labels_vertical_margin', 'labels_horizonal_margin',
				'fields_border_color', 'fields_border_width', 'fields_width', 'fields_height', 'fields_display',
				'buttons_background_color', 'buttons_font_color', 'buttons_font_size', 'buttons_border_color',
				'buttons_hover_background_color', 'buttons_hover_font_color', 'buttons_hover_border_color',
				'buttons_border_width', 'buttons_width', 'buttons_height', 'buttons_display',
				'messages_font_color_error', 'messages_font_color_success',
				'selector_prefix'
			);

			// fill array with default css settings, prevent having to call isset() every time
			$css = array_merge( array_fill_keys( $css_keys, '' ), get_option( 'mc4wp_form_css', array() ) );

			$forms = get_posts( 'post_type=mc4wp-form&posts_per_page=-1' );
			$form_id = ( isset( $forms[0] ) ) ? $forms[0]->ID : 0;

			// check wp version
			global $wp_version;
			if ( version_compare( $wp_version, 3.5, "<" ) ) {
				add_settings_error( 'mc4wp', 'mc4wp-incompatible-wp-version', "You need at least WordPress 3.5 to really enjoy all the benefits of the CSS Builder. Please <a href=\"".admin_url( 'update-core.php' )."\">update your WordPress</a> - strongly recommended." , 'updated' );
			}
		}

		include_once MC4WP_PLUGIN_DIR . 'includes/views/admin-form-settings.php';
	}

	public function show_log_page() {
		include_once MC4WP_PLUGIN_DIR . 'includes/tables/MC4WP_Subscribers_Table.php';
		$table = new MC4WP_Subscribers_Table( $this );
		$tab = 'log';
		include_once MC4WP_PLUGIN_DIR . 'includes/views/admin-reports.php';
	}

	public function show_stats_page() {
		$statistics = MC4WP::statistics();

		// set default range or get range from URL
		$range = ( isset( $_GET['range'] ) ) ? $_GET['range'] : 'last_week';

		// get data
		if ( $range != 'custom' ) {
			$args = $statistics->get_range_times( $range );
		} else {
			// construct timestamps from given date in select boxes
			$start = strtotime( implode( '-', array( $_GET['start_year'], $_GET['start_month'], $_GET['start_day'] ) ) );
			$end = strtotime( implode( '-', array( $_GET['end_year'], $_GET['end_month'], $_GET['end_day'] ) ) );

			// calculate step size
			$step = $statistics->get_step_size( $start, $end );
			$given_day = $_GET['start_day'];

			$args = compact( "step", "start", "end", "given_day" );
		}

		// check if start timestamp comes after end timestamp
		if ( $args['start'] >= $args['end'] ) {
			$args = $statistics->get_range_times( 'last_week' );
			add_settings_error( 'mc4wp-statistics-error', 'invalid-range', "End date can't be before the start date" );
		}

		// setup statistic settings
		$ticksizestep = ( $args['step'] == 'week' ) ? 'month' : $args['step'];
		$statistics_settings = $this->statistics_settings = array( 'ticksize' => array( 1, $ticksizestep ) );
		$statistics_data = $this->statistics_data = $statistics->get_statistics( $args );

		//$totals = $statistics->get_total_signups( $args );

		// add scripts
		// use wp_localize_script only if WP version >= 3.3
		global $wp_version;
		if ( version_compare( $wp_version, 3.3, ">=" ) ) {
			wp_localize_script( 'mc4wp-statistics', 'mc4wp_statistics_data', $statistics_data );
			wp_localize_script( 'mc4wp-statistics', 'mc4wp_statistics_settings', $statistics_settings );
		} else {
			add_action( 'admin_print_footer_scripts', array( $this, 'print_statistics_data' ) );
		}

		$start_day = ( isset( $_GET['start_day'] ) ) ? $_GET['start_day'] : 0;
		$start_month = ( isset( $_GET['start_month'] ) ) ? $_GET['start_month'] : 0;
		$start_year = ( isset( $_GET['start_year'] ) ) ? $_GET['start_year'] : 0;
		$end_day = ( isset( $_GET['end_day'] ) ) ? $_GET['end_day'] : 0;
		$end_month = ( isset( $_GET['end_month'] ) ) ? $_GET['end_month'] : 0;
		$end_year = ( isset( $_GET['end_year'] ) ) ? $_GET['end_year'] : 0;
		$tab = 'statistics';

		include_once MC4WP_PLUGIN_DIR . 'includes/views/admin-reports.php';
	}

	public function show_reports_page() {
		$tab = ( isset( $_GET['tab'] ) ) ? $_GET['tab'] : 'stats';

		if ( $tab == 'log' ) {
			return $this->show_log_page();
		} else {
			return $this->show_stats_page();
		}
	}

	private function activate_license() {
		if ( $this->has_valid_license() ) { return true; }

		$license_status = $this->call_license_api( 'activate_license' );
		update_option( 'mc4wp_license_status', $license_status );

		if ( $license_status == 'valid' && ( $_SERVER['REMOTE_ADDR'] == '127.0.0.1' || !filter_var( $_SERVER["REMOTE_ADDR"], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE ) ) ) {
			// local (dev) environment, deactivate license
			$this->deactivate_license();
			update_option( 'mc4wp_license_status', 'local' );
		}

		return $license_status;
	}

	private function deactivate_license() {
		if ( !$this->has_valid_license() ) { return true; }

		// handle local licenses, no need to call license API
		if ( $this->get_license_status() == 'local' ) {
			$license_status = 'deactivated';
		} else {
			$license_status = $this->call_license_api( 'deactivate_license' );
		}

		update_option( 'mc4wp_license_status', $license_status );
		return $license_status;
	}

	/**
	 * Call the license api with the given action
	 */
	private function call_license_api( $action ) {

		$license_key = $this->options['general']['license_key'];
		$params = array(
			'edd_action'=> $action,
			'license'  => $license_key,
			'item_name' => urlencode( MC4WP_ITEM_NAME )
		);

		// Call the custom API.
		$response = wp_remote_get( trim( add_query_arg( $params, MC4WP_SHOP_URL ) ), array( 'timeout' => 15, 'sslverify' => false, 'headers' => array( 'Accept-Encoding' => '*' ) ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) ) {
			add_settings_error( 'mc4wp', 'http-error', "HTTP Error: ". $response->get_error_message(), 'error' );
			return false;
		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		$license_status = $license_data->license;

		return $license_status;
	}

	/**
	 * Toggle (activate | deactivate) the license status
	 */
	public function toggle_license_status() {
		if ( !isset( $_POST['mc4wp_toggle_license'] ) ) { return; }

		if ( !check_admin_referer( 'mc4wp_toggle_license', '_mc4wp_nonce' ) ) { return false; }

		if ( $this->has_valid_license() ) {
			$license_status = $this->deactivate_license();
		} else {
			$license_status = $this->activate_license();
		}

		if ( $license_status ) {
			$notices = array(
				'deactivated' => array( 'message' => 'License deactivated.', 'css_class' => 'updated' ),
				'failed' => array( 'message' => 'Something went wrong when trying to deactivate your license. Please try again later.', 'css_class' => 'error' ),
				'invalid' => array( 'message' => 'Your license key seems to be invalid.', 'css_class' => 'error' ),
				'valid' => array( 'message' => 'License activated.', 'css_class' => 'updated' )
			);

			add_settings_error( 'mc4wp_general', $license_status . "-license", $notices[$license_status]['message'], $notices[$license_status]['css_class'] );
		}
	}

	/**
	 * Check if the plugin has an active license.
	 *
	 * @return boolean True if the plugin has a valid (active) license.
	 */
	public function has_valid_license() {
		$license_status = $this->get_license_status();
		return $license_status == 'valid' || $license_status == 'local';
	}

	public function get_license_status() {
		return get_option( 'mc4wp_license_status' );
	}

	/**
	 * Load the auto-updater for automatic WP plugin updates.
	 */
	public function load_auto_updater() {
		// only get updates with a valid license
		if ( !$this->has_valid_license() ) { return false; }

		$license_key = $this->options['general']['license_key'];

		if ( empty( $license_key ) ) { return false; }

		if ( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
			// load our custom updater if it doesn't already exist
			include_once MC4WP_PLUGIN_DIR . 'includes/third-party/EDD_SL_Plugin_Updater.php';
		}

		$edd_updater = new EDD_SL_Plugin_Updater( 'http://dannyvankooten.com/mailchimp-for-wordpress/', MC4WP_PLUGIN_FILE, array(
				'version'  => MC4WP_VERSION_NUMBER,
				'license'  => $license_key,
				'item_name'     => MC4WP_ITEM_NAME,
				'author'  => 'Danny van Kooten'
			)
		);
	}

	/**
	 * Show a notice if MailChimp for WP Lite is activated
	 */
	public function show_notice_to_deactivate_lite() {
		if ( !is_plugin_active( 'mailchimp-for-wp/mailchimp-for-wp.php' ) ) { return; }
		?><div class="updated">
			<p><strong>Welcome to MailChimp for WordPress Pro!</strong> We've transfered the settings you had set in the Lite version, please <a href="<?php echo get_admin_url( null, 'plugins.php#mailchimp-for-wp' ); ?>">deactivate it now</a> to prevent problems.</p>
		</div>
		<?php
	}

	/**
	 * Print the statistics data in the footer on statistics pages
	 * But only if WP < 3.3, as an alternative to wp_localize_script.
	 */
	public function print_statistics_data() {
?>
		<script type="text/javascript">
		/* <![CDATA[ */
		var mc4wp_statistics_data = <?php echo json_encode( $this->statistics_data ); ?>;
		var mc4wp_statistics_settings = <?php echo json_encode( $this->statistics_settings ); ?>;
		/* ]]> */
		</script>
		<?php
	}

	/*
	* Get the name of the MailChimp list with the given ID.
	*/
	public function get_mailchimp_list_name( $id ) {
		$lists = (array) $this->get_mailchimp_lists();

		foreach ( $lists as $list ) {
			if ( $list->id == $id ) return $list->name;
		}

		return '';
	}

	/**
	 * Get MailChimp lists
	 * Try cache first, then try API, then try fallback cache.
	 */
	private function get_mailchimp_lists() {
		$cached_lists = get_transient( 'mc4wp_mailchimp_lists' );
		$refresh_cache = ( isset( $_POST['renew-cached-data'] ) );

		if ( $refresh_cache || !$cached_lists ) {
			// make api request for lists
			$api = MC4WP::api();
			$lists = array();
			$lists_data = $api->get_lists();

			if ( $lists_data ) {

				$list_ids = array();
				foreach ( $lists_data as $list ) {
					$list_ids[] = $list->id;

					$lists["{$list->id}"] = (object) array(
						'id' => $list->id,
						'name' => $list->name,
						'subscriber_count' => $list->stats->member_count,
						'merge_vars' => array(),
						'interest_groupings' => array()
					);

					// get interest groupings
					$groupings_data = $api->get_list_groupings( $list->id );
					if ( $groupings_data ) {
						$lists["{$list->id}"]->interest_groupings = array_map( array( $this, 'strip_unnecessary_grouping_properties' ), $groupings_data );
					}
				}

				// get merge vars for all lists at once
				$merge_vars_data = $api->get_lists_with_merge_vars( $list_ids );
				if ( $merge_vars_data ) {
					foreach ( $merge_vars_data as $list ) {
						// add merge vars to list
						$lists["{$list->id}"]->merge_vars = array_map( array( $this, 'strip_unnecessary_merge_vars_properties' ), $list->merge_vars );
					}
				}

				// cache renewal triggered manually?
				if ( isset( $_POST['renew-cached-data'] ) ) {
					if ( $lists ) {
						add_settings_error( "mc4wp", "cache-renewed", 'Renewed MailChimp cache.', 'updated' );
					} else {
						add_settings_error( "mc4wp", "cache-renew-failed", 'Failed to renew MailChimp cache - please try again later.' );
					}
				}

				// store lists in transients
				set_transient( 'mc4wp_mailchimp_lists', $lists, ( 24 * 3600 ) ); // 1 day
				set_transient( 'mc4wp_mailchimp_lists_fallback', $lists, 1209600 ); // 2 weeks
				return $lists;
			} else {
				// api request failed, get fallback data (with longer lifetime)
				$cached_lists = get_transient( 'mc4wp_mailchimp_lists_fallback' );
				if ( !$cached_lists ) { return array(); }
			}

		}

		return $cached_lists;
	}

	/**
	 * Build the group array object which will be stored in cache
	 */
	public function strip_unnecessary_group_properties( $group ) {
		return (object) array(
			'name' => $group->name
		);
	}

	/**
	 * Build the groupings array object which will be stored in cache
	 */
	public function strip_unnecessary_grouping_properties( $grouping ) {
		return (object) array(
			'id' => $grouping->id,
			'name' => $grouping->name,
			'groups' => array_map( array( $this, 'strip_unnecessary_group_properties' ), $grouping->groups ),
			'form_field' => $grouping->form_field
		);
	}

	/**
	 * Build the merge_var array object which will be stored in cache
	 */
	public function strip_unnecessary_merge_vars_properties( $merge_var ) {
		$array = array(
			'name' => $merge_var->name,
			'field_type' => $merge_var->field_type,
			'req' => $merge_var->req,
			'tag' => $merge_var->tag
		);

		if ( isset( $merge_var->choices ) ) {
			$array["choices"] = $merge_var->choices;
		}

		return (object) $array;

	}

	/**
	 * Print the IE canvas fallback script in the footer on statistics pages
	 */
	public function print_excanvas_script() {
?>
		<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="<?php echo plugins_url( 'mailchimp-for-wp-pro/assets/js/third-party/excanvas.min.js' ); ?>"></script><![endif]-->
		<?php
	}


	/**
	 * Runs on plugin activation
	 * Transfers settings from MC4WP Lite
	 */
	public function on_activation() {
		// setup log table
		MC4WP::log()->setup();

		// delete transients
		delete_transient( 'mc4wp_mailchimp_lists' );
		delete_transient( 'mc4wp_mailchimp_lists_fallback' );

		// check if PRO option exists and contains data entered by user
		if ( ( $o = get_option( 'mc4wp' ) ) != false && ( !empty( $o['api_key'] ) || !empty( $o['license_key'] ) ) ) { return; }

		// user entered no PRO options in the past, let's see if we can transfer from LITE

		$lite_settings = array(
			'general' => (array) get_option( 'mc4wp_lite' ),
			'checkbox' => (array) get_option( 'mc4wp_lite_checkbox' ),
			'form' => (array) get_option( 'mc4wp_lite_form' )
		);

		$default_options = MC4WP::instance()->get_default_options();

		foreach ( $default_options as $group_key => $options ) {
			foreach ( $options as $option_key => $option_value ) {
				if ( isset( $lite_settings[$group_key][$option_key] ) && !empty( $lite_settings[$group_key][$option_key] ) ) {
					$default_options[$group_key][$option_key] = $lite_settings[$group_key][$option_key];
				}
			}
		}


		$forms = get_posts( array( 'post_type' => 'mc4wp-form' ) );
		if ( !$forms ) {
			// no forms found, try to transfer from lite.
			$form_markup = ( isset( $lite_settings['form']['markup'] ) ) ? $lite_settings['form']['markup'] : $this->get_default_form_markup();
			$form_ID = wp_insert_post( array(
					'post_type' => 'mc4wp-form',
					'post_title' => 'Sign-Up Form #1',
					'post_content' => $form_markup,
					'post_status' => 'publish'
				) );

			$lists = isset( $lite_settings['form']['lists'] ) ? $lite_settings['form']['lists'] : array();
			update_post_meta( $form_ID, '_mc4wp_settings', array( 'lists' => $lists ) );
			update_option( 'mc4wp_default_form_id', $form_ID );
		}

		// store options
		update_option( 'mc4wp', $default_options['general'] );
		update_option( 'mc4wp_checkbox', $default_options['checkbox'] );
		update_option( 'mc4wp_form', $default_options['form'] );
	}

	/**
	 * Runs on deactivation
	 * Remotely deactivates the license key so it can be used on another website
	 */
	public function on_deactivation() {
		// deactivate license
		$result = $this->deactivate_license();

		delete_transient( 'mc4wp_mailchimp_lists' );
		delete_transient( 'mc4wp_mailchimp_lists_fallback' );
	}

}
