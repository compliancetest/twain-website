<?php
/**
 * ------- Moover Short Code ------- 
**/
class Moover_Shortcode {
	static $add_script;
	function init() {
		add_shortcode('moover', array(__CLASS__, 'handle_moover_shortcode'));
	}	
	function handle_moover_shortcode($atts) {
		extract(shortcode_atts(array( "id" => '' ), $atts));
		global $wpdb;
		$moover_result = $wpdb->get_row('SELECT * FROM ' . MOOVER_TABLE_NAME . ' WHERE moover_id =' . $id);
		if (!$moover_result) return;
		
		//Moover's CSS
		echo '<link rel="stylesheet" href="'.MOOVER_PLUGIN_URL.'moover/jquery.id.moover-' . MOOVER_VERSION_CORE . '.css'.'"/>';
		//JS
		wp_register_script('jquery.id.moover-'.MOOVER_VERSION_CORE.'.min.js', MOOVER_PLUGIN_URL . 'moover/jquery.id.moover-'.MOOVER_VERSION_CORE.'.min.js', array('jquery'));
		wp_print_scripts('jquery.id.moover-'.MOOVER_VERSION_CORE.'.min.js');
		wp_register_script('moover_script_'.$id.'.js', MOOVER_PLUGIN_URL . 'get_script/?id='.$id, array('jquery'), $moover_result->version);
		wp_print_scripts('moover_script_'.$id.'.js');
		
		//Return mOover's HTML
		return getMooverHTML($moover_result);
	}
}
Moover_Shortcode::init();
?>