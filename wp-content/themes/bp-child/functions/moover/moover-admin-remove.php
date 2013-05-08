<?php
/* ----- Function to remove mOover ----- */
global $moover_status;
global $moover_status_class;
moover_remove_slider( $_GET['remove_moover'] );
function moover_remove_slider ( $moover_id ) {
	global $wpdb;
	global $moover_status;
	global $moover_status_class;
	$delete_moover = $wpdb->query(
		"
		DELETE FROM " . MOOVER_TABLE_NAME . " 
		WHERE moover_id = '" . $_GET['remove_moover'] . "'
		"
	);
	if( $delete_moover ) {
		 $moover_status = 'mOover ID "' . $_GET['remove_moover'] . '" was successfully removed!';
		$moover_status_class = 'updated';
	}
	else {
		$moover_status = 'Error occured while removing mOover ID "' . $_GET['remove_moover'] . '" ! Seems to be you are trying to remove already removed mOover!';
		$moover_status_class = 'error';
		return;
	};
}
?>