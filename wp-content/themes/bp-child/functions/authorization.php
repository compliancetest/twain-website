<?php
/***
* Including the functions related with permission checking
*/

//Check Capabilities of the current user 
function checkCurrentUserCapability()
{
    //Only Admin Can see the groups list age
    if(is_page('groups'))
    {
        if(current_user_can('create_group'))
            return true;
        addMessage('You are not allowed to see the page.', 'error');
        wp_redirect(get_site_url());
        exit;
    }
    
    //Test Case
    if(is_single() && get_post_type() == 'test-case')
    {        
        if(current_user_can('read_case'))
            return true;
        
        $redirect = get_site_url();
        if(is_user_logged_in())
        {
            //Get Test Suite Id
            $suiteID = _get_current_test_suite(get_the_ID());
        
            if($suiteID){
                //check Community Member
                $groupID = get_post_meta($suiteID, 'community_id', true);
                if(groups_is_user_member(get_current_user_id(), $groupID))            
                {
                    return true;
                }
                $redirect = get_permalink($suiteID);
            }        
        }
        addMessage('You must join the community to view Test Case details.', 'notice');
        wp_redirect($redirect);
        
        exit;
    }
}
add_action('template_redirect', 'checkCurrentUserCapability', 0);

function can_edit_suite($suiteID, $user_id = null)
{    
    if($user_id == null)
        $user_id = get_current_user_id();
        
    //Check if the user is a system admin
    if(user_can($user_id, 'edit_other_suite'))
    {
        return true;
    }
    
    //Check if the user is the admin of the Community
    $comunity_id = get_post_meta($suiteID, 'community_id', true);
    if(groups_is_user_admin($user_id, $comunity_id))
    {
        return true;
    }
    
    return false;
}

function can_create_suite($user_id = null)
{
    if($user_id == null)
        $user_id = get_current_user_id();
        
    //Check if the user is a system admin
    if(user_can($user_id, 'create_suite'))
    {
        return true;
    }
    
    //Check if the user is an admin of a Community    
    
    if(bp_is_group_admin($user_id))
    {
        return true;
    }
    
}

function bp_is_group_admin($user_id)
{
    global $wpdb;
    
    $groups = groups_get_user_groups($user_id);
    if(!$groups || !$groups['groups'])
        return false;
    
    foreach($groups['groups'] as $group_id)
    {
        if(groups_is_user_admin($user_id, $group_id))
        {
            return true;
        }
    }
    return false;
}

function is_customer($user_id)
{
    return false;
}

function can_edit_test_case($caseID, $user_id = null)
{    
    if($user_id == null)
        $user_id = get_current_user_id();
        
    //Check if the user is a system admin
    if(user_can($user_id, 'edit_other_case'))
    {
        return true;
    }
    //Get Test Suite ID
    $suiteID = _get_current_test_suite($caseID);
    if(!$suiteID)
        return false;
    //Check if the user is the admin of the Community
    $comunity_id = get_post_meta($suiteID, 'community_id', true);
    if(groups_is_user_admin($user_id, $comunity_id))
    {
        return true;
    }
    
    return false;
}

function can_create_test_case($user_id = null)
{
    if($user_id == null)
        $user_id = get_current_user_id();
        
    //Check if the user is a system admin
    if(user_can($user_id, 'create_case'))
    {
        return true;
    }
    
    //Check if the user is an admin of a Community    
    
    if(bp_is_group_admin($user_id))
    {
        return true;
    }
    
}

function can_create_group($user_id = null)
{
    if($user_id == null)
        $user_id = get_current_user_id();
        
    //Check if the user is a system admin
    if(user_can($user_id, 'create_group'))
    {
        return true;
    }
    
    if(is_admin() || is_super_admin())
        return true;
    
    return false;
}

function can_make_compliance_claim($user_id = null)
{
    if($user_id == null)
        $user_id = get_current_user_id();
        
    //Check if the user is a system admin
    if(user_can($user_id, 'make_compliance_claim'))
    {
        return true;
    }
    
    if(is_admin() || is_super_admin() || is_customer($user_id))
        return true;
    
    return false;
}

function can_create_product_and_service($user_id = null)
{
    if($user_id == null)
        $user_id = get_current_user_id();
    
    //Check if the user is a system admin
    if(user_can($user_id, 'create_product_service'))
    {
        return true;
    }
    
    if(bp_is_group_admin($user_id) || is_customer($user_id))
    {
        return true;
    }
    
    return false;
}

function can_edit_product_and_service($product_service_id, $user_id = null)
{
    if($user_id == null)
        $user_id = get_current_user_id();
    
    //Check if the user is a system admin
    if(user_can($user_id, 'edit_other_product_service'))
    {
        return true;
    }
    
    if(bp_is_group_admin($user_id))
    {
        return true;
    }
    
    if(is_customer($user_id) && get_post_author_id($product_service_id) == $user_id)
    {
        return true;
    }
    
    return false;
}