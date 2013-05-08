<?php
$removeDB = get_option('moover-settings');
	global $wpdb;
	$mooverTableName = $wpdb->prefix . "id_moover";
	$sql = "DROP TABLE " . $mooverTableName;
	$wpdb->query($sql);
	delete_option('moover-settings');
?>