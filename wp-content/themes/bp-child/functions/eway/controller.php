<?php

add_action('init', 'process_eway_payment');
function process_eway_payment()
{
    global $wpdb;
    
    $webserviceURL = get_eway_token_webservice_url();
    $customerID = get_eway_customer_id();
    $userName = get_eway_user_name();
    $userPWD = get_eway_user_pwd();
    
    
    if(isset($_POST['_paymentnonce']) && wp_verify_nonce($_POST['_paymentnonce'], 'direct_payment'))
    {        
        
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
        
        require_once(THE_FUNCTION . '/soap/nusoap.php');
        
        $client = new nusoap_client($webserviceURL, false);
        $err = $client->getError();
        
        if ($err) {
            return 'Soap Construction Error: ' . $err;
        }
        
        $client->namespaces['man'] = 'https://www.eway.com.au/gateway/managedpayment';
        $headers = "<man:eWAYHeader><man:eWAYCustomerID>" . $customerID . "</man:eWAYCustomerID><man:Username>" . $userName . "</man:Username><man:Password>" . $userPWD . "</man:Password></man:eWAYHeader>";
        $client->setHeaders($headers);    
        
        $requestbody = array(
            'man:managedCustomerID' => $card->customer_id,
            'man:amount' => $suite->monthlySubscriptionPrice * 100,
            'man:cvn' => $card->cvn,
            //'man:invoiceReference' => '',
//            'man:invoiceDescription' => ''
        );
        $soapaction = 'https://www.eway.com.au/gateway/managedpayment/ProcessPayment';
        $result = $client->call('man:ProcessPayment', $requestbody, '', $soapaction);
        
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
                "amount" => $suite->monthlySubscriptionPrice * 100,
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
                'customer_id' => $card->customer_id,
                'esb_user_id' => 0,
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
                '[paid_amount]' => $suite->monthlySubscriptionPrice,
                '[community_url]' => bp_get_group_permalink($group)
            );
            cp_send_email(array('name' => $emailData['[name]'], 'email' => $emailData['[email]']), 'purchase_subscription', $emailData);
            cp_send_email_to_admin('purchase_subscription', $emailData);
            
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
            
            $result = sendRestUserAction('/user/create', $data);
            
            $resultDoc = new DOMDocument();
            
            if(!$result || !$resultDoc->loadXML($result))
            {
                echo "Payment was proceed successfully with an error.";
            }else{
                if($resultDoc->getElementsByTagName('code')->item(0)->nodeValue == 'ERROR')
                {
                    echo 'Your payment was proceed successfully, But there was an error.' . $resultDoc->getElementsByTagName('error')->item(0)->nodeValue;
                }else{            
                    $wpdb->update($wpdb->prefix . "users_purchases", array('esb_user_id' => $resultDoc->getElementsByTagName('userId')->item(0)->nodeValue), array('id' => $id));
                    echo 'success';
                }
            }
            
        }
        exit;
    }
    
}

add_action('init', 'free_charge');
function free_charge()
{
    global $wpdb;
    
    if(isset($_GET['_paymentnonce']) && wp_verify_nonce($_GET['_paymentnonce'], 'free_charge'))
    {        
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
        
        $result = sendRestUserAction('/user/create', $data);
        
        $resultDoc = new DOMDocument();
        
        if(!$result || !$resultDoc->loadXML($result))
        {
            addMessage("There was an error while processing your request!", 'error');                        
        }else{
            if($resultDoc->getElementsByTagName('code')->item(0)->nodeValue == 'ERROR')
            {
                addMessage($resultDoc->getElementsByTagName('error')->item(0)->nodeValue, "error");                
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
                    'customer_id' => 0,
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
}
add_action('init', 'unsubscribe_purchase');
function unsubscribe_purchase()
{
    global $wpdb;
    
    if(isset($_REQUEST['_paymentnonce']) && wp_verify_nonce($_REQUEST['_paymentnonce'], 'unsubscribe'))
    {        
        $return = isset($_REQUEST['return']) ? base64_decode($_REQUEST['return']) : '/my-profile';
        
        if(!is_user_logged_in())
        {
            addMessage("Permission Denied!", "error");
            wp_redirect("/");
            exit;
        }
        
        $user = get_userdata(get_current_user_id());
        
        $pId = $_REQUEST['id'];
        
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . 'users_purchases WHERE user_id=%d AND id=%d AND `status`="Active"', $user->ID, $pId);
        $purchase = $wpdb->get_row($query);
        if(!$purchase)
        {
            addMessage('Invalid Request!', 'error');
            wp_redirect($return);
            exit;
        }
        
        //Remove Backend Account
        $data = '<api:deleteUserRequest xmlns:api="http://compliancetest.net/api">
                    <api:user>
                        <api:userId>' . $purchase->esb_user_id . '</api:userId>                        
                    </api:user>
                </api:deleteUserRequest>';
        
        $result = sendRestUserAction('/user/delete', $data);
        
        $resultDoc = new DOMDocument(); 
        
        if(!$result || !$resultDoc->loadXML($result))
        {
            addMessage("There was an error while cancelling your subscription.", "error");
            
        }else{
            if($resultDoc->getElementsByTagName('code')->item(0)->nodeValue == 'ERROR')
            {
                addMessage('Error:' . $resultDoc->getElementsByTagName('error')->item(0)->nodeValue, 'error');
            }else{            
                
                addMessage('Your subscription has been cancelled.');
                //Change status to canceled 
                $wpdb->update($wpdb->prefix . 'users_purchases', array('status' => 'Cancelled'), array('id' => $purchase->id));            
                $suite = new TestSuite($purchase->suite_id);
                $suite->load();
                //Send Mail
                $emailData = array(
                    '[name]' => cp_get_user_fullname($user->ID),
                    '[email]' => $user->user_email,
                    '[suite_name]' => $suite->name,
                    '[suite_url]' => get_permalink($suite->id),
                    '[paid_amount]' => $suite->monthlySubscriptionPrice
                );
                cp_send_email(array('name' => $emailData['[name]'], 'email' => $emailData['[email]']), 'cancel_subscription', $emailData);
                cp_send_email_to_admin('cancel_subscription_admin', $emailData);            
            }
        }
        
        wp_redirect($return);
        exit;
    }    
}

