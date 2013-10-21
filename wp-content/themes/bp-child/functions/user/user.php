<?php
/**
* The Gate Way function for users action
*/
require_once('user-auth.php');
require_once('user-profile.php');


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



function getUserCreditCards($user_id = null)
{
    global $wpdb;
    
    if(!$user_id)
        $user_id = get_current_user_id();
    
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
add_action('wp_head', 'add_user_script');
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

//Get User Test Suites
function getUserTestSuites($user_id = null)
{
    if($user_id == null)
        $user_id = get_current_user_id();
    
    //Getting User Groups
    $groups = groups_get_groups( array('user_id' => $user_id) );
    
    $args = array(
        'post_type' => 'test-suite', 
        'posts_per_page' => -1,
        'meta_query' => array(
            'relation' => 'OR'            
        )
    );
    
    if(!is_admin() && !is_super_admin())
    {        
        foreach($groups['groups'] as $group)
        {
            $args['meta_query'][] = array(
                    'key' => 'community_id',
                    'value' => $group->id,
                    'compare' => '='
                );
        }
    }
    
    $testsuites = get_posts( $args );
    
    return $testsuites;
}

function getUserProductsAndServices($user_id = null, $exclusive = array())
{
    if($user_id == null)
        $user_id = get_current_user_id();
    
    $args = array(
        'post_type' => 'product-service', 
        'posts_per_page' => -1,
        'author' => $user_id
    );
    
    
    $rows = get_posts($args);
    $results = array();
    
    if(!$exclusive)
    {        
        $results = $rows;
    }else{
        foreach($rows as $row)
        {
            if(in_array($row->ID, $exclusive))
                continue;
            $results[] = $row;
        }    
    }
    
    return $results;
}


function getUserPurchase($suite_id = null, $status='Active', $user_id = null)
{
    global $wpdb;
    
    if($user_id == null)
        $user_id = get_current_user_id();
    
    if($suite_id == null){
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "users_purchases WHERE user_id=%d AND `status`=%s AND expiry_date >= '" . date("Y-m-d") . "' GROUP BY id", $user_id, $status);
        $result = $wpdb->get_results($query);
    }else{
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "users_purchases WHERE user_id=%d AND suite_id=%d AND `status`=%s AND expiry_date >= '" . date("Y-m-d") . "' GROUP BY id", $user_id, $suite_id, $status);
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

function getUserSubscriptions($user_id = null)
{
    global $wpdb;
    
    if($user_id == null)
        $user_id = get_current_user_id();
        
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "users_purchases WHERE user_id=%d AND status='Active'", $user_id);
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


function getUserSubscribedSuites($user_id = null)
{
    global $wpdb;
    
    if($user_id == null)
        $user_id = get_current_user_id();
        
    $query = $wpdb->prepare(
        "SELECT sp.*, pm.meta_value as `name` FROM " . $wpdb->prefix . "users_purchases AS sp " .
        "LEFT JOIN " . $wpdb->postmeta . " AS pm ON pm.post_id=sp.suite_id AND pm.meta_key='ts_name' " .
        "WHERE sp.user_id=%d AND sp.status='Active' AND pm.meta_value IS NOT NULL", $user_id
    );
    
    $rows = $wpdb->get_results($query);
    
    return $rows;
}

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

function getUserSubscribedCases($user_id = null)
{
    global $wpdb;
    
    if($user_id == null)
        $user_id = get_current_user_id();
        
    $query = $wpdb->prepare(
        "SELECT suite_id FROM " . $wpdb->prefix . "users_purchases " .
        "WHERE user_id=%d AND status='Active'", $user_id
    );
    
    $suite_ids = $wpdb->get_col($query);
    
    if(!$suite_ids)
        return array();
        
    $query = "SELECT DISTINCT(p.ID), pm1.meta_value as caseName FROM " . $wpdb->posts . " AS p LEFT JOIN " . $wpdb->postmeta . " AS pm ON p.ID=pm.post_id AND pm.meta_key='test_suite' LEFT JOIN " . 
            $wpdb->postmeta ." AS pm1 ON p.ID=pm1.post_id AND pm1.meta_key='test_case_id' WHERE p.post_type='test-case' AND p.post_status='publish' AND pm.meta_value IN (" . implode(", ", $suite_ids) . ")";
    
    $rows = $wpdb->get_results($query);
    
    return $rows;
}

function getManageableSuiteIds($user_id = null)
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


function getManageableCustomers($user_id = null)
{
    global $wpdb;
       
    if($user_id == null)
        $user_id = get_current_user_id();
    
    if(!is_super_admin() && !is_admin())
    {
        $suite_ids = getManageableSuiteIds($user_id);
        if(!$suite_ids)
            return null;
            
        $query = "SELECT p.esb_user_id as CUSTOMER_ID, u.display_name AS CUSTOMER_NAME FROM $wpdb->prefix" . "users_purchases AS p LEFT JOIN $wpdb->users AS u ON u.ID = p.user_id WHERE p.status='Active' AND p.suite_id IN (" . implode(", ", $suite_ids) . ") ORDER BY u.display_name";        
    }else{
        $query = "SELECT p.esb_user_id as CUSTOMER_ID, u.display_name AS CUSTOMER_NAME FROM $wpdb->prefix" . "users_purchases AS p LEFT JOIN $wpdb->users AS u ON u.ID = p.user_id WHERE  p.status='Active' ORDER BY u.display_name";
    }
    
    $customers = $wpdb->get_results($query);
    
    return $customers;
}

function getUserPermittedCustomersIDs($user_id = null)
{
    global $wpdb;
        
    if($user_id == null)
        $user_id = get_current_user_id();
    
    if(!is_super_admin() && !is_admin())
    {
        $suite_ids = getManageableSuiteIds($user_id);
        if(!$suite_ids)
            $query = "SELECT DISTINCT(p.esb_user_id) FROM $wpdb->prefix" . "users_purchases AS p LEFT JOIN $wpdb->users AS u ON u.ID = p.user_id WHERE p.status='Active' AND user_id=" . intval($user_id) . " ORDER BY u.display_name";        
        else
            $query = "SELECT DISTINCT(p.esb_user_id) FROM $wpdb->prefix" . "users_purchases AS p LEFT JOIN $wpdb->users AS u ON u.ID = p.user_id WHERE p.status='Active' AND (p.suite_id IN (" . implode(", ", $suite_ids) . ") OR user_id=" . intval($user_id) . ") ORDER BY u.display_name";        
    }else{
        $query = "SELECT DISTINCT(p.esb_user_id) FROM $wpdb->prefix" . "users_purchases AS p LEFT JOIN $wpdb->users AS u ON u.ID = p.user_id WHERE  p.status='Active' ORDER BY u.display_name";
    }
    
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