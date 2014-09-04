<?php
/**
* Manage Test Data Functions
*/

function getCommunityProfileTypes($community_id)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "community_profile_types WHERE community_id=%d", $community_id);
    
    $rows = $wpdb->get_results($query);
    
    return $rows;
}

function getCustomerProfileTypes($customer_id)
{
    global $wpdb;
    
    $community_ids = getUserSubscribedCommunities($customer_id);
    
    $query = "SELECT * FROM " . $wpdb->prefix . "community_profile_types WHERE community_id IN (" . implode(", ", $community_ids) . ")";
    $rows = $wpdb->get_results($query);
    
    return $rows;
}

function getCommunityProfileInstatnces($community_id)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT pi.*, pt.title AS profile_type_title, pt.schema FROM " . $wpdb->prefix . "community_profile_instances AS pi LEFT JOIN " . $wpdb->prefix . "community_profile_types AS pt ON pt.id=pi.type_id WHERE pi.community_id=%d AND pi.type='harness' ORDER BY pi.purpose, pi.profile_name ASC", $community_id);
    $rows = $wpdb->get_results($query);
    
    return $rows;
}

function getCustomerProfileInstances($user_id = null)
{
    global $wpdb;
    
    if(!$user_id)
        $user_id = get_current_user_id();
    
    $query = $wpdb->prepare("SELECT pi.*, pt.title AS profile_type_title, pt.schema FROM " . $wpdb->prefix . "community_profile_instances AS pi LEFT JOIN " . $wpdb->prefix . "community_profile_types AS pt ON pt.id=pi.type_id WHERE pi.creator_id=%d AND pi.type='tester' ORDER BY pi.purpose, pi.profile_name", $user_id);
    $rows = $wpdb->get_results($query);
    
    return $rows;
    
}

function getUserLastUsedProfileType($type = 'harness', $community_id = null, $user_id = null)
{
    global $wpdb;
    
    if(!$user_id)
        $user_id = get_current_user_id();
    
    $query = $wpdb->prepare("SELECT type_id FROM " . $wpdb->prefix . "community_profile_instances WHERE creator_id=%d AND type=%s", $user_id, $type);
    
    if($community_id != null)
        $query .= $wpdb->prepare(" AND community_id=%d", $community_id);
        
    $query .= ' ORDER BY created_date DESC';
    
    $type = $wpdb->get_var($query);
    
    return $type;
}