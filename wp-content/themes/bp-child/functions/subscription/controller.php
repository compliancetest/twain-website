<?php

add_action('init', 'execute_subscription_actions');
function execute_subscription_actions()
{
    if(isset($_POST['_paymentnonce']) && wp_verify_nonce($_POST['_paymentnonce'], 'direct_payment'))
    {
        process_eway_payment();        
    }else if(isset($_GET['_paymentnonce']) && wp_verify_nonce($_GET['_paymentnonce'], 'free_charge')){
        free_charge();
    }else if(isset($_REQUEST['_paymentnonce']) && wp_verify_nonce($_REQUEST['_paymentnonce'], 'unsubscribe')){  
        unsubscribe_purchase();
    }else if(isset($_REQUEST['ext-action']) && wp_verify_nonce($_REQUEST['ext-action'], 'check-subscriptions')){
        $inarrearsCount = get_option('inarrears_count');
        $frozenCount = get_option('frozen_count');
    }else if(isset($_REQUEST['ext-action']) && $_REQUEST['ext-action'] == 'process_recurring_payment'){
        process_recurring_payment();
    }
}

function process_eway_payment()
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
    
    $paymentAmount = calculateFirstPaymentAmount($suite->monthlySubscriptionPrice);
    
    $result = processEwayPayment($card->customer_id, $paymentAmount);
    
    if($result['ewayTrxnStatus'] == 'False')
    {
        echo $result['ewayTrxnError'];
        exit;
    }else{            
        //Save Transaction
        $wpdb->insert($wpdb->prefix . 'users_transactions', array(
            "user_id" => $user->ID,
            "suite_id" => $suite->id,
            "trxn_number" => $result['ewayTrxnNumber'],
            "amount" => $paymentAmount,
            "auth_code" => $result['ewayAuthCode'],
            "created_date" => date("Y-m-d H:i:s")
        ));
        //Create MSH Datas 
        $esb_data = array(
            'p_mode_agreement' => 'LIGHT',
            'harness_endpoint_url' => 'http://esb.compliancetest.net/services/LoggingProxy/mediate', 
            'harness_username' => $user->user_login . "_" . $suite->id, 
            'harness_password' => cp_generate_password(8)
        );
        
        //Save Billing Data to Database
        $id = $wpdb->insert($wpdb->prefix . "users_purchases", array(
            'user_id' => $user->ID,
            'suite_id' => $suite->id,
            'price' => $suite->monthlySubscriptionPrice,
            'paid_amount' => $paymentAmount,
            'card_id' => $card->id,
            'esb_user_id' => 0,
            'harness_endpoint_url' => $esb_data['harness_endpoint_url'],
            'harness_username' => $esb_data['harness_username'],
            'harness_password' => $esb_data['harness_password'],
            'p_mode_agreement' => $esb_data['p_mode_agreement'],
            'tester_endpoint_url' => '',
            'tester_username' => '',
            'tester_password' => '',
            'status' => 'Active',
            'expiry_date' => date("Y-m-d", strtotime('first day next month')),
            'created_date' => date('Y-m-d H:i:s')
        ));
        
        $id = $wpdb->insert_id;
        
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
            '[suite_name]' => $suite->name,
            '[suite_url]' => get_permalink($suite->id),
            '[paid_amount]' => $paymentAmount * 100,
            '[community_url]' => bp_get_group_permalink($group)
        );
        cp_send_email(array('name' => $emailData['[name]'], 'email' => $emailData['[email]']), 'purchase_subscription', $emailData);
        cp_send_email_to_admin('purchase_subscription_admin', $emailData);
        
        //Create Backend Customer Using SOAP            
        $data = '<api:createUserRequest xmlns:api="http://compliancetest.net/api">
                    <api:user>
                        <api:username>' . $esb_data['harness_username'] . '</api:username>
                        <api:password>' . $esb_data['harness_password'] . '</api:password>
                        <api:userGroups>
                            <api:group>
                                <api:groupId>' . $suite->community_id . '</api:groupId>
                                <api:groupName>' . bp_get_group_name($group) . '</api:groupName>
                            </api:group>
                        </api:userGroups>                       
                        <api:userPModeAgreement>' . $esb_data['p_mode_agreement'] . '</api:userPModeAgreement>                            
                        <api:userEndpoint />
                        <api:userEndpointUsername />
                        <api:userEndpointPassword />
                    </api:user>
                </api:createUserRequest>';
        
        $result = $CPRest->doUserAPI('user/create', $data);
        $resultDoc = new DOMDocument();
        
        if(!$result || !$resultDoc->loadXML($result))
        {
            echo 'Your payment was processed successfully, but there was a problem creating your test credentials. Please try again later by updating your test harness access details in the "My Test Suite Subscriptions" section of the dashboard.';
        }else{                
            if($resultDoc->getElementsByTagName('code')->item(0)->nodeValue == 'ERROR')
            {
                echo 'Your payment was processed successfully, but there was a problem creating your test credentials: ' . $resultDoc->getElementsByTagName('error')->item(0)->nodeValue . '. Please try again later by updating your test harness access details in the "My Test Suite Subscriptions" section of the dashboard.';
            }else{            
                $wpdb->update($wpdb->prefix . "users_purchases", array('esb_user_id' => $resultDoc->getElementsByTagName('userId')->item(0)->nodeValue), array('id' => $id));
                echo 'success';
            }
        }
        
    }
    exit;
    
}

function free_charge()
{
    global $wpdb, $CPRest;
      
    $suite_id = $_GET['suite_id'];
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
    
    if($suite->monthlySubscriptionPrice > 0)
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
        'harness_endpoint_url' => 'http://esb.compliancetest.net/services/LoggingProxy/mediate', 
        'harness_username' => $user->user_login . "_" . $suite->id, 
        'harness_password' => cp_generate_password(8)
    );
    
    //Create Backend Customer Using SOAP            
    $data = '<api:createUserRequest xmlns:api="http://compliancetest.net/api">
                    <api:user>
                        <api:username>' . $esb_data['harness_username'] . '</api:username>
                        <api:password>' . $esb_data['harness_password'] . '</api:password>
                        <api:userGroups>
                            <api:group>
                                <api:groupId>' . $suite->community_id . '</api:groupId>
                                <api:groupName>' . bp_get_group_name($group) . '</api:groupName>
                            </api:group>
                        </api:userGroups>                       
                        <api:userPModeAgreement>' . $esb_data['p_mode_agreement'] . '</api:userPModeAgreement>                            
                        <api:userEndpoint />
                        <api:userEndpointUsername />
                        <api:userEndpointPassword />
                    </api:user>
                </api:createUserRequest>';
    
    
    $result = $CPRest->doUserAPI('user/create', $data);
    
    $resultDoc = new DOMDocument();
    
    if(!$result || !$resultDoc->loadXML($result))
    {
        addMessage('There was a problem creating your test credentials. Please try again later by updating your test harness access details in the "My Test Suite Subscriptions" section of the dashboard.', 'error');                        
    }else{
        if($resultDoc->getElementsByTagName('code')->item(0)->nodeValue == 'ERROR')
        {
            addMessage("There was a problem creating your test credentials: " . $resultDoc->getElementsByTagName('error')->item(0)->nodeValue . '. Please try again later by updating your test harness access details in the "My Test Suite Subscriptions" section of the dashboard.', "error");                
        }else{            
            //Send Email
            $emailData = array(
                '[name]' => cp_get_user_fullname($user->ID),
                '[email]' => $user->user_email,
                '[suite_name]' => $suite->name,
                '[suite_url]' => get_permalink($suite->id),
                '[paid_amount]' => $suite->monthlySubscriptionPrice,
                '[community_url]' => bp_get_group_permalink($group)
            );
            cp_send_email(array('name' => $emailData['[name]'], 'email' => $emailData['[email]']), 'purchase_subscription', $emailData);
            cp_send_email_to_admin('purchase_subscription_admin', $emailData);
            
            //Save Billing Data to Database
            $id = $wpdb->insert($wpdb->prefix . "users_purchases", array(
                'user_id' => $user->ID,
                'suite_id' => $suite->id,
                'price' => 0.0,
                'card_id' => 0,
                'esb_user_id' => $resultDoc->getElementsByTagName('userId')->item(0)->nodeValue,
                'harness_endpoint_url' => $esb_data['harness_endpoint_url'],
                'harness_username' => $esb_data['harness_username'],
                'harness_password' => $esb_data['harness_password'],
                'p_mode_agreement' => $esb_data['p_mode_agreement'],
                'tester_endpoint_url' => '',
                'tester_username' => '',
                'tester_password' => '',
                'status' => 'Active',
                'expiry_date' => date("Y-m-d", strtotime('+1 month')),
                'created_date' => date('Y-m-d H:i:s')
            ));
            
            $id = $wpdb->insert_id;
            
            addMessage("Your subscription has been proceeded successfully");
            
        }
    }
    wp_redirect($return);
    exit;
  
}

function unsubscribe_purchase()
{
    global $wpdb, $CPRest;
    
    $return = isset($_REQUEST['return']) ? base64_decode($_REQUEST['return']) : '/my-profile';
    
    if(!is_user_logged_in())
    {
        addMessage("Permission Denied!", "error");
        wp_redirect("/");
        exit;
    }
    
    $user = get_userdata(get_current_user_id());
    
    $pId = $_REQUEST['id'];
    
    $subscription = new CT_Subscription($pId);

    if(!$subscription->id || $subscription->user_id != $user->ID || $subscription->status == 'Unsubscribing')
    {
        addMessage('Invalid Request!', 'error');
        wp_redirect($return);
        exit;
    }
    
    /*** For Testing ***/
    removeSubscription($subscription);
    wp_redirect($return);
    exit;
    
    if($subscription->status != 'Active')
    {
        //Unsubscribe the purchasement now
        removeSubscription($subscription);
    }else{
        //Just Update the Status to Unsubscribing        
        $subscription->cancel();
        
        addMessage("Your request has been sent successfully. Your subscription will be cancelled at the end of the month.");
    }
    
    wp_redirect($return);
    exit;   
}

function process_recurring_payment()
{
    global $wpdb;
    
    $query = "SELECT p.*, c.customer_id FROM {$wpdb->prefix}users_purchases AS p LEFT JOIN {$wpdb->prefix}users_cards AS c ON c.id=p.card_id WHERE p.`status`='Active'";
    $subscriptions = $wpdb->get_results($query);
    foreach($subscriptions as $subscription)
    {
        //Monthly Billing
        $currentPrice = get_post_meta($subscription->id, 'monthly_subscription_price', true);
        $paymentAmount = $currentPrice > $subscription->price ? $subscription->price : $currentPrice;
        
        $result = processEwayPayment($card->customer_id, $paymentAmount);
        
        $user = get_userdata($subscription->user_id);
        
        $suite = new TestSuite($subscription->suite_id);
        $suite->load();
        
        if($result['ewayTrxnStatus'] == 'False')
        {
            
            exit;
        }else{                        
            //Save Transaction
            $wpdb->insert($wpdb->prefix . 'users_transactions', array(
                "user_id" => $subscription->user_id,
                "suite_id" => $subscription->suite_id,
                "trxn_number" => $result['ewayTrxnNumber'],
                "amount" => $paymentAmount,
                "auth_code" => $result['ewayAuthCode'],
                "created_date" => date("Y-m-d H:i:s")
            ));
            //Extend the period of the subscription
            $wpdb->update($wpdb->prefix . "users_purchases", array('expiry_date' => date("Y-m-d", strtotime('first day next month'))), array('id' => $subscription->id));
            
        }
        
    }
    
    exit;
}