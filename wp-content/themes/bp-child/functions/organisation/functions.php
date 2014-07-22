<?php

/**
* Check the user is an organisation admin
* 
* @param Int $user_id
* @param Int $organisation_id
* 
* @return organisation id or false
*/
function ct_is_organisation_admin($user_id, $organisation_id = null)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT id FROM {$wpdb->prefix}organisations_members WHERE user_id=%d AND is_admin=1");
    if($organisation_id) {
        $query .= $wpdb->prepare(" AND organisation_id=%d", $organisation_id);
    }
    
    $id = $wpdb->get_var($query);
    
    return $id;
}

/**
* Check the organisation purchased a subscription to the test suite
* 
* @param Int $organisation_id
* @param Int $suite_id
* @return subscription id or FALSE
*/
function ct_is_organisation_purchased_subscription($organisation_id, $suite_id)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT id FROM {$wpdb->prefix}organisations_subscriptions AS os
                             LEFT JOIN {$wpdb->prefix}test_suites AS s ON s.family_mark=os.family_mark
            WHERE os.organisation_id=%d AND s.suite_id=%d", $organisation_id, $suite_id);
    $id = $wpdb->get_var($query);
    
    return !$id ? false : $id;
}


function ct_get_organisation_subscription($organisation_id, $suite_id)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT id FROM {$wpdb->prefix}organisations_subscriptions AS os
                             LEFT JOIN {$wpdb->prefix}test_suites AS s ON s.family_mark=os.family_mark
            WHERE os.organisation_id=%d AND s.suite_id=%d", $organisation_id, $suite_id);
    $data = $wpdb->get_row($query);
    
    return $data;
}