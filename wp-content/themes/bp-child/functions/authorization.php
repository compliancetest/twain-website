<?php
/***
* Including the functions related with permission checking
*/

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