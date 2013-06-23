<?php
/***
* Including the functions related with permission checking
*/

//Check Capabilities of the current user 
function checkCurrentUserCapability()
{
    //Only Admin Can see the groups list age
    if(is_page('groups') && !current_user_can('create_group'))
    {
        wp_redirect(get_site_url());
        exit;
    }
    
    if(is_single() && get_post_type() == 'test-case' && !current_user_can('read_case'))
    {        
        addMessage('Please subscribe to see detail information.', 'notice');
        //Get Test Suite Id
        $suiteID = _get_current_test_suites(get_the_ID());
        if(!$suiteID)        
            wp_redirect(get_site_url());
        else
            wp_redirect(get_permalink($suiteID[0]));
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

