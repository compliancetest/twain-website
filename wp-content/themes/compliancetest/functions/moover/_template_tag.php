<?php
/**
 * ------- mOover Custom Template Tag ------- 
*/

function moover($id) {
	global $wpdb;
	$moover_result = $wpdb->get_row('SELECT * FROM ' . MOOVER_TABLE_NAME . ' WHERE moover_id =' . $id);
	$wpdb->flush();
	
	if (!$moover_result) return;
		
	//Moover's CSS
	echo '<link rel="stylesheet" href="'.MOOVER_PLUGIN_URL.'moover/jquery.id.moover-' . MOOVER_VERSION_CORE . '.css'.'"/>';
	
	//JS
	wp_register_script('jquery.id.moover-'.MOOVER_VERSION_CORE.'.min.js', MOOVER_PLUGIN_URL . 'moover/jquery.id.moover-'.MOOVER_VERSION_CORE.'.min.js', array('jquery'));
	wp_print_scripts('jquery.id.moover-'.MOOVER_VERSION_CORE.'.min.js');
	wp_register_script('moover_script_'.$id.'.js', MOOVER_PLUGIN_URL . 'get_script/?id='.$id, array('jquery'), $moover_result->version);
	wp_print_scripts('moover_script_'.$id.'.js');
	
	//Echo mOover's HTML
	echo getMooverHTML($moover_result);
	
}
?>