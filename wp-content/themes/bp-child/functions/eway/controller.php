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
        
        //Create Recurring Payment
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
            $msh = array('mode' => 'PUSH', 'url' => '', 'username' => $user->user_login . "_" . $suite->id, 'password' => wp_generate_password(15, true));
            
            //Save Billing Data to Database
            $id = $wpdb->insert($wpdb->prefix . "users_purchases", array(
                'user_id' => $user->ID,
                'suite_id' => $suite->id,
                'customer_id' => $card->customer_id,
                'esb_username' => $msh['username'],
                'msh_p_mode' => $msh['mode'],
                'msh_url' => $msh['url'],
                'msh_username' => $msh['username'],
                'msh_password' => $msh['password'],
                'status' => 'Active',
                'expiry_date' => date("Y-m-d", strtotime('+1 month')),
                'created_date' => date('Y-m-d H:i:s')
            ));
            //Make this customer a member of the group
            if(!groups_is_user_member($user->ID, $suite->community_id))
            {
                groups_join_group($suite->community_id);
            }
            
            $group = groups_get_group( array('group_id' => $suite->community_id));
            
            //Create Backend Customer Using SOAP
            $esb_client = new nusoap_client('http://esb.test.compliancetest.net:8280/api/integrationAPI', false);            
            $esb_client->namespaces['man'] = 'http://compliancetest.net/messaging';            
            $requestbody = array(
                'msg:user' => array(
                    'msg:username' => $msh['username'],
                    'msg:userGroups' => array(
                        'msg:group' => array(
                            'msg:groupID' => $suite->community_id,
                            'msg:groupName' => bp_get_group_name($group)
                        )        
                    ),                    
                    'msg:userPMode' => $msh['mode'],
                    'msg:userEndpoint' => $msh['url'],
                    'msg:userEndpointUsername' => $msh['username'],
                    'msg:userEndpointPassword' => $msh['password'],
                )
            );
            $soapaction = '/user/create';
            $result = $esb_client->call('msg:createUserRequest', $requestbody, '', $soapaction);
            print_r($requestbody);
            print_r($result);
            
            if(!$id)
            {
                echo 'Your payment successfully sent! But there was an error while storing your purchase.';
            }else{
                echo 'success';    
            }
            
        }
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
        
        
        addMessage('Your subscription has been cancelled.');
        //Change status to canceled 
        $wpdb->update($wpdb->prefix . 'users_purchases', array('status' => 'Cancelled'), array('id' => $purchase->id));            
        
        
        wp_redirect($return);
        exit;
    }    
}