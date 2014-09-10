<?php
/***
* Get Test Data and Echo
*/

//$query = $wpdb->prepare("SELECT * FROM $wpdb->prefix" . "community_profile_instances WHERE token=%s", $token);<br>
if( is_super_admin() ){
    $wpdb->delete($wpdb->prefix . 'community_profile_meta', array('1'=>'1'), '%d');
    $results = $wpdb->get_results("SELECT * FROM $wpdb->prefix" . "community_profile_instances");

    foreach ($results as $row) {
        $content = json_decode(base64_decode($row->content));
        $profile_meta = getProfileMetaData($content);
        foreach ($profile_meta as $meta_key => $meta_value) {
            if( is_array( $row->id ) || is_array( $meta_key ) || is_array( $meta_value ) ){
                continue;
            }
            $wpdb->insert("{$wpdb->prefix}community_profile_meta", array(
                'profile_id' => $row->id,
                'meta_key' => $meta_key,
                'meta_value' => $meta_value,
            ));
        }
    }

    echo count($results) . ' profiles for searching.';
} else{
    exit('You havent access to perform this action');
}

/*function getProfileMetaData($data, $meta_key = '', $level = 0) {
    $ret = array();
    foreach ($data as $key => $value) {
        if ($level == 0 && !in_array($key, array('Profile', 'Entity', 'Fund')))
            continue;
        if (!is_object($value)) {
            $ret[($meta_key == '') ? ($key) : ($meta_key.'_'.$key)] = $value;
            continue;
        }
        $ret = array_merge($ret, getProfileMetaData($value, ($meta_key == '') ? ($key) : ($meta_key.'_'.$key), $level + 1));
    }
    return $ret;
}*/