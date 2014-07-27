<?php

/**
* Check the user is an organisation admin
* 
* @param Int $user_id
* @param Int $organisation_id
* 
* @return organisation id or false
*/
function ct_is_organisation_admin($user_id = null, $organisation_id = null)
{
    global $wpdb;
    
    if(!$user_id)
        $user_id = get_current_user_id();
    
    $query = $wpdb->prepare("SELECT organisation_id FROM {$wpdb->prefix}organisations_members WHERE user_id=%d AND is_admin=1", $user_id);
    if($organisation_id) {
        $query .= $wpdb->prepare(" AND organisation_id=%d", $organisation_id);
    }
    
    $id = $wpdb->get_var($query);
    
    return $id;
}

function ct_get_organisation_admin($organisation_id)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT u.* FROM {$wpdb->prefix}organisations_members AS om LEFT JOIN {$wpdb->users} AS u ON u.ID=om.user_id WHERE om.organisation_id=%d AND om.user_id=u.ID", $organisation_id);
    $data = $wpdb->get_row($query);
    
    return $data;
}

/**
* Check the organisation purchased a subscription to the test suite
* 
* @param Int $organisation_id
* @param Int $suite_id
* @return subscription id or FALSE
*/
function ct_is_organisation_purchased_subscription($organisation_id, $suite_family_mark)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT id FROM {$wpdb->prefix}organisations_subscriptions AS os                             
            WHERE os.organisation_id=%d AND os.suite_family_mark=%d", $organisation_id, $suite_family_mark);
    $id = $wpdb->get_var($query);
    
    return !$id ? false : $id;
}

/**
* Get organisation subscription
* 
* @param Int $organisation_id
* @param Int $suite_family_mark
* 
* @array or null
*/
function ct_get_organisation_subscription($organisation_id, $suite_family_mark)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}organisations_subscriptions                             
            WHERE organisation_id=%d AND suite_family_mark=%d", $organisation_id, $suite_family_mark);
    $data = $wpdb->get_row($query);
    
    return $data;
}

/**
* Get user subscription
* 
* @param int $user_id
* @param int $suite_family_mark
* 
* @return array or null
*/
function ct_get_assigned_organisation_subscription($user_id, $suite_family_mark)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT * from {$wpdb->prefix}organisations_subscriptions 
                            WHERE user_id=%d AND suite_family_mark=%d AND `status` != 'Unsubscribing'", $user_id, $suite_family_mark);
       
    $data = $wpdb->get_row($query);
    
    return $data;
}

function ct_get_user_subscriptions($user_id)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT us.*, os.status, os.nickname from {$wpdb->prefix}users_subscriptions AS us
                             LEFT JOIN {$wpdb->prefix}organisations_subscriptions AS os ON os.id=us.parent_id
                            WHERE user_id=%d", $user_id);
       
    $data = $wpdb->get_results($query);
    
    return $data;
}

function ct_get_suite_harness_detail($user_id, $suite_id)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT * from {$wpdb->prefix}users_subscriptions 
                            WHERE user_id=%d AND suite_id=%d", $user_id, $suite_id);
       
    $data = $wpdb->get_row($query);
    
    return $data;
}

/**
* Get the organisation of $user_id
* If $user_id is null, get the organisation of the current logged user
* 
* @param Int $user_id
* 
* @return Array or null
*/
function ct_get_user_organisation($user_id = null)
{
    global $wpdb;
    
    if(!$user_id)
        $user_id = get_current_user_id();
        
    $data = get_userdata($user_id);
    
    //Getting domain
    list($p, $domain) = explode("@",  $data->user_email);
    
    $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}organisations WHERE organisation_domain=%s", $domain);
    $data = $wpdb->get_row($query);

    return $data;    
}

function ct_get_organisation_unallocated_subscriptions($organisation_id, $suite_family_mark)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT count(id) FROM {$wpdb->prefix}organisations_subscriptions WHERE organisation_id=%d AND suite_family_mark=%d AND user_id=0", $organisation_id, $suite_family_mark);
    $c = $wpdb->get_var($query);
    
    return $c;
}

function ct_get_organisation_subscriptions($organisation_id)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT os.*, u.user_email, u.display_name, t.suite_title FROM {$wpdb->prefix}organisations_subscriptions AS os 
                            LEFT JOIN {$wpdb->users} AS u ON u.ID=os.user_id 
                            LEFT JOIN {$wpdb->prefix}test_suites AS t ON t.family_mark=os.suite_family_mark
                            WHERE us.organisation_id=%d", $organisation_id);
    
    $rows = $wpdb->get_results($query);
    
    return $rows;
}

function ct_get_organisation_subscription_by_id($subscription_id)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}organisations_subscriptions WHERE id=%d", $subscription_id);
    $data = $wpdb->get_row($query);
    
    return $data;
}

function ct_get_test_suites_without_version()
{
    global $wpdb;
    
    $query = "SELECT DISTINCT(family_mark), suite_title FROM {$wpdb->prefix}test_suites ORDER BY suite_title";
    $data = $wpdb->get_results($query);
    
    return $data;
}

function ct_get_user_viewable_subscriptions($user_id)
{
    global $wpdb;
    
    $data = get_userdata($user_id);
    
    //Getting domain
    list($p, $domain) = explode("@",  $data->user_email);
    
    $query = $wpdb->prepare("SELECT os.* FROM {$wpdb->prefix}organisations_subscriptions AS os
                             LEFT JOIN {$wpdb->prefix}organisations AS o ON o.id = os.organisation_id
                             WHERE o.organisation_domain=%s ORDER BY os.nickname", 
                             $domain);
                             
    $data = $wpdb->get_results($query);
    
    return $data;
}