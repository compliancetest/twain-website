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
    
	/*$options[] = array(
		'name' => __('Inactive user login message', 'options_framework_theme'),
		'id' => 'log_msg',
		'type' => 'textarea');*/
		
	$options[] = array(
		'name' => __('Menu Settings', 'options_framework_theme'),
		'type' => 'heading');
	
	/*What Is ComplianceTest? */    
    $options[] = array(
        'name' => __('WHAT IS ComplianceTest?', 'options_framework_theme'),
        'desc' => __('More Information - Link'),
        'id' => 'what_is_compliancetest_more_link',
        'type' => 'text');
	$options[] = array(		
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
	
		
	/*WHY ComplianceTest? */
    
	$options[] = array(
        'name' => __('WHY ComplianceTest?', 'options_framework_theme'),
        'desc' => __('More Information - Link'),
        'id' => 'why_compliancetest_more_link',
        'type' => 'text');
    $options[] = array(		
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
		
	/* ComplianceTest SERVICES */
    $options[] = array(
        'name' => __('ComplianceTest SERVICES', 'options_framework_theme'),
        'desc' => __('More Information - Link'),
        'id' => 'compliancetest_service_more_link',
        'type' => 'text');
	$options[] = array(		
		'desc' => __('Test Suites - Item Title'),
		'id' => 'testsuites_t',
		'type' => 'text');	
    $options[] = array(
        'desc' => __('Test Suites - Item Icon'),
        'id' => 'testsuites_icon',
        'type' => 'upload');        
	$options[] = array(
		'desc' => __('Test Suites - Item Description'),
		'id' => 'testsuites_d',
		'type' => 'textarea');		
	
    
	$options[] = array(
		'desc' => __('Collaboration - Item Title'),
		'id' => 'collaboration_t',
		'type' => 'text');	
	$options[] = array(
        'desc' => __('Collaboration - Icon'),
        'id' => 'collaboration_icon',
        'type' => 'upload');        
	$options[] = array(
		'desc' => __('Collaboration - Item Description'),
		'id' => 'collaboration_d',
		'type' => 'textarea');	
		
	
		
	$options[] = array(
		'desc' => __('Product Repository - Item Title'),
		'id' => 'productrep_t',
		'type' => 'text');		
    $options[] = array(
        'desc' => __('Product Repository - Icon'),
        'id' => 'productrep_icon',
        'type' => 'upload');    
	$options[] = array(
		'desc' => __('Product Repository - Item Description'),
		'id' => 'productrep_d',
		'type' => 'textarea');		
		
	
		
	$options[] = array(
		'desc' => __('Test Harness - Item Title'),
		'id' => 'testharness_t',
		'type' => 'text');		
		
	$options[] = array(
		'desc' => __('Test Harness - Icon'),
		'id' => 'testharness_icon',
		'type' => 'upload');	
		
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
        'desc' => __('How it works - Icon'),
        'id' => 'how_icon',
        'type' => 'upload');        
	$options[] = array(
		'desc' => __('How it works - Description'),
		'id' => 'how_desc',
		'type' => 'textarea');
	$options[] = array(
		'desc' => __('How it works - Link To'),
		'id' => 'how_linkto',
		'type' => 'text');		

    
	$options[] = array(
		'desc' => __('FAQ - Item Title'),
		'id' => 'faq_t',
		'type' => 'text');		
	$options[] = array(
        'desc' => __('FAQ - Icon'),
        'id' => 'faq_icon',
        'type' => 'upload');    
	$options[] = array(
		'desc' => __('FAQ - Description'),
		'id' => 'faq_desc',
		'type' => 'textarea');
	$options[] = array(
		'desc' => __('FAQ - Link To'),
		'id' => 'faq_linkto',
		'type' => 'text');
        
        
	$options[] = array(
		'desc' => __('Documentation - Item Title'),
		'id' => 'documentation_t',
		'type' => 'text');		
	$options[] = array(
        'desc' => __('Documentation - Icon'),
        'id' => 'documentation_icon',
        'type' => 'upload');	
	$options[] = array(
		'desc' => __('Documentation - Description'),
		'id' => 'documentation_desc',
		'type' => 'textarea');
	$options[] = array(
		'desc' => __('Documentation - Link To'),
		'id' => 'documentation_linkto',
		'type' => 'text');			

	$options[] = array(
		'desc' => __('Forum - Item Title'),
		'id' => 'forum_t',
		'type' => 'text');		
	$options[] = array(
        'desc' => __('Forum - Icon'),
        'id' => 'forum_icon',
        'type' => 'upload');    	
	$options[] = array(
		'desc' => __('Forum - Description'),
		'id' => 'forum_desc',
		'type' => 'textarea');	
		
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
        'name' => __('Community Boxes', 'options_framework_theme'),
        'desc' => __('1. Box Title'),
        'id' => 'box_title1',
        'type' => 'text');    
    $options[] = array(
        'desc' => __('1. Box Image'),
        'id' => 'box_image1',
        'type' => 'upload');
    $options[] = array(
        'desc' => __('1. Box Content Title'),
        'id' => 'box_content_title1',
        'type' => 'text');
    $options[] = array(
        'desc' => __('1. Box Link To.'),
        'id' => 'box_linkto1',
        'type' => 'text');    
    $options[] = array(
        'desc' => __('1. Box Content'),
        'id' => 'box_content1',
        'type' => 'textarea');
    $options[] = array(
        'desc' => __('2. Box Title'),
        'id' => 'box_title2',
        'type' => 'text');    
    $options[] = array(
        'desc' => __('2. Box Image'),
        'id' => 'box_image2',
        'type' => 'upload');
    $options[] = array(
        'desc' => __('2. Box Content Title'),
        'id' => 'box_content_title2',
        'type' => 'text');
    $options[] = array(
        'desc' => __('2. Box Link To.'),
        'id' => 'box_linkto2',
        'type' => 'text');  
    $options[] = array(
        'desc' => __('2. Box Content'),
        'id' => 'box_content2',
        'type' => 'textarea');
			
	$options[] = array(
		'name' => __('Footer Settings', 'options_framework_theme'),
		'type' => 'heading');
		
	$options[] = array(
		'desc' => __('Twitter Username'),
		'id' => 'twitter_username',
		'type' => 'text');		
		
	$options[] = array(
        'desc' => __('Copyright text'),
        'id' => 'copyright',
        'type' => 'text');            

    $options[] = array(
		'desc' => __('Google Analytics Code'),
		'id' => 'google-analytics-code',
		'type' => 'textarea');			

        
    $options[] = array(
        'name'  => __('Contact Us Page'),
        'type'  =>  'heading'
    );
    
    $options[] = array(
        'name'  => 'Information in United States',
        'id'    =>  'us_company_name',
        'desc'  =>  'Company Name',
        'type'  =>  'text'
    );
    $options[] = array(
        'id'    =>  'us_company_address',
        'desc'  =>  'Company Address',
        'type'  =>  'textarea'
    );
    $options[] = array(
        'id'    =>  'us_company_phone',
        'desc'  =>  'Phone Number',
        'type'  =>  'text'
    );
    $options[] = array(
        'name'  => 'Information in Australia',
        'id'    =>  'au_company_name',
        'desc'  =>  'Company Name',
        'type'  =>  'text'
    );
    $options[] = array(
        'id'    =>  'au_company_address',
        'desc'  =>  'Company Address',
        'type'  =>  'textarea'
    );
    $options[] = array(
        'id'    =>  'au_company_phone',
        'desc'  =>  'Phone Number',
        'type'  =>  'text'
    );

    $options[] = array(
        'name'  => __('404 Page Settings'),
        'type'  =>  'heading'
    );

    $options[] = array(
        'id'    =>  '404_title',
        'type'  =>  'text',
        'desc'  => 'Title'
    );
    $options[] = array(
        'id'    =>  '404_description',
        'desc'  =>  'Description',
        'type'  =>  'textarea'
    );

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
