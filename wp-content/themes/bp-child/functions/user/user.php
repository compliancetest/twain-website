<?php
/**
* The Gate Way function for users action
*/
require_once('user-auth.php');
require_once('user-profile.php');
require_once('user-transactions.php');


add_action('init', 'compliancetest_user_actions');
//Process User Login, Register Action
function compliancetest_user_actions()
{
    global $wpdb;
    
    if ( ! current_user_can( 'manage_options' ) ) {
        show_admin_bar( false );
        remove_action('wp_head', '_admin_bar_bump_cb');
    }
    
    $cpAction = isset($_REQUEST['cp-action']) ? $_REQUEST['cp-action'] : null;
    if($cpAction == 'login')
    {
        compliancetest_login();
    }else if(wp_verify_nonce($cpAction, 'register')){
        compliancetest_create_new_user();
    }else if(wp_verify_nonce($cpAction,'resend_email_verification')){
        resend_email_verification();
    }else if(wp_verify_nonce($cpAction,'request_reset_password')){
        cp_request_reset_password();
    }else if(wp_verify_nonce($cpAction,'reset_password')){
        cp_reset_password();
    }else if(wp_verify_nonce($cpAction, 'user_activation')){
        cp_activate_user();
    }else if(wp_verify_nonce($cpAction,'my_details_edit')){
        cp_user_detail_edit();
    }else if(wp_verify_nonce($cpAction,'edit_payment_method')){
        cp_user_payment_edit();
    }else if(wp_verify_nonce($cpAction ,'save_payment_method')){
        $result = cp_user_payment_save();        
        if($result === true || is_int($result))
            echo "success";
        else
            echo $result;
        exit;
    }else if(wp_verify_nonce($cpAction ,'my_organisation_edit')){
        cp_user_organisation_edit();
    }else if(wp_verify_nonce($cpAction ,'delete_payment_method')){
        cp_delete_payment_method();
    }else if(wp_verify_nonce($cpAction ,'leave-group')){
        $gID = $_REQUEST['group_id'];
        if ( groups_is_user_member( bp_loggedin_user_id(), $gID ) ) {

            // Stop sole admins from abandoning their group
            $group_admins = groups_get_group_admins( $gID );
             if ( 1 == count( $group_admins ) && $group_admins[0]->user_id == bp_loggedin_user_id() )
                echo  __( 'This community must have at least one admin', 'buddypress' );
            elseif ( !groups_leave_group( $gID ) )
                echo __( 'There was an error leaving the community.', 'buddypress' );
            else
                echo 'success';
        }else{
            echo 'Invalid Request!';
        }
        exit;
    }else if(wp_verify_nonce($cpAction, 'save-harness')){
        $result = cp_save_customer_harness_detail();       
        echo $result;
        exit;
    }else if(wp_verify_nonce($cpAction, 'edit-transaction-log')){
        $result = cp_edit_transaction_log();               
        echo $result;
        exit;
    }else if(wp_verify_nonce($cpAction, 'save-transaction-log')){
        $result = cp_save_transaction_log();               
        echo $result;
        exit;
    }else if(wp_verify_nonce($cpAction, 'delete-transaction-log')){
        $result = cp_delete_transaction_log();        
        exit;
    }else if(wp_verify_nonce($cpAction, 'view-validation-log')){
        $result = cp_view_validation_log();        
        echo $result;
        exit;
    }else if(wp_verify_nonce($cpAction, 'suite-notify-changes')){
        $result = cp_save_suite_notify_changes();        
        echo $result;
        exit;
    }
}



function getUserCreditCards($user_id = null, $only_active = false)
{
    global $wpdb;
    
    if(!$user_id)
        $user_id = get_current_user_id();
    
    if($only_active)
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "users_cards WHERE user_id=%d And `status`='Active'", $user_id);
    else
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "users_cards WHERE user_id=%d", $user_id);
    $rows = $wpdb->get_results($query);
    
    return $rows;
}

function getUserCardById($card_id, $user_id = null)
{
    global $wpdb;
    
    if(!$user_id)
        $user_id = get_current_user_id();
    
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "users_cards WHERE user_id=%d and id=%d", $user_id, $card_id);
    $row = $wpdb->get_row($query);
    
    return $row;
}

function getCustomerCardDetailById($customer_id)
{
    global $wpdb;
    
    $tokenWebserviceURL = get_eway_token_webservice_url();
    $customerID = get_eway_customer_id();
    $userName = get_eway_user_name();
    $userPWD = get_eway_user_pwd();
    
    //Create or Update Customer Information
    require_once(THE_FUNCTION . '/soap/nusoap.php');    
    
    $client = new nusoap_client($tokenWebserviceURL, false);
    $err = $client->getError();
    if ($err) {
        return 'Soap Construction Error: ' . $err;
    }
    
    $client->namespaces['man'] = 'https://www.eway.com.au/gateway/managedpayment';
    $headers = "<man:eWAYHeader><man:eWAYCustomerID>" . $customerID . "</man:eWAYCustomerID><man:Username>" . $userName . "</man:Username><man:Password>" . $userPWD . "</man:Password></man:eWAYHeader>";
    $client->setHeaders($headers);    
    
    $requestbody = array(
        'man:managedCustomerID' => $customer_id
    );
    $soapaction = 'https://www.eway.com.au/gateway/managedpayment/QueryCustomer';
    $result = $client->call('man:QueryCustomer', $requestbody, '', $soapaction);
    
    return $result;
}

//Add Js File
add_action('wp_enqueue_scripts', 'add_user_script');
function add_user_script()
{
    $actions_depends = array('jquery');
    if (stripos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) {
        wp_enqueue_script('pie', get_stylesheet_directory_uri().'/js/PIE.js', $actions_depends);
        $actions_depends[] = 'pie';
    }
    $actions_depends[] = 'jquery_form';
    $actions_depends[] = 'custom_scripts';
    
    wp_enqueue_script('user-auth', get_stylesheet_directory_uri() . '/functions/user/user.js', $actions_depends);
}


//Get Groups that the user is a admin of
function getUserAdminGroups($user_id)
{
    $groups = groups_get_groups( array('user_id' => $user_id) );
    
    $result = array();
    foreach($groups['groups'] as $g)
    {
        if(groups_is_user_admin($user_id, $g->id))
        {            
            $result[] = $g;
        }
    }
    
    return $result;
}



function getUserPurchase($suite_id = null, $user_id = null)
{
    global $wpdb;
    
    if($user_id == null)
        $user_id = get_current_user_id();
    
    if($suite_id == null){
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "users_purchases WHERE user_id=%d GROUP BY id", $user_id);
        $result = $wpdb->get_results($query);
    }else{
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "users_purchases WHERE user_id=%d AND suite_id=%d GROUP BY id", $user_id, $suite_id);
        $result = $wpdb->get_row($query);
    }    
    
    return $result;
}

function getGroupMemberDetail($group_id, $member_id)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "bp_groups_members WHERE group_id=%d AND user_id=%d", $group_id, $member_id);
    $row = $wpdb->get_row($query);
    
    return $row;
}

function getUserSubscriptions($user_id = null, $all = false)
{
    global $wpdb;
    
    if($user_id == null)
        $user_id = get_current_user_id();
    
    if($all)
        $query = $wpdb->prepare("SELECT s.*, p.post_title AS suite_title FROM " . $wpdb->prefix . "users_purchases AS s LEFT JOIN {$wpdb->posts} AS p ON p.ID=s.suite_id WHERE s.user_id=%d", $user_id);
    else
        $query = $wpdb->prepare("SELECT s.*, p.post_title AS suite_title FROM " . $wpdb->prefix . "users_purchases AS s LEFT JOIN {$wpdb->posts} AS p ON p.ID=s.suite_id WHERE s.user_id=%d AND s.status != 'Frozen'", $user_id);
    $result = $wpdb->get_results($query);
    
    return $result;
    
}

function getSubscribersBySuiteId($suite_id)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT user_id, u.user_email FROM " . $wpdb->prefix . "users_purchases p LEFT JOIN " . $wpdb->users . " AS u ON p.user_id=u.ID WHERE suite_id=%d AND status='Active'", $suite_id);
    $result = $wpdb->get_results($query);
    
    return $result;
    
}



/**
* Getting User Communities that includes the subscribed suites.
* Used in Test Data Section
* 
* @param mixed $user_id
*/
function getUserSubscribedCommunities($user_id = null)
{
    global $wpdb;
    
    $purchases = getUserSubscriptions($customer_id);
    $community_ids = array();
    foreach($purchases as $r)
        $community_ids[] = get_post_meta($r->suite_id, 'community_id', true);
    
    $community_ids = array_unique($community_ids);
    
    return $community_ids;
}


/***
* Get the test suites belong to the communities where the user has admin or mod rule in
* 
* @param int $user_id
*/
function getAssignedSuiteIds($user_id = null)
{
    global $wpdb;
    
    if($user_id == null)
        $user_id = get_current_user_id();
    
    $suite_ids = array();    
    $communities = groups_get_groups(array('user_id' => $user_id));
    
    foreach($communities['groups'] as $community)
    {
        if(groups_is_user_admin($user_id, $community->id) || groups_is_user_mod($user_id, $community->id))
        {
            //Get Group Suites
            $query = "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='community_id' AND meta_value='$community->id'";            
            $sid = $wpdb->get_col($query);
            if($sid)
                $suite_ids = array_merge($sid, $suite_ids);
        }
    }
    
    return $suite_ids;    
}


/**
* Get All Test Suites that the user subscribed to or can manage
* 
* @param mixed $user_id
*/
function getUserAllSuiteIDs($user_id = null)
{
    global $wpdb;
    
    if($user_id == null)
        $user_id = get_current_user_id();
            
    $suite_ids1 = getAssignedSuiteIds($user_id);
    
    $query = $wpdb->prepare("SELECT suite_id FROM " . $wpdb->prefix . "users_purchases WHERE user_id=%d AND `status`='Active'", $user_id);    
    $suite_ids2 = $wpdb->get_col($query);
    
    $result = array_merge($suite_ids1, $suite_ids2);
    $result = array_unique($result);
    
    return $result;
}


/***
* Get the Customers that subscribed to the suites that the user can manage
* 
* @param int $user_id
* @return []
*/
function getManagedCustomers($user_id = null)
{
    global $wpdb;
       
    if($user_id == null)
        $user_id = get_current_user_id();
    
    if(!is_super_admin() && !is_admin())
    {
        $suite_ids = getAssignedSuiteIds($user_id);
        if(!$suite_ids)
            return null;
            
        $query = "SELECT DISTINCT(p.user_id) as CUSTOMER_ID, u.display_name AS CUSTOMER_NAME FROM $wpdb->prefix" . "users_purchases AS p LEFT JOIN $wpdb->users AS u ON u.ID = p.user_id WHERE p.status='Active' AND p.suite_id IN (" . implode(", ", $suite_ids) . ") ORDER BY u.display_name";        
    }else{
        $query = "SELECT DISTINCT(p.user_id) as CUSTOMER_ID, u.display_name AS CUSTOMER_NAME FROM $wpdb->prefix" . "users_purchases AS p LEFT JOIN $wpdb->users AS u ON u.ID = p.user_id WHERE  p.status='Active' ORDER BY u.display_name";
    }
    
    $customers = $wpdb->get_results($query);
    
    return $customers;
}

/***
* Get the Customer WP IDs that subscribed to the suites that the user can manage
* 
* @param int $user_id
* @return []
*/
function getManagedCustomerWPIDs($user_id = null)
{
    global $wpdb;
       
    if($user_id == null)
        $user_id = get_current_user_id();
    
    if(!is_super_admin() && !is_admin())
    {
        $suite_ids = getAssignedSuiteIds($user_id);
        if(!$suite_ids)
            return null;
            
        $query = "SELECT DISTINCT(p.user_id) as CUSTOMER_ID FROM $wpdb->prefix" . "users_purchases AS p WHERE p.status='Active' AND p.suite_id IN (" . implode(", ", $suite_ids) . ")";        
    }else{
        $query = "SELECT DISTINCT(p.user_id) as CUSTOMER_ID FROM $wpdb->prefix" . "users_purchases AS p WHERE  p.status='Active'";
    }
    
    $ids = $wpdb->get_col($query);
    
    return $ids;
}

/***
* Get the Customer ESB IDs that subscribed to the suites that the user can manage
* 
* @param int $user_id
* @return []
*/
function getManagedCustomerESBIDs($user_id = null)
{
    global $wpdb;
       
    if($user_id == null)
        $user_id = get_current_user_id();
    
    if(!is_super_admin() && !is_admin())
    {
        $suite_ids = getAssignedSuiteIds($user_id);
        if(!$suite_ids)
            return null;
            
        $query = "SELECT DISTINCT(p.esb_user_id) as CUSTOMER_ID FROM $wpdb->prefix" . "users_purchases AS p WHERE p.status='Active' AND p.suite_id IN (" . implode(", ", $suite_ids) . ")";        
    }else{
        $query = "SELECT DISTINCT(p.esb_user_id) as CUSTOMER_ID FROM $wpdb->prefix" . "users_purchases AS p WHERE  p.status='Active'";
    }
    
    $ids = $wpdb->get_col($query);
    
    return $ids;
}

/***
* Getting User Customer ESB IDs as well as Managed customer ids
* 
* @param mixed $user_id
*/
function getUserAllCustomerESBIDs($user_id = null, $exclude_free_charge = false)
{
    global $wpdb;
    
    if($user_id == null)
        $user_id = get_current_user_id();
    
    if(!is_super_admin() && !is_admin())
    {
        $suite_ids = getAssignedSuiteIds($user_id);
        if(!$suite_ids)            
            $query = "SELECT DISTINCT(p.esb_user_id) as CUSTOMER_ID FROM $wpdb->prefix" . "users_purchases AS p WHERE p.status='Active' AND p.user_id=$user_id";        
        else
            $query = "SELECT DISTINCT(p.esb_user_id) as CUSTOMER_ID FROM $wpdb->prefix" . "users_purchases AS p WHERE p.status='Active' AND (p.suite_id IN (" . implode(", ", $suite_ids) . ") OR p.user_id=$user_id)";        
    }else{
        $query = "SELECT DISTINCT(p.esb_user_id) as CUSTOMER_ID FROM $wpdb->prefix" . "users_purchases AS p WHERE p.status='Active'";
    }
    
    if($exclude_free_charge)
        $query  .= " AND p.card_id > 0";
    
    $ids = $wpdb->get_col($query);
    
    return $ids;
}

/**
* Get the last used data for user to trigger message
* 
* @param mixed $user_id
*/
function getUserLastDataForMessage($user_id = null)
{
    global $wpdb;
    
    if($user_id == null)
        $user_id = get_current_user_id();
        
    $query = $wpdb->prepare(
        "SELECT * FROM " . $wpdb->prefix . "message_tmp " .
        "WHERE user_id=%d", $user_id
    );
    
    $data = $wpdb->get_row($query);
    
    return $data;
}

/**
* Get User Message Templates
* 
* @param mixed $user_id
*/
function getUserPreviousMessageTemplates($user_id = null)
{
    global $wpdb;
    
    if($user_id == null)
        $user_id = get_current_user_id();
        
    $query = $wpdb->prepare(
        "SELECT * FROM " . $wpdb->prefix . "message_templates " .
        "WHERE user_id=%d", $user_id
    );
    
    $rows = $wpdb->get_results($query);
    
    return $rows;
}

function getTestCaseTemplates($case_id)
{
    global $CPRest;
    
    if(is_numeric($case_id))
        $case_id = get_post_meta($case_id, 'test_case_id', true);
    
    $result = $CPRest->doRepositoryAPI('template/list/' . $case_id, null, false, false);
    
    $resultDoc = new DOMDocument();
        
    if(!$resultDoc || !$resultDoc->loadXML($result))
        return array();
    
    $results = array();
    
    $templates = $resultDoc->getElementsByTagName('template');
    for($i=0; $i<$templates->length; $i++)
    {
        $results[] = $templates->item($i)->nodeValue;
    }
    
    asort($results);
    
    return $results;
}