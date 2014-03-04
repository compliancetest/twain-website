<?php

add_action('init', 'execute_subscription_actions');
function execute_subscription_actions()
{
    if(isset($_POST['_paymentnonce']) && wp_verify_nonce($_POST['_paymentnonce'], 'direct_payment'))
    {
        purchase_paid_subscription();        
    }else if(isset($_POST['_paymentnonce']) && wp_verify_nonce($_POST['_paymentnonce'], 'free_charge')){
        purchase_free_subscription();
    }else if(isset($_POST['_paymentnonce']) && wp_verify_nonce($_POST['_paymentnonce'], 'create_subscription')){
        purchase_additional_subscription();
    }else if(isset($_REQUEST['_paymentnonce']) && wp_verify_nonce($_REQUEST['_paymentnonce'], 'unsubscribe')){  
        unsubscribe();
    }else if(isset($_REQUEST['ext-action']) && wp_verify_nonce($_REQUEST['ext-action'], 'check-subscriptions')){
        $inarrearsCount = get_option('inarrears_count');
        $frozenCount = get_option('frozen_count');
    }else if(isset($_REQUEST['ext-action']) && $_REQUEST['ext-action'] == 'process_recurring_payment'){
        process_recurring_payment();
        process_suspended_subscriptions();
        process_inarrear_frozen_subscriptions();
        exit;
    }else if(isset($_REQUEST['ext-action']) && $_REQUEST['ext-action'] == 'increase_inarrear_frozen_count'){
        increase_inarrear_frozen_count();
        exit;
    }
}

function purchase_paid_subscription()
{
    global $wpdb, $CPRest;

    if(!is_user_logged_in())
    {
        echo "Permission Denied!";
        exit;
    }
    
    $user = get_userdata(get_current_user_id());
    
    $suite_id = $_POST['suite_id'];
    $suite = new TestSuite($suite_id);
    $suite->load();
    if(!$suite->id)
    {
        echo "Invalid Request!";
        exit;
    }
    
    $card = null;
    
    //Check Card info
    if(isset($_POST['card_id']) && $_POST['card_id'])
    {
        //Read Card Info
        $card = getUserCardById($_POST['card_id']);
    }
    if(!$card)
    {
        $_POST['card_expiry'] = $_POST['exp_month'] . "/" . $_POST['exp_year'];
        $_POST['id'] = '';
        $card_id = cp_user_payment_save();
        if(!is_int($card_id))
        {
            //Card Error
            echo $card_id;
            exit;
        }
        
        $card = getUserCardById($card_id);
    }
    
    if(!$card)
    {
        echo "There was an error while processing your purchase.";
        exit;        
    }
    
    $signup_fee = get_user_meta($user->ID, 'signup_fee', true);
    $monthly_fee = get_user_meta($user->ID, 'monthly_fee', true);
    
    if(isset($signup_fee[$suite->id]))
        $suite->signupPrice = doubleval($signup_fee[$suite->id]);
    if(isset($monthly_fee[$suite->id]))
        $suite->monthlySubscriptionPrice = doubleval($monthly_fee[$suite->id]);
    
    $paymentAmount = $suite->signupPrice + calculateFirstPaymentAmount($suite->monthlySubscriptionPrice);
    
    $result = processEwayPayment($card->customer_id, $paymentAmount, 'Subscription to ' . $suite->title . ' test suite');
    
    if($result['ewayTrxnStatus'] == 'True')
    {
        
        //Create MSH Datas 
        $esb_data = array(
            'p_mode_agreement' => 'LIGHT',
            'harness_endpoint_url' => 'http://esb.compliancetest.net/services/Superstream', 
            'harness_username' => $user->user_login . "_" . $suite->id, 
            'harness_password' => cp_generate_password(8)
        );
        
        //Save Billing Data to Database
        $wpdb->insert($wpdb->prefix . "users_purchases", array(
            'user_id' => $user->ID,
            'monthly_fee' => $suite->monthlySubscriptionPrice,
            'signup_fee' => $suite->signupPrice,
            'paid_amount' => $paymentAmount,
            'card_id' => $card->id,
            'created_date' => date('Y-m-d H:i:s'),
            'expiry_date' => date("Y-m-d", strtotime('first day next month')),
            'status' => 'Active',
            'inarrears_count' => 0,
            'frozen_count' => 0
        ));
        
        $purchase_id = $wpdb->insert_id;
        
        //Save Transaction
        $wpdb->insert($wpdb->prefix . 'users_transactions', array(
            "user_id" => $user->ID,
            "purchase_id" => $purchase_id,
            "trxn_number" => $result['ewayTrxnNumber'],
            "amount" => $paymentAmount,
            "auth_code" => $result['ewayAuthCode'],
            "created_date" => date("Y-m-d H:i:s")
        ));
        
        //Create subscription row
        $wpdb->insert($wpdb->prefix . "users_subscriptions", array(
            'user_id' => $user->ID,
            'suite_id' => $suite->id,
            'purchase_id' => $purchase_id,
            'subscribed_date' => date('Y-m-d H:i:s'),
            'esb_user_id' => 0,
            'harness_username' => $esb_data['harness_username'],
            'harness_password' => $esb_data['harness_password'],
            'harness_endpoint_url' => $esb_data['harness_endpoint_url'],
            'tester_username' => '',
            'tester_password' => '',
            'tester_endpoint_url' => '',
            'p_mode_agreement' => $esb_data['p_mode_agreement'],
            'status' => 'Active'
        ));
        
        $subscribe_id = $wpdb->insert_id;
        
        //Make this customer a member of the group
        if(!groups_is_user_member($user->ID, $suite->community_id))
        {
            groups_join_group($suite->community_id);
        }
        
        $group = groups_get_group( array('group_id' => $suite->community_id));

        //Send Email
        $emailData = array(
            '[name]' => cp_get_user_fullname($user->ID),
            '[email]' => $user->user_email,
            '[suite_name]' => $suite->title,
            '[suite_url]' => get_permalink($suite->id),
            '[paid_amount]' => $paymentAmount,
            '[signup_fee]' => $suite->signupPrice,
            '[monthly_fee]' => $suite->monthlySubscriptionPrice,
            '[community_url]' => bp_get_group_permalink($group),
            '[payment_email]' => $card->email
        );
        
        if($suite->monthlySubscriptionPrice == 0) //Signup Fee Only Subscription
        {            
            cp_send_email(array('name' => $emailData['[name]'], 'email' => $emailData['[email]']), 'purchase_signup_fee_only_subscription', $emailData);
            cp_send_email_to_admin('purchase_signup_fee_only_subscription_admin', $emailData);        
        }else{ //General Subscription
            cp_send_email(array('name' => $emailData['[name]'], 'email' => $emailData['[email]']), 'purchase_subscription', $emailData);
            cp_send_email_to_admin('purchase_subscription_admin', $emailData);        
        }
        
        echo 'success';
        
    }else{            
        if(isset($result['ewayTrxnError']))
            echo $result['ewayTrxnError'];
        else if(isset($result['faultstring']))
            echo $result['faultstring'];
        exit;
    }
    exit;
    
}

function purchase_free_subscription()
{
    global $wpdb, $CPRest;
      
    $suite_id = $_POST['suite_id'];
    $return = !$suite_id ? "/" : get_permalink($suite_id);
    
    if(!is_user_logged_in())
    {
        addMessage("Permission Denied!", "error");
        wp_redirect($return);
        exit;
    }
    
    $user_id = get_current_user_id();        
    $user = get_userdata($user_id);
    
    $suite = new TestSuite($suite_id);
    $suite->load();
    
    $signup_fee = get_user_meta($user->ID, 'signup_fee', true);
    $monthly_fee = get_user_meta($user->ID, 'monthly_fee', true);
    
    if(isset($signup_fee[$suite->id]))
        $suite->signupPrice = doubleval($signup_fee[$suite->id]);
    if(isset($monthly_fee[$suite->id]))
        $suite->monthlySubscriptionPrice = doubleval($monthly_fee[$suite->id]);
    
    $paymentAmount = $suite->signupPrice + calculateFirstPaymentAmount($suite->monthlySubscriptionPrice);    
    
    if($paymentAmount > 0)
    {
        addMessage("Invalid Request!", "error");
        wp_redirect($return);
        exit;
    }
    
    $group = groups_get_group( array('group_id' => $suite->community_id));
    
    if(!groups_is_user_member($user_id, $suite->community_id))
    {
        addMessage("You must join to this community to get the access of the test suite.", "error");
        wp_redirect(bp_get_group_permalink($group));
        exit;
    }
    
    //Create MSH Datas 
    $esb_data = array(
        'p_mode_agreement' => 'LIGHT',
        'harness_endpoint_url' => 'http://esb.compliancetest.net/services/Superstream', 
        'harness_username' => $user->user_login . "_" . $suite->id, 
        'harness_password' => cp_generate_password(8)
    );
    
    
    //Save Billing Data to Database
    $wpdb->insert($wpdb->prefix . "users_purchases", array(
        'user_id' => $user->ID,
        'monthly_fee' => 0,
        'signup_fee' => 0,
        'paid_amount' => 0,
        'card_id' => 0,
        'created_date' => date('Y-m-d H:i:s'),
        'expiry_date' => date("Y-m-d", strtotime('first day next month')),
        'status' => 'Active',
        'inarrears_count' => 0,
        'frozen_count' => 0
    ));
    
    $purchase_id = $wpdb->insert_id;
    
    //Create subscription row
    $wpdb->insert($wpdb->prefix . "users_subscriptions", array(
        'user_id' => $user->ID,
        'suite_id' => $suite->id,
        'purchase_id' => $purchase_id,
        'subscribed_date' => date('Y-m-d H:i:s'),
        'esb_user_id' => 0,
        'harness_username' => $esb_data['harness_username'],
        'harness_password' => $esb_data['harness_password'],
        'harness_endpoint_url' => $esb_data['harness_endpoint_url'],
        'tester_username' => '',
        'tester_password' => '',
        'tester_endpoint_url' => '',
        'p_mode_agreement' => $esb_data['p_mode_agreement'],
        'status' => 'Active'
    ));
    
    $subscribe_id = $wpdb->insert_id;
    
    //Send Email
    $emailData = array(
        '[name]' => cp_get_user_fullname($user->ID),
        '[email]' => $user->user_email,
        '[suite_name]' => $suite->title,
        '[suite_url]' => get_permalink($suite->id),        
        '[community_url]' => bp_get_group_permalink($group)
    );
    cp_send_email(array('name' => $emailData['[name]'], 'email' => $emailData['[email]']), 'purchase_free_subscription', $emailData);
    cp_send_email_to_admin('purchase_free_subscription_admin', $emailData);
    
//    addMessage("Your subscription has been proceeded successfully");
    echo 'success';
//    wp_redirect($return);
    exit;
  
}


function purchase_additional_subscription()
{
    global $wpdb, $CPRest;
    
    $suite_id = intval($_POST['suite_id']);
    $user_id = get_current_user_id();
    
    $return = !$suite_id ? "/" : get_permalink($suite_id);
    
    if(!$user_id || !$suite_id)
    {
//        addMessage("Invalid Request!", "error");
        echo "Invalid Request!";
//        wp_redirect($return);
        exit;
    }
    
    //Getting Family Mark
    $suite = new TestSuite($suite_id);
    $suite->load();
    
    $query = $wpdb->prepare("SELECT s.*, p.paid_amount, p.monthly_fee, p.signup_fee FROM {$wpdb->prefix}users_subscriptions AS s
                             INNER JOIN {$wpdb->prefix}test_suites AS ts ON s.suite_id=ts.suite_id
                             LEFT JOIN {$wpdb->prefix}users_purchases AS p ON p.id=s.purchase_id
                             WHERE ts.family_mark=%d AND s.user_id=%d", $suite->familyMark, $user_id);
    $rows = $wpdb->get_results($query);
    
    if(!$rows)
    {
        /*addMessage("Invalid Request!", "error");
        wp_redirect($return);*/
        echo "Invalid Request!";
        exit;
    }
    
    $purchase = $rows[0];
    
    $purchase_id = $rows[0]->purchase_id;
    $status = $rows[0]->status;
    
    $group = groups_get_group( array('group_id' => $suite->community_id));
    
    $user = get_userdata($user_id);
    
    //Create MSH Datas 
    $esb_data = array(
        'p_mode_agreement' => 'LIGHT',
        'harness_endpoint_url' => 'http://esb.compliancetest.net/services/Superstream', 
        'harness_username' => $user->user_login . "_" . $suite->id, 
        'harness_password' => cp_generate_password(8)
    );
    
    //Create subscription row
    $wpdb->insert($wpdb->prefix . "users_subscriptions", array(
        'user_id' => $user->ID,
        'suite_id' => $suite->id,
        'purchase_id' => $purchase_id,
        'subscribed_date' => date('Y-m-d H:i:s'),
        'esb_user_id' => 0,
        'harness_username' => $esb_data['harness_username'],
        'harness_password' => $esb_data['harness_password'],
        'harness_endpoint_url' => $esb_data['harness_endpoint_url'],
        'tester_username' => '',
        'tester_password' => '',
        'tester_endpoint_url' => '',
        'p_mode_agreement' => $esb_data['p_mode_agreement'],
        'status' => $status
    ));
    
    $subscribe_id = $wpdb->insert_id;
    
    //Send Email
    $emailData = array(
        '[name]' => cp_get_user_fullname($user->ID),
        '[email]' => $user->user_email,
        '[suite_name]' => $suite->title,
        '[suite_url]' => get_permalink($suite->id),
        '[paid_amount]' => $purchase->paid_amount,
        '[monthly_fee]' => $purchase->monthly_fee,
        '[signup_fee]' => $purchase->signup_fee,
        '[community_url]' => bp_get_group_permalink($group)
    );
    
    cp_send_email(array('name' => $emailData['[name]'], 'email' => $emailData['[email]']), 'purchase_additional_subscription', $emailData);
    cp_send_email_to_admin('purchase_additional_subscription_admin', $emailData);
    
//    addMessage("Your subscription has been proceeded successfully");
    
    echo 'success';
    
    exit;
}

function unsubscribe()
{
    global $wpdb, $CPRest;
    
    $return = isset($_REQUEST['return']) ? base64_decode($_REQUEST['return']) : '/my-test-suites';
    
    if(!is_user_logged_in())
    {
        addMessage("Permission Denied!", "error");
        wp_redirect("/");
        exit;
    }
    
    $user = get_userdata(get_current_user_id());

    $pId = $_REQUEST['id'];
    
    $subscription = new CT_Subscription($pId);

    if(!$subscription->id || $subscription->user_id != $user->ID)
    {
        addMessage('Invalid Request!', 'error');
        wp_redirect($return);
        exit;
    }

    if($subscription->status != 'Active' || isset($_POST['delete-now']))
    {
        //First Cancel Subscription
        /*if($subscription->status != 'Unsubscribing')
        {
            $subscription->cancel();
        }*/
        
        //Remove Subscription
        removeSubscription($subscription);
    }else{
        $subscription->cancel();
    }
    
    wp_redirect($return);
    exit;   
}


function process_recurring_payment()
{
    global $wpdb;
    
    $query = "SELECT p.*, c.customer_id FROM {$wpdb->prefix}users_purchases AS p LEFT JOIN {$wpdb->prefix}users_cards AS c ON c.id=p.card_id WHERE p.`status`='Active' AND p.expiry_date <= '" . date("Y-m-d") . "'";
    $subscriptions = $wpdb->get_results($query, ARRAY_A);
    foreach($subscriptions as $row)
    {
        //Monthly Billing
        $monthlyFee = getSubscriptionMonthlyFee($row->id, $row->user_id);
        
        if($monthlyFee > 0)
        {
            $result = processEwayPayment($row['customer_id'], $monthlyFee);    
        }
        
        
        
        
        if($result['ewayTrxnStatus'] == 'True')
        {
            //Save Transaction
            $wpdb->insert($wpdb->prefix . 'users_transactions', array(
                "user_id" => $row['user_id'],
                "purchase_id" => $row['purchase_id'],
                "trxn_number" => $result['ewayTrxnNumber'],
                "amount" => $monthlyFee,
                "auth_code" => $result['ewayAuthCode'],
                "created_date" => date("Y-m-d H:i:s")
            ));
            
            //Extend the period of the subscription
            $wpdb->update($wpdb->prefix . "users_purchases", array('expiry_date' => date("Y-m-d", strtotime('first day next month'))), array('id' => $row['id']));            
        }else{
            //Set the status to InArrears
            $subscription->inArrears();
        }
    }
    
}

function process_suspended_subscriptions()
{
    global $wpdb;
    
    $query = "SELECT p.*, c.customer_id FROM {$wpdb->prefix}users_purchases AS p LEFT JOIN {$wpdb->prefix}users_cards AS c ON c.id=p.card_id WHERE (p.`status`='InArrears' OR p.`status`='Frozen') AND c.`status`='Active'";
    $subscriptions = $wpdb->get_results($query, ARRAY_A);
    foreach($subscriptions as $row)
    {
        //Monthly Billing
        $currentPrice = get_post_meta($row['suite_id'], 'monthly_subscription_price', true);
        if($row['monthly_fee'] < $currentPrice)
            $row['monthly_fee'] = $currentPrice;
        
        $result = processEwayPayment($row['customer_id'], $row['monthly_fee']);
        
        $subscription = new CT_Subscription();
        $subscription->bind($row);
        
        if($result['ewayTrxnStatus'] == 'True')
        {            
            //Save Transaction
            $wpdb->insert($wpdb->prefix . 'users_transactions', array(
                "user_id" => $row['user_id'],
                "suite_id" => $row['suite_id'],
                "trxn_number" => $result['ewayTrxnNumber'],
                "amount" => $row['monthly_fee'],
                "auth_code" => $result['ewayAuthCode'],
                "created_date" => date("Y-m-d H:i:s")
            ));
            
            $subscription->active();
            
        }else{             
            //Set Card Status to Suspended
            $wpdb->update($wpdb->prefix . 'users_cards', array('status' => 'Suspended'), array('id' => $row['card_id']));
        }
        
    }
    
}

function increase_inarrear_frozen_count()
{
    global $wpdb;
    
    //Increase InArrears Count
    $query = "UPDATE {$wpdb->prefix}users_purchases SET `inarrears_count`=`inarrears_count` + 1 WHERE `status`='InArrears'";
    $wpdb->query($query);
    //Increase Frozen Count
    $query = "UPDATE {$wpdb->prefix}users_purchases SET `frozen_count`=`frozen_count` + 1 WHERE `status`='Frozen'";
    $wpdb->query($query);
    
}

function process_inarrear_frozen_subscriptions()
{
    global $wpdb;
    
    $InArrearsCount = get_option('inarrears_count');
    $FrozenCount = get_option('frozen_count');
    
    //Move subscription of expired InArrears Cound InArrears to Frozen 
    $query = "SELECT p.*, c.customer_id FROM {$wpdb->prefix}users_purchases AS p LEFT JOIN {$wpdb->prefix}users_cards AS c ON c.id=p.card_id WHERE p.`status`='InArrears' AND `inarrears_count` > " . $InArrearsCount;
    $rows = $wpdb->get_results($query, ARRAY_A);
    foreach($rows as $row)
    {
        $obj = new CT_Subscription($row);
        $obj->frozen();
    }
    
    //Move subscription of expired InArrears Cound InArrears to Frozen 
    $query = "SELECT p.*, c.customer_id FROM {$wpdb->prefix}users_purchases AS p LEFT JOIN {$wpdb->prefix}users_cards AS c ON c.id=p.card_id WHERE p.`status`='Frozen' AND `frozen_count` > " . $FrozenCount;
    $rows = $wpdb->get_results($query, ARRAY_A);
    foreach($rows as $row)
    {
        $obj = new CT_Subscription($row);
        $obj->delete();
    }
    
}