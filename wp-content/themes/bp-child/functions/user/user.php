<?php
/**
* The Gate Way function for users action
*/
require_once('user-auth.php');
require_once('user-profile.php');
require_once('user-transactions.php');
require_once('user-email-verifications.php');
require_once('user-delete-data.php');


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
    }else if($cpAction == 'email_activation'){
        cp_activate_email();
    }else if(wp_verify_nonce($cpAction,'my_details_edit')){
        cp_user_detail_edit();
    }else if(wp_verify_nonce($cpAction,'organisation_detail_edit')){
        cp_user_organisation_detail_edit();
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
    }else if(wp_verify_nonce($cpAction ,'my_organisation_join')){
        cp_user_organisation_join();
    }else if(wp_verify_nonce($cpAction ,'leave_organisation')){
        $org_membership = ct_get_user_organisation_membership( get_current_user_id() );
        if( ! $org_membership ){
            exit('You are not member of any organisation!');
        }
        if( ! $org_membership->is_admin ){
            $org_controller = new CT_Organisation_Controller();
            $org_controller->delete_membership( get_current_user_id(), $org_membership->organisation_id);
        }
        addMessage( 'Success!' );
        wp_redirect( '/my-profile/' );
        exit;
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
    }else if(wp_verify_nonce($cpAction, 'get-harness')){
        cp_get_customer_harness_detail();               
        exit;
    }else if(wp_verify_nonce($cpAction, 'get-harness-profile-data')){
        cp_get_customer_harness_detail_profile_data();               
        exit;
    }else if(wp_verify_nonce($cpAction, 'edit-transaction-log')){
        $result = cp_edit_transaction_log();               
        echo $result;
        exit;
    }else if(wp_verify_nonce($cpAction, 'download_file')){
        cp_download_log();
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
    }else if(wp_verify_nonce($cpAction, 'update-error-checking-action')){
        exit( cp_save_limited_error_checking() );
    }else if(wp_verify_nonce($cpAction, 'update-mandatory_response_validation-action')){
        exit( cp_save_mandatory_response_validation_checking() );
    }else if(wp_verify_nonce($cpAction, 'update-force-action')){
        exit( cp_save_force_checking() );
    }else if(wp_verify_nonce($cpAction, 'insufficient-privilege')){
        $privilege = base64_decode($_REQUEST['privilege']);
        
        $user_id = get_current_user_id();        
        $user_membership = ct_get_user_organisation_membership($user_id);
        
        if(isset($_REQUEST['new']) && !$user_membership)         {
            display_signup_organisation_box();
        } else {
            display_insufficient_privilege_box($privilege);    
        }
        exit;
    }
}

function cp_download_log(){
    global $wpdb;

    $row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM wp_users_transactions_files WHERE fileId = %s ",  $_REQUEST['id']  ) );

    header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
    header("Last-Modified: " . gmdate("D,d M Y H:i:s") . " GMT");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("Content-Type: Application/octet-stream");
    header("Content-disposition: attachment; filename=".$row->fileName);

    echo $row->content;
    exit;
}
function display_signup_organisation_box()
{
    ?>
    <div class="popup-box" style="display: none; width: 500px">
      <form name="" action="<?php echo site_url() ?>/index.php" method="post">
        <div class="popup-box-header radius6 noradiusbottom">Organisation Record Required</div>
        <div class="popup-box-content">
            An organisation record needs to be created for your organisation as products are owned by organisations. You can create a record via the Organisation section in your Profile tab.
        </div>
        <div class="popup-box-footer radius6 noradiustop">
            <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Close</span></a>
            <div class="clear"></div>
        </div>
        <a class="close_btn"></a>
      </form>
    </div>
    <?php
}

function display_insufficient_privilege_box($privilege)
{
    global $wpdb;
        
    $title = ct_get_privilege_by_code($privilege, 'title');
    ?>
    <div class="popup-box" style="display: none; width: 450px">
        <div class="popup-box-header radius6 noradiusbottom">Insufficient Privileges</div>
        <div class="popup-box-content"><p class="message error">You do not have the "<?php echo $title?>" privilege necessary for this action. Please contact your organisation administrator for the <?php echo get_site_title();?> site.</p></div>
        <div class="popup-box-footer radius6 noradiustop">
            <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Close</span></a>            
            <div class="clear"></div>
        </div>
        <a class="close_btn"></a>
    </div>
    <?php
    exit;
}

function getOrganisationID($user_id = null)
{
    global $wpdb;
    
    if(!$user_id)
        $user_id = get_current_user_id();
    
    $query = $wpdb->prepare("SELECT organisation_id FROM " . $wpdb->prefix . "organisations_members WHERE user_id=%d And is_admin=1", $user_id);
    $organisation_id = $wpdb->get_var($query);
    
    return $organisation_id;
}

function getOrganisationById($organisation_id = null)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "organisations WHERE id=%d", $organisation_id);
    $row = $wpdb->get_row($query);
    
    return $row;
}

function getUserCreditCards($user_id = null, $only_active = false)
{
    global $wpdb;
    
    if(!$user_id)
        $user_id = get_current_user_id();
    
    if($only_active)
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "organisations_payment_methods WHERE user_id=%d And `status`='Active'", $user_id);
    else
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "organisations_payment_methods WHERE user_id=%d", $user_id);
    $rows = $wpdb->get_results($query);
    
    return $rows;
}

function getUserCardById($card_id, $user_id = null)
{
    global $wpdb;
    
    if( ! $user_id) {
        $user_id = get_current_user_id();
    }
    
    $row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM wp_organisations_payment_methods WHERE user_id = %d AND id = %d ", $user_id, $card_id) );
    
    return $row;
}
function getOrganisationCardById( $card_id, $organisation_id )
{
    global $wpdb;

    return $wpdb->get_row( $wpdb->prepare("SELECT * FROM wp_organisations_payment_methods WHERE organisation_id = %d AND id = %d ", $organisation_id, $card_id ) );
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
    global $wpdb;

    return $wpdb->get_results($wpdb->prepare("SELECT c.* FROM communities AS c
                                              JOIN communities_members AS cm ON c.id = cm.community_id
                                              WHERE cm.user_id = %d AND is_admin = 1", $user_id));
}

function getUserCommunities($user_id)
{
    global $wpdb;
    return $wpdb->get_results($wpdb->prepare("SELECT c.*, cm.created_at as membership_date, cm.is_admin FROM communities AS c
                                              JOIN communities_members AS cm ON c.id = cm.community_id
                                              WHERE cm.user_id = %d", $user_id));
}

function doesUserCommunityAdmin($user_id, $communityId)
{
    global $wpdb;
    return $wpdb->get_row($wpdb->prepare("SELECT c.* FROM communities AS c
                                          JOIN communities_members AS cm ON c.id = cm.community_id
                                          WHERE cm.user_id = %d AND is_admin = 1 AND c.id = %s", $user_id, $communityId));
}

function getUserPurchase($suite_id = null, $user_id = null)
{
    global $wpdb;
    
    if($user_id == null)
        $user_id = get_current_user_id();
    
    if($suite_id == null){
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "users_subscriptions WHERE user_id=%d GROUP BY id", $user_id);
        $result = $wpdb->get_results($query);
    }else{
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "users_subscriptions WHERE user_id=%d AND suite_id=%d GROUP BY id", $user_id, $suite_id);
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
        $query = $wpdb->prepare("SELECT s.*, p.post_title AS suite_title, os.status FROM " . $wpdb->prefix . "users_subscriptions AS s 
                                 LEFT JOIN {$wpdb->posts} AS p ON p.ID=s.suite_id
                                 LEFT JOIN {$wpdb->prefix}organisations_subscriptions AS os ON os.id=s.parent_id
                                 WHERE s.user_id=%d", $user_id);
    else
        $query = $wpdb->prepare("SELECT s.*, p.post_title AS suite_title, os.status FROM " . $wpdb->prefix . "users_subscriptions AS s 
                                 LEFT JOIN {$wpdb->posts} AS p ON p.ID=s.suite_id
                                 LEFT JOIN {$wpdb->prefix}organisations_subscriptions AS os ON os.id=s.parent_id
                                 WHERE s.user_id=%d AND os.status != 'Frozen'", $user_id);
    
    $query .= " ORDER BY suite_title";

    $result = $wpdb->get_results($query);
    
    return $result;
    
}

function getSubscribersBySuiteId($suite_id)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT user_id, u.user_email FROM " . $wpdb->prefix . "users_subscriptions p LEFT JOIN " . $wpdb->users . " AS u ON p.user_id=u.ID WHERE suite_id=%d AND status='Active'", $suite_id);
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
    
    $purchases = getUserSubscriptions( $user_id );
    $community_ids = array();
    foreach($purchases as $r) {
        $temp_community_id = get_post_meta($r->suite_id, 'community_id', true);
        if( ! empty( $temp_community_id ) ) {
            $community_ids[] = $temp_community_id;
        }
    }
    
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
    
    $query = $wpdb->prepare("SELECT suite_id FROM " . $wpdb->prefix . "users_subscriptions WHERE user_id=%d AND `status`='Active'", $user_id);    
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
            
        $query = "SELECT DISTINCT(p.user_id) as CUSTOMER_ID, u.display_name AS CUSTOMER_NAME FROM $wpdb->prefix" . "users_subscriptions AS p LEFT JOIN $wpdb->users AS u ON u.ID = p.user_id WHERE p.status='Active' AND p.suite_id IN (" . implode(", ", $suite_ids) . ") ORDER BY u.display_name";        
    }else{
        $query = "SELECT DISTINCT(p.user_id) as CUSTOMER_ID, u.display_name AS CUSTOMER_NAME FROM $wpdb->prefix" . "users_subscriptions AS p LEFT JOIN $wpdb->users AS u ON u.ID = p.user_id WHERE  p.status='Active' ORDER BY u.display_name";
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
            
        $query = "SELECT DISTINCT(p.user_id) as CUSTOMER_ID FROM $wpdb->prefix" . "users_subscriptions AS p WHERE p.status='Active' AND p.suite_id IN (" . implode(", ", $suite_ids) . ")";        
    }else{
        $query = "SELECT DISTINCT(p.user_id) as CUSTOMER_ID FROM $wpdb->prefix" . "users_subscriptions AS p WHERE  p.status='Active'";
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
            
        $query = "SELECT p.id as CUSTOMER_ID FROM $wpdb->prefix" . "users_subscriptions AS p WHERE p.status='Active' AND p.suite_id IN (" . implode(", ", $suite_ids) . ")";        
    }else{
        $query = "SELECT p.id as CUSTOMER_ID FROM $wpdb->prefix" . "users_subscriptions AS p WHERE  p.status='Active'";
    }
    
    $ids = $wpdb->get_col($query);
    
    return $ids;
}

/***
* Getting User Customer ESB IDs as well as Managed customer ids
* 
* @param mixed $user_id
*/
function getUserAllCustomerESBIDs($user_id = null)
{
    global $wpdb;
    
    if($user_id == null)
        $user_id = get_current_user_id();
    
    if(!is_super_admin())
    {   
        $org_ids = array();
        
        $user_membership = ct_get_user_organisation_membership($user_id);
        if ($user_membership) {
            $org_ids[] = $user_membership->organisation_id;
        }
                           
        if ( ct_is_group_admin_or_support() ){
            $orgs = ct_get_user_viewable_organisations();
            foreach( $orgs AS $org ){
                $org_ids[] = $org->id;
            }
        }
        
        if (!$org_ids) {
            return array();
        }
        
        $query = "SELECT id AS CUSTOMER_ID FROM {$wpdb->prefix}organisations_subscriptions WHERE  organisation_id IN (" . implode(", ", $org_ids) . ")";
        
    }else{
        $query = "SELECT DISTINCT(s.id) as CUSTOMER_ID FROM {$wpdb->prefix}organisations_subscriptions AS s ";
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

/**
* Check user is 
* 
* @param Int $customer_id
* @param Int $user_id
*/
function cp_is_customer_admin($customer_id, $user_id = false)
{
    global $wpdb;
    
    if(!$user_id)
        $user_id = get_current_user_id();
    
    if(!$user_id)
        return false;
    
    //Getting Community IDs
    $query = $wpdb->prepare("SELECT count(id) FROM " . $wpdb->prefix . "bp_groups_members WHERE is_admin=1 AND user_id=%d AND group_id IN 
        (SELECT DISTINCT(group_id) FROM " . $wpdb->prefix . "bp_groups_members WHERE user_id=%d AND is_confirmed=1)", $user_id, $customer_id);
    
    $c = $wpdb->get_var($query);
    
    return $c > 0 ? true : false;
}

function cp_is_customer_support($customer_id, $user_id = false)
{
    global $wpdb;
    
    if(!$user_id)
        $user_id = get_current_user_id();
    
    if(!$user_id)
        return false;
    
    //Getting Community IDs
    $query = $wpdb->prepare("SELECT count(id) FROM " . $wpdb->prefix . "bp_groups_members WHERE is_mod=1 AND user_id=%d AND group_id IN 
        (SELECT DISTINCT(group_id) FROM " . $wpdb->prefix . "bp_groups_members WHERE user_id=%d AND is_confirmed=1)", $user_id, $customer_id);
    
    $c = $wpdb->get_var($query);
    
    return $c > 0 ? true : false;
}

function cp_is_customer_support_or_admin($customer_id, $user_id = false)
{
    global $wpdb;
    
    if(!$user_id)
        $user_id = get_current_user_id();
    
    if(!$user_id)
        return false;
    
    //Getting Community IDs
    $query = $wpdb->prepare("SELECT count(id) FROM " . $wpdb->prefix . "bp_groups_members WHERE (is_mod=1 OR is_admin=1) AND user_id=%d AND group_id IN 
        (SELECT DISTINCT(group_id) FROM " . $wpdb->prefix . "bp_groups_members WHERE user_id=%d AND is_confirmed=1)", $user_id, $customer_id);
    
    $c = $wpdb->get_var($query);
    
    return $c > 0 ? true : false;
}

function cp_update_user_cards_count($user_id)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT count(*) FROM {$wpdb->prefix}organisations_payment_methods WHERE user_id=%d", $user_id);    
    $cards = $wpdb->get_var($query);
    
    $wpdb->update($wpdb->prefix . "users_extra", array('cards' => $cards), array('userID' => $user_id));
    
}

function cp_update_user_subscriptions_count($user_id)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT count(*) FROM {$wpdb->prefix}users_subscriptions WHERE user_id=%d", $user_id);    
    $subscriptions = $wpdb->get_var($query);
    
    $wpdb->update($wpdb->prefix . "users_extra", array('subscriptions' => $subscriptions), array('userID' => $user_id));
}

