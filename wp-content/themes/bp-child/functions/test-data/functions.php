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
    
    if(!$community_ids)
        return array();
        
    $query = "SELECT * FROM wp_community_profile_types WHERE community_id IN (" . implode(", ", $community_ids) . ") AND is_displayed = 1 ";
    $rows = $wpdb->get_results($query);
    
    return $rows;
}

function getCommunityProfileInstatnces($community_id)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT pi.*, pt.title AS profile_type_title, pt.schema FROM " . $wpdb->prefix . "community_profile_instances AS pi LEFT JOIN " . $wpdb->prefix . "community_profile_types AS pt ON pt.id=pi.type_id WHERE pi.community_id=%s AND pi.type='harness' ORDER BY pi.purpose, pi.profile_name ASC", $community_id);
    $rows = $wpdb->get_results($query);
    
    return $rows;
}

function getCustomerProfileInstances($user_id = null, $process_filters = false )
{
    global $wpdb;
    
    if(!$user_id)
        $user_id = get_current_user_id();

    if( $process_filters ){
        $where = array( TRUE );
        if( isset( $_GET['type'] ) && $_GET['type'] != 'All' ){
            $where[] = $wpdb->prepare( ' type_name = %s ', $_GET['type'] );
        }
        if( isset( $_GET['validity'] ) && $_GET['validity'] != 'All' ){
            $where[] = $wpdb->prepare( ' validation_status = %s ', $_GET['validity'] );
        }
        if( isset( $_GET['tag'] ) && $_GET['tag'] != 'All' ){
            $profiles_with_tag = $wpdb->get_results( $wpdb->prepare("SELECT * FROM wp_tags2items WHERE tag_id = %d AND item_type = 'PROFILE'", $_GET['tag'] ) );
            $p_ids = array();
            if( $profiles_with_tag ){
                foreach( $profiles_with_tag AS $profile_with_tag ){
                    $p_ids[] = $profile_with_tag->item_id;
                }
                $where[] = ' pi.id IN('.implode(',', $p_ids ).') ';
            }
        }
        $where_str = implode(' AND ', $where);
        $query = $wpdb->prepare("SELECT pi.*, pt.title AS profile_type_title, pt.schema FROM wp_community_profile_instances AS pi
                                LEFT JOIN wp_community_profile_types AS pt ON pt.id = pi.type_id
                                WHERE $where_str AND pi.creator_id = %d AND pi.type='tester' ORDER BY pi.purpose, pi.profile_name", $user_id);
        return $wpdb->get_results($query);
    }
    $query = $wpdb->prepare("SELECT pi.*, pt.title AS profile_type_title, pt.schema FROM " . $wpdb->prefix . "community_profile_instances AS pi LEFT JOIN " . $wpdb->prefix . "community_profile_types AS pt ON pt.id=pi.type_id WHERE pi.creator_id=%d AND pi.type='tester' ORDER BY pi.purpose, pi.profile_name", $user_id);
    $rows = $wpdb->get_results($query);
    
    return $rows;
    
}
function getCustomerProfileInstancesFilters( $profiles ){
    global $wpdb;
    $tags = $type = $validity = $ids = array();
    foreach( $profiles AS $profile ){
        if( ! in_array( $profile->type_name, $type ) ) {
            array_push( $type, $profile->type_name );
        }
        if( ! in_array( $profile->validation_status, $validity ) ) {
            array_push( $validity, $profile->validation_status );
        }
        $ids[] = $profile->id;
    }
    if( $ids ) {
        $tags_data = $wpdb->get_results("SELECT id, name FROM wp_tags WHERE id IN(SELECT tag_id FROM wp_tags2items WHERE item_id IN(" . implode(',', $ids) . ") ) GROUP BY name ORDER BY name");
        foreach( $tags_data AS $tag_entry ){
            $tags[$tag_entry->id] = $tag_entry->name;
        }
    }
    sort( $type );
    sort( $validity );
    return array(
        'type'     => $type,
        'tags'     => $tags,
        'validity' => $validity
    );
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