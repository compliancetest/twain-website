<?php
/***
* Get Test Data and Echo
*/
function getMetaParameter($mode = 'key') {
    foreach ($_REQUEST as $key => $value) {
        if (!in_array($key, array('user', 'password', 'tester'))) {
            if ($mode == 'key')
                return $key;
            else
                return $value;
        }
    }
    return null;
}

$token = isset($_GET['id']) ? $_GET['id'] : '';
if ($token) {
    $query = $wpdb->prepare("SELECT * FROM $wpdb->prefix" . "community_profile_instances WHERE token=%s", $token);
    $row = $wpdb->get_row($query);
} else {
    $user = isset($_GET['user']) ? $_GET['user'] : '';
    $password = isset($_GET['password']) ? $_GET['password'] : '';
    $meta_key = getMetaParameter('key');
    $meta_value = getMetaParameter('value');
    if ($user && $password) {
        $query = $wpdb->prepare("SELECT harness_password, user_id FROM $wpdb->prefix" . "users_subscriptions WHERE harness_username=%s limit 1", $user);
        $user_row = $wpdb->get_row($query);
        if ($password == $user_row->harness_password) {
            if ($meta_key) {
                $query = $wpdb->prepare("SELECT * FROM $wpdb->prefix" . "community_profile_meta cpm Inner Join $wpdb->prefix" . "community_profile_instances cpi ON cpm.profile_id=cpi.id Where cpm.meta_key=%s AND cpm.meta_value=%s Order By cpm.meta_id desc, cpi.id desc, cpi.type desc Limit 1", $meta_key, $meta_value);
                echo $query;
            } else {
                $query = $wpdb->prepare("SELECT * FROM $wpdb->prefix" . "community_profile_instances cpi Where creator_id=%s Order By cpi.id desc, cpi.type desc Limit 1", $user_row->user_id);
            }
            $row = $wpdb->get_row($query);
        }
    }
}

if(!$row)
{
    header('HTTP/1.0 404 Not Found');
    exit();
}else{
    header('content-type: text/json');
    echo base64_decode($row->content);
}