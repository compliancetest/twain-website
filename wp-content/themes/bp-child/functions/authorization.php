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
    
    //View Test Case
    if(is_single() && get_post_type() == 'test-case')
    {        
        if(current_user_can('read_case'))
            return true;
        
        $suiteID = _get_current_test_suite(get_the_ID());
        $redirect = get_permalink($suiteID);
        $groupID = get_post_meta($suiteID, 'community_id', true);
        $group = groups_get_group(array('group_id' => $groupID));
        
        if(is_user_logged_in())
        {
            //Get Test Suite Id
            if($suiteID){
                //check Community Member
                if(groups_is_user_member(get_current_user_id(), $groupID))            
                {
                    return true;
                }                
            }        
        }
        addMessage('You must join the community to view Test Case details. Go to the <a href="' . bp_get_group_permalink($group) . '">Community Home Page</a> to join', 'notice');
        wp_redirect($redirect);
        
        exit;
    }
}
add_action('template_redirect', 'checkCurrentUserCapability', 0);


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

function is_customer($suite_id = null, $user_id = null)
{
    global $wpdb;
    
    if($user_id == null)
        $user_id = get_current_user_id();
    
//    if(is_admin() || is_super_admin())
//    {
//        return ture;
//    }
    
    if($suite_id == null)
        $query = $wpdb->prepare("SELECT COUNT(1) FROM " . $wpdb->prefix . "users_purchases WHERE user_id=%d and `status`='Active' AND expiry_date >= '" . date("Y-m-d") . "' GROUP BY id", $user_id);
    else
        $query = $wpdb->prepare("SELECT COUNT(1) FROM " . $wpdb->prefix . "users_purchases WHERE user_id=%d and `status`='Active' AND suite_id=%d AND expiry_date >= '" . date("Y-m-d") . "' GROUP BY id", $user_id, $suite_id);
    
    $c = $wpdb->get_var($query);
    
    if($c > 0)
        return true;
    
    return false;
}

/******************************************************************** Test Suite ***************************************************************/
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

function can_delete_suite($suiteID, $user_id = null)
{    
    if($user_id == null)
        $user_id = get_current_user_id();
        
    //Check if the user is a system admin
    if(user_can($user_id, 'delete_other_suite'))
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

/******************************************************************** Test Case ***************************************************************/
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

function can_delete_test_case($caseID, $user_id = null)
{    
    if($user_id == null)
        $user_id = get_current_user_id();
        
    //Check if the user is a system admin
    if(user_can($user_id, 'delete_other_case'))
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

/************************************ Group ********************************************/
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

/******************************************************************** Compliance Claim ***************************************************************/
function can_make_compliance_claim($product_id, $user_id = null)
{
    if($user_id == null)
        $user_id = get_current_user_id();
    
    if(is_user_logged_in())
        return true;
    else
        return false;
    
    //Check if the user is a system admin
    if(user_can($user_id, 'make_compliance_claim'))
    {
        return true;
    }
    
    if(is_admin() || is_super_admin())
        return true;
    
    if(is_customer())
    {
        //Check if the product belong to the community that the customer subscribed.
        return true; //For Now
    }
    
    return false;
}

function can_edit_compliance_claim($claim_id, $user_id = null)
{
    if($user_id == null)
        $user_id = get_current_user_id();
    
    if(is_super_admin() || is_admin())
    {
        return true;
    }
    
    $claim = new ComplianceClaim($claim_id);
    $claim->load();
    
    if($claim->creator_id == $user_id)
        return true;
        
    return true;    
}

function can_delete_compliance_claim($claim_id, $user_id = null)
{
    return can_edit_compliance_claim($claim_id, $user_id);
}



/******************************************************************** Product / Service ***************************************************************/
function can_create_product_and_service($user_id = null)
{
    if(is_user_logged_in())
        return true;
        
    //if($user_id == null)
//        $user_id = get_current_user_id();
//    
    //Check if the user is a system admin
    //if(user_can($user_id, 'create_product_service'))
//    {
//        return true;
//    }
//    
//    if(bp_is_group_admin($user_id) || is_customer($user_id))
//    {
//        return true;
//    }
    
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
    $post = get_post($product_service_id);
    
    if($post->post_author == $user_id)
    {
        return true;
    }
    
    return false;
}

function can_delete_product_and_service($product_service_id, $user_id = null)
{
    return can_edit_product_and_service($product_service_id, $user_id = null);
}