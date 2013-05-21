<?php
/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 * By default it uses the theme name, in lowercase and without spaces, but this can be changed if needed.
 * If the identifier changes, it'll appear as if the options have been reset.
 */

function optionsframework_option_name() {

	// This gets the theme name from the stylesheet
	$themename = get_option( 'stylesheet' );
	$themename = preg_replace("/\W/", "_", strtolower($themename) );

	$optionsframework_settings = get_option( 'optionsframework' );
	$optionsframework_settings['id'] = $themename;
	update_option( 'optionsframework', $optionsframework_settings );
}

/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the 'id' fields, make sure to use all lowercase and no spaces.
 *
 * If you are making your theme translatable, you should replace 'options_framework_theme'
 * with the actual text domain for your theme.  Read more:
 * http://codex.wordpress.org/Function_Reference/load_theme_textdomain
 */

function optionsframework_options() {

	// Test data
	$test_array = array(
		'one' => __('One', 'options_framework_theme'),
		'two' => __('Two', 'options_framework_theme'),
		'three' => __('Three', 'options_framework_theme'),
		'four' => __('Four', 'options_framework_theme'),
		'five' => __('Five', 'options_framework_theme')
	);

	// Multicheck Array
	$multicheck_array = array(
		'one' => __('French Toast', 'options_framework_theme'),
		'two' => __('Pancake', 'options_framework_theme'),
		'three' => __('Omelette', 'options_framework_theme'),
		'four' => __('Crepe', 'options_framework_theme'),
		'five' => __('Waffle', 'options_framework_theme')
	);

	// Multicheck Defaults
	$multicheck_defaults = array(
		'one' => '1',
		'five' => '1'
	);

	// Background Defaults
	$background_defaults = array(
		'color' => '',
		'image' => '',
		'repeat' => 'repeat',
		'position' => 'top center',
		'attachment'=>'scroll' );

	// Typography Defaults
	$typography_defaults = array(
		'size' => '15px',
		'face' => 'georgia',
		'style' => 'bold',
		'color' => '#bada55' );
		
	// Typography Options
	$typography_options = array(
		'sizes' => array( '6','12','14','16','20' ),
		'faces' => array( 'Helvetica Neue' => 'Helvetica Neue','Arial' => 'Arial' ),
		'styles' => array( 'normal' => 'Normal','bold' => 'Bold' ),
		'color' => false
	);

	// Pull all the categories into an array
	$options_categories = array();
	$options_categories_obj = get_categories();
	foreach ($options_categories_obj as $category) {
		$options_categories[$category->cat_ID] = $category->cat_name;
	}

	// Pull all the pages into an array
	$options_pages = array();
	$options_pages_obj = get_pages('sort_column=post_parent,menu_order');
	$options_pages[''] = 'Select a page:';
	foreach ($options_pages_obj as $page) {
		$options_pages[$page->ID] = $page->post_title;
	}

	// If using image radio buttons, define a directory path
	$imagepath =  get_template_directory_uri() . '/images/';

	$options = array();

	/*$options[] = array(
		'name' => __('Basic Settings', 'options_framework_theme'),
		'type' => 'heading');
		
	$options[] = array(
		'name' => __('Home Page Content', 'options_framework_theme'),
		'desc' => __('Select a page to pull content from', 'options_framework_theme'),
		'id' => 'homepage_content',
		'type' => 'select',
		'options' => $options_pages);
			

	$options[] = array(
		'name' => __('Input Text Mini', 'options_framework_theme'),
		'desc' => __('A mini text input field.', 'options_framework_theme'),
		'id' => 'example_text_mini',
		'std' => 'Default',
		'class' => 'mini',
		'type' => 'text');

	$options[] = array(
		'name' => __('Input Text', 'options_framework_theme'),
		'desc' => __('A text input field.', 'options_framework_theme'),
		'id' => 'example_text',
		'std' => 'Default Value',
		'type' => 'text');

	$options[] = array(
		'name' => __('Textarea', 'options_framework_theme'),
		'desc' => __('Textarea description.', 'options_framework_theme'),
		'id' => 'example_textarea',
		'std' => 'Default Text',
		'type' => 'textarea');

	$options[] = array(
		'name' => __('Input Select Small', 'options_framework_theme'),
		'desc' => __('Small Select Box.', 'options_framework_theme'),
		'id' => 'example_select',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $test_array);

	$options[] = array(
		'name' => __('Input Select Wide', 'options_framework_theme'),
		'desc' => __('A wider select box.', 'options_framework_theme'),
		'id' => 'example_select_wide',
		'std' => 'two',
		'type' => 'select',
		'options' => $test_array);

	$options[] = array(
		'name' => __('Select a Category', 'options_framework_theme'),
		'desc' => __('Passed an array of categories with cat_ID and cat_name', 'options_framework_theme'),
		'id' => 'example_select_categories',
		'type' => 'select',
		'options' => $options_categories);

	$options[] = array(
		'name' => __('Select a Page', 'options_framework_theme'),
		'desc' => __('Passed an pages with ID and post_title', 'options_framework_theme'),
		'id' => 'example_select_pages',
		'type' => 'select',
		'options' => $options_pages);

	$options[] = array(
		'name' => __('Input Radio (one)', 'options_framework_theme'),
		'desc' => __('Radio select with default options "one".', 'options_framework_theme'),
		'id' => 'example_radio',
		'std' => 'one',
		'type' => 'radio',
		'options' => $test_array);

	$options[] = array(
		'name' => __('Example Info', 'options_framework_theme'),
		'desc' => __('This is just some example information you can put in the panel.', 'options_framework_theme'),
		'type' => 'info');

	$options[] = array(
		'name' => __('Input Checkbox', 'options_framework_theme'),
		'desc' => __('Example checkbox, defaults to true.', 'options_framework_theme'),
		'id' => 'example_checkbox',
		'std' => '1',
		'type' => 'checkbox');

	$options[] = array(
		'name' => __('Advanced Settings', 'options_framework_theme'),
		'type' => 'heading');

	$options[] = array(
		'name' => __('Check to Show a Hidden Text Input', 'options_framework_theme'),
		'desc' => __('Click here and see what happens.', 'options_framework_theme'),
		'id' => 'example_showhidden',
		'type' => 'checkbox');
		
	$options[] = array(
		'name' => __('Hidden Text Input', 'options_framework_theme'),
		'desc' => __('This option is hidden unless activated by a checkbox click.', 'options_framework_theme'),
		'id' => 'example_text_hidden',
		'std' => 'Hello',
		'class' => 'hidden',
		'type' => 'text');

	$options[] = array(
		'name' => __('Uploader Test', 'options_framework_theme'),
		'desc' => __('This creates a full size uploader that previews the image.', 'options_framework_theme'),
		'id' => 'example_uploader',
		'type' => 'upload');

	$options[] = array(
		'name' => "Example Image Selector",
		'desc' => "Images for layout.",
		'id' => "example_images",
		'std' => "2c-l-fixed",
		'type' => "images",
		'options' => array(
			'1col-fixed' => $imagepath . '1col.png',
			'2c-l-fixed' => $imagepath . '2cl.png',
			'2c-r-fixed' => $imagepath . '2cr.png')
	);

	$options[] = array(
		'name' =>  __('Example Background', 'options_framework_theme'),
		'desc' => __('Change the background CSS.', 'options_framework_theme'),
		'id' => 'example_background',
		'std' => $background_defaults,
		'type' => 'background' );

	$options[] = array(
		'name' => __('Multicheck', 'options_framework_theme'),
		'desc' => __('Multicheck description.', 'options_framework_theme'),
		'id' => 'example_multicheck',
		'std' => $multicheck_defaults, // These items get checked by default
		'type' => 'multicheck',
		'options' => $multicheck_array);

	$options[] = array(
		'name' => __('Colorpicker', 'options_framework_theme'),
		'desc' => __('No color selected by default.', 'options_framework_theme'),
		'id' => 'example_colorpicker',
		'std' => '',
		'type' => 'color' );
		
	$options[] = array( 'name' => __('Typography', 'options_framework_theme'),
		'desc' => __('Example typography.', 'options_framework_theme'),
		'id' => "example_typography",
		'std' => $typography_defaults,
		'type' => 'typography' );
		
	$options[] = array(
		'name' => __('Custom Typography', 'options_framework_theme'),
		'desc' => __('Custom typography options.', 'options_framework_theme'),
		'id' => "custom_typography",
		'std' => $typography_defaults,
		'type' => 'typography',
		'options' => $typography_options );

	$options[] = array(
		'name' => __('Text Editor', 'options_framework_theme'),
		'type' => 'heading' );
	*/
	
	/**
	 * For $settings options see:
	 * http://codex.wordpress.org/Function_Reference/wp_editor
	 *
	 * 'media_buttons' are not supported as there is no post to attach items to
	 * 'textarea_name' is set by the 'id' you choose
	 */
	/*
	$wp_editor_settings = array(
		'wpautop' => true, // Default
		'textarea_rows' => 5,
		'tinymce' => array( 'plugins' => 'wordpress' )
	);
	
	$options[] = array(
		'name' => __('Default Text Editor', 'options_framework_theme'),
		'desc' => sprintf( __( 'You can also pass settings to the editor.  Read more about wp_editor in <a href="%1$s" target="_blank">the WordPress codex</a>', 'options_framework_theme' ), 'http://codex.wordpress.org/Function_Reference/wp_editor' ),
		'id' => 'example_editor',
		'type' => 'editor',
		'settings' => $wp_editor_settings );
	
	$options[] = array(
		'name' => __('Custom Post Types', 'options_framework_theme'),
		'type' => 'heading' );
		
	$options[] = array(
		'type' => 'custom_post_types',
		'options' => $options_pages ); 
	*/

	$options[] = array(
		'name' => __('Header Settings', 'options_framework_theme'),
		'type' => 'heading');
		
	$options[] = array(
		'name' => __('Logo Image', 'options_framework_theme'),
		'id' => 'logo',
		'type' => 'upload');
    
    
	$options[] = array(
		'name' => __('Success user register message', 'options_framework_theme'),
		'id' => 'reg_msg',
		'type' => 'textarea');
		
	$options[] = array(
		'name' => __('Menu Settings', 'options_framework_theme'),
		'type' => 'heading');
	
	/*What Is ComplianceTest? */
	$options[] = array(
		'name' => __('WHAT IS ComplianceTest?', 'options_framework_theme'),
		'desc' => __('WHAT IS ComplianceTest? - Item Title'),
		'id' => 'what_t',
		'type' => 'text');	

	$options[] = array(
		'desc' => __('WHAT IS ComplianceTest? - Item Icon'),
		'id' => 'what_icon',
		'type' => 'upload');	

	$options[] = array(
		'desc' => __('WHAT IS ComplianceTest? - Item Description'),
		'id' => 'what_d',
		'type' => 'textarea');		
	
	$options[] = array(
		'desc' => __('Issuers - Item Title'),
		'id' => 'issuers_t',
		'type' => 'text');	
		
	$options[] = array(
		'desc' => __('Issuers - Item Icon'),
		'id' => 'issuers_icon',
		'type' => 'upload');	
		
	$options[] = array(
		'desc' => __('Issuers - Item Description'),
		'id' => 'issuers_d',
		'type' => 'textarea');		
		
	$options[] = array(
		'desc' => __('Implementers - Item Title'),
		'id' => 'implementers_t',
		'type' => 'text');		
		
	$options[] = array(
		'desc' => __('Implementers - Item Icon'),
		'id' => 'implementers_icon',
		'type' => 'upload');	
		
	$options[] = array(
		'desc' => __('Implementers - Item Description'),
		'id' => 'implementers_d',
		'type' => 'textarea');			
		
	$options[] = array(
		'desc' => __('Find out How it works - Link To'),
		'id' => 'what_link',
		'type' => 'text');	
		
		
	/*WHY ComplianceTest? */
	$options[] = array(
		'name' => __('WHY ComplianceTest?', 'options_framework_theme'),
		'desc' => __('Community - Item Title'),
		'id' => 'community_t',
		'type' => 'text');	

	$options[] = array(
		'desc' => __('Community - Item Icon'),
		'id' => 'community_icon',
		'type' => 'upload');	

	$options[] = array(
		'desc' => __('Community - Item Description'),
		'id' => 'community_d',
		'type' => 'textarea');		
	
	$options[] = array(
		'desc' => __('Visibility - Item Title'),
		'id' => 'visibility_t',
		'type' => 'text');	
		
	$options[] = array(
		'desc' => __('Visibility - Item Icon'),
		'id' => 'visibility_icon',
		'type' => 'upload');	
		
	$options[] = array(
		'desc' => __('Visibility - Item Description'),
		'id' => 'visibility_d',
		'type' => 'textarea');		
		
	$options[] = array(
		'desc' => __('Support - Item Title'),
		'id' => 'support_t',
		'type' => 'text');		
		
	$options[] = array(
		'desc' => __('Support - Item Icon'),
		'id' => 'support_icon',
		'type' => 'upload');	
		
	$options[] = array(
		'desc' => __('Support - Item Description'),
		'id' => 'support_d',
		'type' => 'textarea');		
		
	$options[] = array(
		'desc' => __('Cost - Item Title'),
		'id' => 'cost_t',
		'type' => 'text');		
		
	$options[] = array(
		'desc' => __('Cost - Item Icon'),
		'id' => 'cost_icon',
		'type' => 'upload');	
		
	$options[] = array(
		'desc' => __('Cost - Item Description'),
		'id' => 'cost_d',
		'type' => 'textarea');	
				
		
	$options[] = array(
		'desc' => __('Confidence - Item Title'),
		'id' => 'confidence_t',
		'type' => 'text');		
		
	$options[] = array(
		'desc' => __('Confidence - Item Icon'),
		'id' => 'confidence_icon',
		'type' => 'upload');	
		
	$options[] = array(
		'desc' => __('Confidence - Item Description'),
		'id' => 'confidence_d',
		'type' => 'textarea');		
		
	$options[] = array(
		'desc' => __('Find out more reasons - Link To'),
		'id' => 'why_link',
		'type' => 'text');		
		
	/* ComplianceTest SERVICES */
	$options[] = array(
		'name' => __('ComplianceTest SERVICES', 'options_framework_theme'),
		'desc' => __('Test Suites - Item Title'),
		'id' => 'testsuites_t',
		'type' => 'text');	

	$options[] = array(
		'desc' => __('Test Suites - Item Description'),
		'id' => 'testsuites_d',
		'type' => 'textarea');		
		
	$options[] = array(
		'desc' => __('Test Suites - Link To'),
		'id' => 'testsuites_linkto',
		'type' => 'text');		
	
	$options[] = array(
		'desc' => __('Collaboration - Item Title'),
		'id' => 'collaboration_t',
		'type' => 'text');	
	
	$options[] = array(
		'desc' => __('Collaboration - Item Description'),
		'id' => 'collaboration_d',
		'type' => 'textarea');	
		
	$options[] = array(
		'desc' => __('Collaboration - Link To'),
		'id' => 'collaboration_linkto',
		'type' => 'text');		
		
	$options[] = array(
		'desc' => __('Product Repository - Item Title'),
		'id' => 'productrep_t',
		'type' => 'text');		

	$options[] = array(
		'desc' => __('Product Repository - Item Description'),
		'id' => 'productrep_d',
		'type' => 'textarea');		
		
	$options[] = array(
		'desc' => __('Product Repository - Link To'),
		'id' => 'productrep_linkto',
		'type' => 'text');	
		
	$options[] = array(
		'desc' => __('Test Harness - Item Title'),
		'id' => 'testharness_t',
		'type' => 'text');		
		
	$options[] = array(
		'desc' => __('Test Harness - Link To'),
		'id' => 'testharness_linkto',
		'type' => 'text');	
		
	$options[] = array(
		'desc' => __('Test Harness - Item Description'),
		'id' => 'testharness_d',
		'type' => 'textarea');	
	
	/* HELP & FAQ */	
	$options[] = array(
		'name' => __('HELP & FAQ', 'options_framework_theme'),
		'desc' => __('How it works - Item Title'),
		'id' => 'how_t',
		'type' => 'text');		
		
	$options[] = array(
		'desc' => __('How it works - Description'),
		'id' => 'how_desc',
		'type' => 'text');	
		
	$options[] = array(
		'desc' => __('How it works - Link To'),
		'id' => 'how_linkto',
		'type' => 'text');		

	$options[] = array(
		'desc' => __('FAQ - Item Title'),
		'id' => 'faq_t',
		'type' => 'text');		
		
	$options[] = array(
		'desc' => __('FAQ - Description'),
		'id' => 'faq_desc',
		'type' => 'text');	
		
	$options[] = array(
		'desc' => __('FAQ - Link To'),
		'id' => 'faq_linkto',
		'type' => 'text');	
		
	$options[] = array(
		'desc' => __('Documentation - Item Title'),
		'id' => 'documentation_t',
		'type' => 'text');		
		
	$options[] = array(
		'desc' => __('Documentation - Description'),
		'id' => 'documentation_desc',
		'type' => 'text');			
		
	$options[] = array(
		'desc' => __('Documentation - Link To'),
		'id' => 'documentation_linkto',
		'type' => 'text');			

	$options[] = array(
		'desc' => __('Forum - Item Title'),
		'id' => 'forum_t',
		'type' => 'text');		
		
	$options[] = array(
		'desc' => __('Forum - Description'),
		'id' => 'forum_desc',
		'type' => 'text');	
		
	$options[] = array(
		'desc' => __('Forum - Link To'),
		'id' => 'forum_linkto',
		'type' => 'text');		

		
	$options[] = array(
		'name' => __('Homepage Settings', 'options_framework_theme'),
		'type' => 'heading');
		
	$options[] = array(
		'name' => __('Two boxes', 'options_framework_theme'),
		'desc' => __('LEFT Box Register Content'),
		'id' => 'lregister_box_content',
		'type' => 'text');
		
	$options[] = array(
		'desc' => __('LEFT Box Register Link Content'),
		'id' => 'lregister_box_link_content',
		'type' => 'text');		
		
	$options[] = array(
		'desc' => __('RIGHT Box Register Content'),
		'id' => 'rregister_box_content',
		'type' => 'text');
		
	$options[] = array(
		'desc' => __('RIGHT Box Register Link Content'),
		'id' => 'rregister_box_link_content',
		'type' => 'text');	
		
	$options[] = array(
		'name' => __('Three boxes', 'options_framework_theme'),
		'desc' => __('1. Box Image'),
		'id' => 'box_image1',
		'type' => 'upload');	
		
	$options[] = array(
		'desc' => __('1. Box Title'),
		'id' => 'box_title1',
		'type' => 'text');
		
	$options[] = array(
		'desc' => __('1. Box - Link to'),
		'id' => 'box_1_linkto',
		'type' => 'text');	
	
	$options[] = array(
		'desc' => __('1. Box Item 1.'),
		'id' => '1box_item1',
		'type' => 'text');
	
	$options[] = array(
		'desc' => __('1. Box Item 1 - Link To.'),
		'id' => '1box_item1_linkto',
		'type' => 'text');
		
	$options[] = array(
		'desc' => __('1. Box Item 2.'),
		'id' => '1box_item2',
		'type' => 'text');
	
	$options[] = array(
		'desc' => __('1. Box Item 2 - Link To.'),
		'id' => '1box_item2_linkto',
		'type' => 'text');		
		
	$options[] = array(
		'desc' => __('1. Box Item 3.'),
		'id' => '1box_item3',
		'type' => 'text');
	
	$options[] = array(
		'desc' => __('1. Box Item 3 - Link To.'),
		'id' => '1box_item3_linkto',
		'type' => 'text');		
	
			
	$options[] = array(
		'desc' => __('2. Box Image'),
		'id' => 'box_image2',
		'type' => 'upload');	
		
	$options[] = array(
		'desc' => __('2. Box Title'),
		'id' => 'box_title2',
		'type' => 'text');
		
	$options[] = array(
		'desc' => __('2. Box - Link to'),
		'id' => 'box_2_linkto',
		'type' => 'text');	
		
		$options[] = array(
		'desc' => __('2. Box Item 1.'),
		'id' => '2box_item1',
		'type' => 'text');
	
	$options[] = array(
		'desc' => __('2. Box Item 1 - Link To.'),
		'id' => '2box_item1_linkto',
		'type' => 'text');
		
	$options[] = array(
		'desc' => __('2. Box Item 2.'),
		'id' => '2box_item2',
		'type' => 'text');
	
	$options[] = array(
		'desc' => __('2. Box Item 2 - Link To.'),
		'id' => '2box_item2_linkto',
		'type' => 'text');		
		
	$options[] = array(
		'desc' => __('2. Box Item 3.'),
		'id' => '2box_item3',
		'type' => 'text');
	
	$options[] = array(
		'desc' => __('2. Box Item 3 - Link To.'),
		'id' => '2box_item3_linkto',
		'type' => 'text');		
		
	$options[] = array(
		'desc' => __('3. Box Image'),
		'id' => 'box_image3',
		'type' => 'upload');	
	
	$options[] = array(
		'desc' => __('3. Box Title'),
		'id' => 'box_title3',
		'type' => 'text');	
		
	$options[] = array(
		'desc' => __('3. Box - Link to'),
		'id' => 'box_3_linkto',
		'type' => 'text');		
		
	/* */
	$options[] = array(
		'desc' => __('3. Box Item 1.'),
		'id' => '3box_item1',
		'type' => 'text');
	
	$options[] = array(
		'desc' => __('3. Box Item 1 - Link To.'),
		'id' => '3box_item1_linkto',
		'type' => 'text');
		
	$options[] = array(
		'desc' => __('3. Box Item 2.'),
		'id' => '3box_item2',
		'type' => 'text');
	
	$options[] = array(
		'desc' => __('3. Box Item 2 - Link To.'),
		'id' => '3box_item2_linkto',
		'type' => 'text');		
		
	$options[] = array(
		'desc' => __('3. Box Item 3.'),
		'id' => '3box_item3',
		'type' => 'text');
	
	$options[] = array(
		'desc' => __('3. Box Item 3 - Link To.'),
		'id' => '3box_item3_linkto',
		'type' => 'text');			
		
			
	$options[] = array(
		'name' => __('Footer Settings', 'options_framework_theme'),
		'type' => 'heading');
		
	$options[] = array(
		'name' => __('Partners Logos', 'options_framework_theme'),
		'desc' => __('1. Partner logo'),
		'id' => 'plogo1',
		'type' => 'upload');		
		
	$options[] = array(
		'desc' => __('2. Partner logo'),
		'id' => 'plogo2',
		'type' => 'upload');		
		
	$options[] = array(
		'desc' => __('3. Partner logo'),
		'id' => 'plogo3',
		'type' => 'upload');	
		
	$options[] = array(
		'desc' => __('4. Partner logo'),
		'id' => 'plogo4',
		'type' => 'upload');	
		
	$options[] = array(
		'desc' => __('5. Partner logo'),
		'id' => 'plogo5',
		'type' => 'upload');
		
	$options[] = array(
		'desc' => __('Copyright text'),
		'id' => 'copyright',
		'type' => 'text');			

	return $options;
}

/*
 * This is an example of how to add custom scripts to the options panel.
 * This example shows/hides an option when a checkbox is clicked.
 */

add_action('optionsframework_custom_scripts', 'optionsframework_custom_scripts');

function optionsframework_custom_scripts() { ?>

<script type="text/javascript">
jQuery(document).ready(function($) {

	$('#example_showhidden').click(function() {
  		$('#section-example_text_hidden').fadeToggle(400);
	});

	if ($('#example_showhidden:checked').val() !== undefined) {
		$('#section-example_text_hidden').show();
	}

});
</script>

<?php
}
