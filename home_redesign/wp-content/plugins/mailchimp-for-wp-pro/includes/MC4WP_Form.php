<?php

class MC4WP_Form
{
	private $form_instance_number = 1;
	private $error = null;
	private $success = false;
	private $submitted_form_instance = 0;

	public function __construct() 
	{
		add_action('init', array($this, 'register_post_type'));

		// check for license after post type has been initialized
		if(!MC4WP::instance()->has_valid_license()) { return; }

		$opts = $this->get_options();

		// enable shortcodes in text widgets
		add_filter( 'widget_text', 'shortcode_unautop');
		add_filter( 'widget_text', 'do_shortcode');	

		add_shortcode('mc4wp_form', array($this, 'output_form'));
		add_shortcode('mc4wp-form', array($this, 'output_form'));
				
		if($opts['ajax']) {
			add_action('wp_enqueue_scripts', array($this, 'load_ajax_scripts'));
		}


		// has a form been submitted, either by ajax or manually?
		if(isset($_POST['mc4wp_form_submit'])) {
			$this->ensure_backwards_compatibility();

			if(!defined('DOING_AJAX') || !DOING_AJAX) {
				add_action('init', array($this, 'submit'));
				add_action( 'wp_footer', array( $this, 'print_scroll_js' ), 99);
			} else {
				// add ajax actions, regardless of setting (form may have individual setting)
				add_action('wp_ajax_nopriv_mc4wp_submit_form', array($this, 'ajax_submit'));
				add_action('wp_ajax_mc4wp_submit_form', array($this, 'ajax_submit'));
			}
		}

		if(isset($_GET['_mc4wp_css_preview'])) {
			add_action('init', array($this, 'show_form_preview'), 1);
		} elseif($opts['css']) {
			// not a preview request, load css.
			if($opts['css'] == 'custom') {
				add_action( 'wp_enqueue_scripts', array($this, 'load_custom_stylesheet'), 90);
			} else {
				add_filter('mc4wp_stylesheets', array($this, 'add_stylesheets'));
			}
		}
		
	}

	public function show_form_preview()
	{
		require MC4WP_PLUGIN_DIR . 'includes/views/form-preview.php';
		die();
	}

	private function get_options()
	{
		return MC4WP::instance()->get_option_group('form');
	}

	public function register_post_type()
	{
		register_post_type( 'mc4wp-form', array(
			'labels' => array(
				'name' => 'MailChimp Sign-up Forms',
				'singular_name' => 'Sign-up Form',
				'add_new_item' => 'Add New Form',
				'edit_item' => 'Edit Form',
				'new_item' => 'New Form',
				'all_items' => 'All Forms',
				'view_item' => null
				),
			'public' => true,
			'publicly_queryable' => false,
			'exclude_from_search' => true,
			'show_in_nav_menus' => false,
			'show_in_menu' => false,
			'rewrite' => false
			)
		);
	}

	public function add_stylesheets($stylesheets) {

		$opts = $this->get_options();

		$stylesheets['form'] = 1;

		// theme?
		if($opts['css'] != 1 && $opts['css'] != 'default') {
			$stylesheets['form-theme'] = $opts['css'];

			if($opts['css'] == 'custom-color') {
				$stylesheets['custom-color'] = urlencode($opts['custom_theme_color']);
			}
		}

		return $stylesheets;
	}

	public function load_custom_stylesheet()
	{
		if(file_exists(WP_CONTENT_DIR . '/mc4wp-custom-styles.css')) {
			wp_enqueue_style( 'mc4wp-custom-form-css', WP_CONTENT_URL . '/mc4wp-custom-styles.css', array());
		}
	}

	public function load_ajax_scripts()
	{		
		wp_enqueue_script('mc4wp-ajax-forms', plugins_url('mailchimp-for-wp-pro/assets/js/ajax-forms.js'), array('jquery-form'), MC4WP_VERSION_NUMBER, true);
		wp_localize_script( 'mc4wp-ajax-forms', 'mc4wp_vars', array(
			'ajaxurl' => admin_url( 'admin-ajax.php', 'http' ),
			'ajax_loader_url' => plugins_url('mailchimp-for-wp-pro/assets/img/ajax-loader.gif')
			)
		);
		
	}

	public function output_form($atts = array(), $content = null)
	{
		if(!function_exists('mc4wp_replace_variables')) {
			include_once MC4WP_PLUGIN_DIR . 'includes/template-functions.php';
		}

		$is_administrator = current_user_can('manage_options');



		if(!isset($atts['id'])) { 

			// try to get default form id
			$atts['id'] = get_option('mc4wp_default_form_id', 0);

			// failure? :(
			if(!$atts['id']) {
				return ($is_administrator) ? '<p><strong>MailChimp for WP Pro error:</strong> Please specify a form ID in the shortcode attributes. Example: <code>[mc4wp-form id="31"]</code></p>' : ''; 
			}
		}

		$form = get_post($atts['id']);

		if(!$form || $form->post_type != 'mc4wp-form') { return ($is_administrator) ? '<p><strong>MailChimp for WP Pro error:</strong> Sign-up form not found. Please check if you have used the correct form ID.</p>' : ''; }

		// get form, first element in posts array
		$form_ID = $form->ID;
		$form_markup = __($form->post_content);
		$settings = $this->get_form_settings($form_ID, true);

		// add some useful css classes
		$css_classes = "mc4wp-form mc4wp-form-{$form_ID} ";

		if($settings['ajax']) { 
			$css_classes .= 'mc4wp-ajax '; 

			// get the ajax-forms.js to load in footer.. 
			if(!wp_script_is('mc4wp-ajax-forms', 'enqueued')) {
				add_action('wp_footer', array($this, 'load_ajax_scripts'));
			} 
			
		}

		if($this->error) $css_classes .= 'mc4wp-form-error ';
		if($this->success) $css_classes .= 'mc4wp-form-success ';

		$content = "<!-- MailChimp for WP Pro v". MC4WP_VERSION_NUMBER ." -->";
		$content .= '<div id="mc4wp-form-'.$this->form_instance_number.'" class="'.$css_classes.'">';

		// show the form fields if not submitted or if submitted with hide_after_success == false
		if(!($this->success && $settings['hide_after_success'])) {
			$content .= '<form method="post" action="#mc4wp-form-'. $this->form_instance_number .'">';

			// replace special values
			$form_markup = str_replace(array('%N%', '{n}'), $this->form_instance_number, $form_markup);
			$form_markup = mc4wp_replace_variables($form_markup, array_values($settings['lists']));

			// allow plugins to alter form content
			$form_markup = apply_filters('mc4wp_form_content', $form_markup, $form_ID);

			// add form markup to output
			$content .= $form_markup;

			// hidden fields
			$content .= '<textarea name="mc4wp_required_but_not_really" style="display: none;"></textarea>';
			$content .= '<input type="hidden" name="mc4wp_form_ID" value="'. $form_ID .'" />';
			$content .= '<input type="hidden" name="mc4wp_form_instance" value="'. $this->form_instance_number .'" />';
			$content .= '<input type="hidden" name="mc4wp_form_submit" value="1" />';
			$content .= '<input type="hidden" name="mc4wp_form_nonce" value="'. wp_create_nonce('_mc4wp_form_nonce') .'" />';
			$content .= "</form>";
		}


		// if ajax, output all error messages (but hidden)
		if($settings['ajax']) {
			$content .= '<span class="mc4wp-ajax-loader" style="display: none;"></span>';

				// output all error messages but hide them
			$messages = array('success', 'already_subscribed', 'invalid_email', 'error');
			foreach($messages as $m) {
				if(isset($settings['text_'. $m]) && !empty($settings['text_'. $m])) {

						// build string with css classes
					$css_classes = "mc4wp-alert mc4wp-{$m}-message ";
					if($m == 'success') { $css_classes .= 'mc4wp-success'; }
					elseif($m == 'already_subscribed') { $css_classes .= 'mc4wp-notice'; }
					else{ $css_classes .= 'mc4wp-error'; }

					$content .= '<div style="display:none;" class="'.$css_classes.'">'. __($settings['text_'. $m]) . '</div>';
				}
			}
		} 

		if((int) $this->form_instance_number === (int) $this->submitted_form_instance) {
			// only show success or error messages if this is the form that was submitted.

			if($this->success) {
				$content .= '<div class="mc4wp-alert mc4wp-success">' . __($settings['text_success']) . '</div>';
			} elseif($this->error) {
				
				$e = $this->error;

				if($e == 'already_subscribed') {
					$text = (empty($settings['text_already_subscribed'])) ? MC4WP::api()->get_error_message() : __($settings['text_already_subscribed']);
					$content .= '<div class="mc4wp-alert mc4wp-notice">'. $text .'</div>';
				} elseif(isset($settings['text_' . $e]) && !empty($settings['text_'. $e] )) {
					$content .= '<div class="mc4wp-alert mc4wp-error">' . __($settings['text_' . $e]) . '</div>';
				}

				if($is_administrator) {
					$api = MC4WP::api();

					// show MailChimp error message for debugging purposes
					if($api->has_error()) {
						$content .= '<div class="mc4wp-alert mc4wp-error"><strong>Admin notice:</strong> '. $api->get_error_message() . '</div>';
					}	
				}		
			}
		}

		/* WordPress Administrators only */
		if($is_administrator) {

			if(empty($settings['lists'])) {
				$content .= '<div class="mc4wp-alert mc4wp-error"><strong>Admin notice:</strong> you selected no MailChimp list(s) for this form yet. <a href="'. get_admin_url(null, "post.php?post={$form_ID}&action=edit") .'">Edit this sign-up form</a> and select at least one list.</div>';
			}

		} 

		$content .= "</div>";
		$content .= "<!-- / MailChimp for WP Pro -->";

		// increase form instance number in case there is more than one form on a page
		$this->form_instance_number++;

		return $content;
	}

	public function submit()
	{

		// check nonce
		if(!isset($_POST['mc4wp_form_nonce']) || !wp_verify_nonce( $_POST['mc4wp_form_nonce'], '_mc4wp_form_nonce' )) { 
			$this->error = 'invalid_nonce';
			$success = false;
		} else {
			$success = $this->subscribe();
		}

		$this->submitted_form_instance = (isset($_POST['mc4wp_form_instance'])) ? $_POST['mc4wp_form_instance'] : 0;

		if($success) { 

			$form_ID = $_POST['mc4wp_form_ID'];
			$settings = $this->get_form_settings($form_ID, true);

			// check if we want to redirect the visitor
			if ( !empty( $settings['redirect'] ) ) {
				wp_redirect( $settings['redirect'] );
				exit;
			}

			return true;
		} else {
			return false;
		}
	}

	public function ajax_submit()
	{
		// unset the $_POST['action'] var used to identify this AJAX request
		if(isset($_POST['action'])) unset($_POST['action']);

		// check nonce, die if invalid.
		check_ajax_referer('_mc4wp_form_nonce', 'mc4wp_form_nonce');

		$success = $this->subscribe();
		$response = array();
		$response['success'] = $success;
		
		if($success) {
			$form_ID = $_POST['mc4wp_form_ID'];
			$settings = $this->get_form_settings($form_ID, true);
			$response['redirect'] = (empty($settings['redirect'])) ? false : $settings['redirect'];
			$response['hide_form'] = ($settings['hide_after_success'] == 1);
		} else {
			$response['error'] = $this->error;
			$response['mailchimp_error'] = $this->mailchimp_error;
		}

		// clear output, some plugins might have thrown errors by now.
		ob_end_clean();

		header( "Content-Type: application/json" );
		echo json_encode($response);
		exit;
	}

	private function subscribe()
	{
	
		// check if honeypot was filled
		if(isset($_POST['mc4wp_required_but_not_really']) && !empty($_POST['mc4wp_required_but_not_really'])) {
			// spam bot filled the honeypot field
			$this->error = 'spam';
			return false;
		}

		// upercase all user field names
		foreach($_POST as $name => $value) {
			if($name !== strtoupper($name) && substr($name, 0, 6) !== 'mc4wp_') {
				$_POST[strtoupper($name)] = $value;
				unset($_POST[$name]);
			}
		}
		
		// validate email field
		if(!isset($_POST['EMAIL']) || !is_email($_POST['EMAIL'])) {
			$this->error = 'invalid_email';
			return false;
		} 	

		// get individual form settings
		$form_ID = (isset($_POST['mc4wp_form_ID'])) ? $_POST['mc4wp_form_ID'] : 0;
		$settings = $this->get_form_settings($form_ID, true);

		// check if MailChimp lists were selected
		if(empty($settings['lists'])) {
			$this->error = 'no_lists_selected';
			return false;
		}

		$merge_vars = array();
		$merge_vars['GROUPINGS'] = array();
		$email = $_POST['EMAIL'];

		foreach($_POST as $name => $value) {

			// only add uppercases fields to merge variables array
			if($name == 'EMAIL' || $name !== strtoupper($name)) { continue; }

			if($name === 'GROUPINGS') {

				$groupings = $value;

				// malformed, do nothing..
				if(!is_array($groupings)) { continue; }

				foreach($groupings as $grouping_id_or_name => $groups) {

						$grouping = array();

						// group ID or group name given?
						if(is_numeric($grouping_id_or_name)) {
							$grouping['id'] = $grouping_id_or_name;
						} else {
							$grouping['name'] = $grouping_id_or_name;
						}

						// comma separated list should become an array
						if(!is_array($groups)) {
							$grouping['groups'] = explode(',', $groups);
						} else {
							$grouping['groups'] = $groups;
						}

						// add grouping to array
						$merge_vars['GROUPINGS'][] = $grouping;
				}
				
			} else {
				$merge_vars[$name] = $value;
			}	
		}

		// unset groupings if not used
		if(empty($merge_vars['GROUPINGS'])) { unset($merge_vars['GROUPINGS']); }

		// Try to guess FNAME and LNAME if they are not given, but NAME is
		if(isset($merge_vars['NAME']) && !isset($merge_vars['FNAME']) && !isset($merge_vars['LNAME'])) {
			$strpos = strpos($merge_vars['NAME'], ' ');
			if($strpos) {
				$merge_vars['FNAME'] = substr($merge_vars['NAME'], 0, $strpos);
				$merge_vars['LNAME'] = substr($merge_vars['NAME'], $strpos);
			} else {
				$merge_vars['FNAME'] = $merge_vars['NAME'];
			}
		}

		$api = MC4WP::api();

		// make subscribe request for each selected list
		foreach($settings['lists'] as $list_id) {
			$result = $api->subscribe($list_id, $email, $merge_vars, 'html', $settings['double_optin'], $settings['update_existing'], $settings['replace_interests'], $settings['send_welcome']);
		}

		if($result === true) {
			// create referer url
			$from_url = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '';
			do_action('mc4wp_subscribe_form', $email, $settings['lists'], $form_ID, $merge_vars, $from_url); 

			$this->success = true;
		} else {
			$this->success = false;
			$this->error = $result;
			$this->mailchimp_error = $api->get_error_message();
		}

		return $this->success;
	}

	public function get_form_settings($form_ID, $inherit = false)
	{
		$inherited_settings = $this->get_options();
		$form_settings = array();

		// set defaults
		$form_settings['lists'] = array();

		// fill optional meta keys with empty strings
		$optional_meta_keys = array('double_optin', 'update_existing', 'replace_interests', 'send_welcome', 'ajax', 'hide_after_success', 'redirect', 'text_success', 'text_error', 'text_invalid_email', 'text_already_subscribed');
		foreach($optional_meta_keys as $meta_key) {
			if($inherit) {
				$form_settings[$meta_key] = $inherited_settings[$meta_key];
			} else {
				$form_settings[$meta_key] = '';
			}
		}

		$meta = get_post_meta($form_ID, '_mc4wp_settings', true);
		if($meta) {
			foreach($meta as $key => $value) {
				// only add meta value if not empty
				if($value != '') { $form_settings[$key] = $value; }
			}
		}

		return $form_settings;
	}

	/*
		Ensure backwards compatibility so sign-up forms that contain old form mark-up rules don't break
		- Uppercase $_POST variables that should be sent to MailChimp
		- Format GROUPINGS in one of the following formats. 
			$_POST[GROUPINGS][$group_id] = "Group 1, Group 2"
			$_POST[GROUPINGS][$group_name] = array("Group 1", "Group 2")
	*/
	public function ensure_backwards_compatibility()
	{
		// detect old style GROUPINGS, then fix it.
		if(isset($_POST['GROUPINGS']) && is_array($_POST['GROUPINGS']) && isset($_POST['GROUPINGS'][0])) {

			$old_groupings = $_POST['GROUPINGS'];
			unset($_POST['GROUPINGS']);
			$new_groupings = array();

			foreach($old_groupings as $grouping) {

				if(!isset($grouping['id']) && !isset($grouping['name'])) { continue; }
				if(!isset($grouping['groups'])) { continue; }

				$key = (isset($grouping['id'])) ? $grouping['id'] : $grouping['name'];

				$new_groupings[$key] = $grouping['groups'];

			}

			// re-fill $_POST array with new groupings
			if(!empty($new_groupings)) { $_POST['GROUPINGS'] = $new_groupings; }

		}

		return;
	}

	public function print_scroll_js() {
		?><script type="text/javascript">(function(){if(undefined==window.jQuery){return}var e=window.jQuery,t=window.location.hash;if(t.substring(1,12)=="mc4wp-form-"){var n=e(t);if(!n.length){return false}var r=n.offset().top,i=(e(window).innerHeight()-n.outerHeight())/2;r=i>0?r-i:r;if(r>0){var s=500+r;e("html,body").animate({scrollTop:r},s)}return false}})()</script><?php
	}
}