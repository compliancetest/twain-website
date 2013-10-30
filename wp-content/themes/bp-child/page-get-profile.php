<?php
/***
* Get Test Data and Echo
*/
$token = isset($_GET['id']) ? $_GET['id'] : '';

$query = $wpdb->prepare("SELECT * FROM $wpdb->prefix" . "community_profile_instances WHERE token=%s", $token);

$row = $wpdb->get_row($query);

if(!$row)
{
    echo 'Invalid Request';
}else{
    header('content-type: text/json');
    echo base64_decode($row->content);
}