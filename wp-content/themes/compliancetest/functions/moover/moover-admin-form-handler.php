<?php

function mooverReplaceQuotes($a) {
	return str_replace(array("\'", '\"'), array("'", '"'), $a);
};

$newMoover = array();

/* Main Parameters */
$newMoover['title'] = mooverReplaceQuotes($_POST['moover-title']);
$newMoover['width'] = $_POST['moover-width'];
$newMoover['height'] = $_POST['moover-height'];
/* Parameters */
//$newMoover['manualMode']=$_POST['manualMode'];
$newMoover['moveImage']=$_POST['moveImage'];
$newMoover['moveTime']=$_POST['moveTime'];
$newMoover['moveWidth']=$_POST['moveWidth'];
$newMoover['slideTime']=$_POST['slideTime'];
$newMoover['scaleImage']=$_POST['scaleImage'];
$newMoover['preloader']=$_POST['preloader'];
/*Navigation*/
$newMoover['navigation']=$_POST['navigation'];
$newMoover['navigationActive']=$_POST['navigationActive'];
$newMoover['stopButton']=$_POST['stopButton'];
$newMoover['playButton']=$_POST['playButton'];
$newMoover['nextButton']=$_POST['nextButton'];
$newMoover['prevButton']=$_POST['prevButton'];
/* Pro Settings */
$newMoover['disableCSS3']=$_POST['disableCSS3'];
$newMoover['onStart']=$_POST['onStart'];
$newMoover['onSlideSwitch']=$_POST['onSlideSwitch'];
$newMoover['onSlideSwitchEnd']=$_POST['onSlideSwitchEnd'];

/* Slides HTML and JS */
$newMoover['slides'] = array();
foreach ($_POST['moover-html'] as $key=>$html) {
	$html = mooverReplaceQuotes($html);
	$js = mooverReplaceQuotes($_POST['moover-js'][$key]);
	$newMoover['slides'][]=array('html'=>$html, 'js'=>$js);
}
if( isset( $_POST['create-moover'] ) ) {
	/*
	Now we need to add all information into DB table
	*/
	global $wpdb;
	$new_moover_row = $wpdb->insert( 
		MOOVER_TABLE_NAME, 
		array( 'title' => $newMoover['title'], 'options' => serialize($newMoover), 'version' => '1', 'created' => current_time('mysql') ) 
	);
	if( $new_moover_row ) {
		$moover_status = 'mOover "' . $newMoover['title'] . '" was successfully added!';
		$moover_status_class = 'updated';
	}
	else {
		$moover_status = 'Error occured while adding mOover "' . $newMoover['title'] . '" !';
		$moover_status_class = 'error';
	};
	$wpdb->flush();
	
};

if( isset( $_POST['save-edited-moover'] ) ) {
	/*
	Now we need to update all information into DB table
	*/
	global $wpdb;
	$update_moover_row = $wpdb->update( 
		MOOVER_TABLE_NAME, 
		array( 'title' => $newMoover['title'], 'options' => serialize($newMoover), 'version' => $_POST['moover-new-version'], 'created' => current_time('mysql') ), 
		array( 'moover_id' => $_POST['moover_id'] ) 
	);
	$wpdb->flush();
	
	if( $update_moover_row ) {
		$moover_status = 'mOover "' . $newMoover['title'] . '" was successfully updated!';
		$moover_status_class = 'updated';
	}
	else {
		$moover_status = 'Error occured while updating mOover "' . $newMoover['title'] . '" !';
		$moover_status_class = 'error';
	};
	
}
?>