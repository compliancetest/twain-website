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
            $suiteID = _get_current_test_suites(get_the_ID());
        
            if($suiteID){
                //check Community Member
                $groupID = get_post_meta($suiteID[0], 'community_id', true);
                if(groups_is_user_member(get_current_user_id(), $groupID))            
                {
                    return true;
                }
                $redirect = get_permalink($suiteID[0]);
            }        
        }
        wp_redirect($redirect);
        
        exit;
    }
}
add_action('template_redirect', 'checkCurrentUserCapability', 0);

function isSuiteEditable($suiteID, $userID = null)
{    
    if($userID == null)
    {
        $userID = get_current_user_id();       
    }
    
    if(!$userID)
        return false;
    
    
    
    $current_group_id = get_post_meta($suiteID, 'community_id', true);
    $group = groups_get_group( array( 'group_id' => $current_group_id ) );
    
    //Check if the user is 
}

