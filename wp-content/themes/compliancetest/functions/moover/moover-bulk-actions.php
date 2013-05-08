<?php

global $moover_status;
global $moover_status_class;
/* ----- Remove Sliders ----- */
if ( $_POST['moover-action'] == "delete" ) {
	if ( in_array( "all", $_POST['moover-id'] ) ) {
		foreach ( $_POST['moover-id'] as $moover_id ) {
			if ( $moover_id != "all") 
				moover_remove_sliders ( $moover_id );	
		}
	}
	else {
		foreach ( $_POST['moover-id'] as $moover_id ) {
			moover_remove_sliders ( $moover_id );	
		}
	}
}
/* ----- Copy Sliders ----- */
if ( $_POST['moover-action'] == "copy" ) {
	if ( in_array( "all", $_POST['moover-id'] ) ) {
		foreach ( $_POST['moover-id'] as $moover_id ) {
			if ( $moover_id != "all") 
				moover_copy_sliders ( $moover_id );	
		}
	}
	else {
		foreach ( $_POST['moover-id'] as $moover_id ) {
			moover_copy_sliders ( $moover_id );	
		}
	}	
	
}
function moover_copy_sliders ( $moover_id ) {
	global $wpdb;
	global $moover_status;
	global $moover_status_class;
	
	$oldSlider = $wpdb->get_row('SELECT * FROM ' . MOOVER_TABLE_NAME . ' where moover_id =' . $moover_id, ARRAY_A);
	unset($oldSlider['moover_id']);
	$oldSlider['title'] = $oldSlider['title']." Copy";
	$oldSlider['created'] = current_time('mysql');
	$oldSlider['version'] = 1;
	$newOptions = unserialize($oldSlider['options']);
	$newOptions['title'] = $oldSlider['title'];
	$oldSlider['options'] = serialize ( $newOptions );
	$new_moover_row = $wpdb->insert( 
		MOOVER_TABLE_NAME, 
		$oldSlider 
	);
	if( $new_moover_row ) {
		$moover_status .= 'Congratulations! New mOover was successfully added!<br/>';
		$moover_status_class = 'updated';
	}
	else {
		$moover_status .= 'Error occured while adding new mOover!<br/>';
		$moover_status_class = 'error';
	};
		
}

function moover_remove_sliders ( $moover_id ) {
	global $wpdb;
	global $moover_status;
	global $moover_status_class;
	$delete_moover = $wpdb->query(
		"
		DELETE FROM " . MOOVER_TABLE_NAME . " 
		WHERE moover_id = '" . $moover_id . "'
		"
	);
	if( $delete_moover ) {
		 $moover_status .= 'mOover ID "' . $moover_id . '" was successfully removed! <br />';
		$moover_status_class = 'updated';
	}
	else {
		$moover_status .= 'Error occured while removing mOover ID "' . $moover_id . '" ! Seems to be you are trying to remove already removed mOover! <br />';
		$moover_status_class = 'error';
		return;
	};
	$wpdb->flush();
}
?>