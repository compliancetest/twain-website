<?php
/*
Plugin Name: iDangero.us mOover
Plugin URI: http://www.idangero.us/sliders/moover/
Description: Ultra slick & fancy jQuery content slider with amazing CSS3 effects
Version: 1.0
Author: Vladimir Kharlampidi, The iDangero.us
Author URI: http://www.idangero.us/
*/

/*  
Copyright 2012 Vladimir Kharlampidi, The iDangero.us  (email: info@idangero.us)
*/


define('MOOVER_PLUGIN_URL', get_bloginfo('template_url').'/functions/moover/');
define('MOOVER_VERSION_WP','1.0');
define('MOOVER_VERSION_CORE','1.4');
define('MOOVER_VERSION_TF','1.0');
global $wpdb;
define('MOOVER_TABLE_NAME', $wpdb->prefix . "id_moover");
$wpdb->flush();

// Init Chop Slider admin menus and pages
add_action('admin_menu', 'moover_admin_menu');

// Init Chop Slider Settings
add_action('admin_init', 'moover_settings');
function moover_settings() {
	register_setting( 'moover_settings', 'moover-settings' );
	add_settings_section('moover_settings_section', 'mOover Settings', 'moover_settings_html', 'general');
	/* add_settings_field('moover_remove_db', '<p>Remove mOover database on plugin delete:</p><span class="cs-tip">Set to "Do not remove" if you are going to remove mOover before update to the new version</span>', 'cs_setting_removeDB', 'general', 'moover_settings_section'); */
	add_settings_field('moover_permissions', '<p>Permissions:</p><span class="cs-tip">Switch to "Administrator" if you are using Multiple Sites and want to allow for the sites administrators to mananage sliders.</span>', 'cs_setting_permissions', 'general', 'moover_settings_section');
	
	$testOption = get_option('moover-settings');
	if(!is_array($testOption)) {
		add_option( 'moover-settings', Array('remove_db'=>1, 'permissions'=>'edit_plugins') );	
	}
}
function moover_settings_html() {
		
}
/* function cs_setting_removeDB() {
	$options = get_option('moover-settings');
	echo '<select name="moover-settings[remove_db]">
	<option value="1" '.($options['remove_db']==1 ? 'selected="selected"' : "").'>Remove</option>
	<option value="0" '.($options['remove_db']==0 ? 'selected="selected"' : "").'>Do not remove</option>
	</select>
	';
} */
function cs_setting_permissions() {
	$options = get_option('moover-settings');
	echo '<select name="moover-settings[permissions]">
	<option value="edit_plugins" '.($options['permissions']=='edit_plugins' ? 'selected="selected"' : "").'>Super Admin</option>
	<option value="delete_pages" '.($options['permissions']=='delete_pages' ? 'selected="selected"' : "").'>Administrator</option>
	</select>
	';
}

// Chop Slider Admin Pages
function moover_admin_menu() {
	$opt = get_option('moover-settings');
	$role = $opt['permissions'];
    // Add a new top-level Chop Slider's page:
    $chop_slider_manage_page = add_menu_page('mOover slider', 'mOover slider', $role, 'moover', 'moover_manage_page', get_bloginfo('template_url'). "/functions/moover/images/admin/micon.png");
	add_action('admin_print_styles-' . $chop_slider_manage_page, 'moover_admin_init');
	
    // Add a submenu for the "Add New" page:
    $chop_slider_addnew_page = add_submenu_page('moover', 'Add New mOover', 'Add New', $role, 'moover-add', 'moover_add_page');
	add_action('admin_print_styles-' . $chop_slider_addnew_page, 'moover_medialibrary');
	
	// Add a submenu for the "Edit" page:
    $chop_slider_edit_page = add_submenu_page('null', 'Edit mOover', 'Edit mOover', $role, 'moover-edit', 'moover_edit_page');
	add_action('admin_print_styles-' . $chop_slider_edit_page, 'moover_medialibrary');
	
}
// Scripts and Styles for all Custom Pages
function moover_admin_init(){
	wp_register_style('moover-admin.css', MOOVER_PLUGIN_URL . 'moover-admin.css', array(), MOOVER_VERSION_WP);
	wp_enqueue_style('moover-admin.css');
	wp_register_script('moover-admin.js', MOOVER_PLUGIN_URL . 'moover-admin.js', array('jquery'), MOOVER_VERSION_WP);
	wp_enqueue_script('moover-admin.js');
}

// Scripts and Styles for the Add and Edit pages with a medialibrary and TinyMCE
function moover_medialibrary() {
	moover_admin_init();
		
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_enqueue_script('jquery');
	wp_enqueue_style('thickbox');
	wp_register_style('jquery.id.moover-'.MOOVER_VERSION_CORE.'.css', MOOVER_PLUGIN_URL . 'moover/jquery.id.moover-'.MOOVER_VERSION_CORE.'.css', array(), MOOVER_VERSION_WP);
	wp_enqueue_style('jquery.id.moover-'.MOOVER_VERSION_CORE.'.css');
		
	wp_register_script('jquery.id.moover-'.MOOVER_VERSION_CORE.'.min.js', MOOVER_PLUGIN_URL . 'moover/jquery.id.moover-'.MOOVER_VERSION_CORE.'.min.js', array('jquery'), MOOVER_VERSION_WP);
	wp_enqueue_script('jquery.id.moover-'.MOOVER_VERSION_CORE.'.min.js');
	//ColorPicker
	wp_register_style('colorpicker.css', MOOVER_PLUGIN_URL . 'colorpicker/css/colorpicker.css', array());
	wp_enqueue_style('colorpicker.css');
	wp_register_script('colorpicker.js', MOOVER_PLUGIN_URL . 'colorpicker/js/colorpicker.js', array('jquery'));
	wp_enqueue_script('colorpicker.js');
	//UI
	wp_register_style('jquery-ui-1.8.18.custom.css', MOOVER_PLUGIN_URL . 'jqueryui/css/ui-lightness/jquery-ui-1.8.18.custom.css', array(), MOOVER_VERSION_WP);
	wp_enqueue_style('jquery-ui-1.8.18.custom.css');
	wp_register_script('jquery-ui-1.8.18.custom.min.js', MOOVER_PLUGIN_URL . 'jqueryui/js/jquery-ui-1.8.18.custom.min.js', array('jquery'), MOOVER_VERSION_WP);
	wp_enqueue_script('jquery-ui-1.8.18.custom.min.js');
	
}

// Moover Management (Home) Page
function moover_manage_page() {
    include_once dirname( __FILE__ ) . "/moover-admin-manage.php";
}

// Moover "Add " Page
function moover_add_page() {
    include_once dirname( __FILE__ ) . "/moover-admin-add.php";
}
// Moover "Edit" Page
function moover_edit_page() {
    include_once dirname( __FILE__ ) . "/moover-admin-edit.php";
}

// New Data Table If Not Created
function moover_add_new_table() {
	global $wpdb;
	$mv_table_name = MOOVER_TABLE_NAME;
	if($wpdb->get_var("SHOW TABLES LIKE '$mv_table_name'") != $mv_table_name) {
		$sql = "CREATE TABLE " . $mv_table_name . " (
			  `moover_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			  `title` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
			  `options` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
			  `version` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
			  `created` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
			)  CHARACTER SET utf8 COLLATE utf8_general_ci;";
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
};
moover_add_new_table();

include('_shortcode.php');
include('_template_tag.php');
/**
 * ------------------ Function to Get HTML Content of Slides ------------------- 
*/
function getMooverHTML($res) {
	$moover = unserialize($res->options);
	if ( strpos($moover['width'],'%')>0 ) {
		$width = $moover['width'];	
	}
	else {
		$width = $moover['width'].'px';	
	}
	$html = '<div class="moover moover_'.$res->moover_id.'" style="width:'.$width.'; height: '.$moover['height'].'px">
';
	foreach($moover['slides'] as $slide) {
		$html.=$slide['html'].'';		
	}
	if ($moover['stopButton'] == 'a.slider_pause' ) {
	$html.='<div class="control slider_loaded"><a href="javascript:void(0);" class="slider_pause slider_pause_'.$res->moover_id.'"></a><a href="javascript:void(0);" class="slider_play slider_play_'.$res->moover_id.' psb_inactive"></a></div>';
	}
	if ($moover['navigation']==='true') {
		$html.='<div class="moover-pagination moover-pagination_'.$res->moover_id.'"></div>';
	}
	$html.='<div class="slider_loader"></div></div>';	
	return $html;
};
?>