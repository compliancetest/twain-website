<?php

add_action('init', 'process_eway_payment');
function process_eway_payment()
{
    global $wpdb;
    
    $paymentURL = get_eway_payment_url();
    $webserviceURL = get_eway_rebill_webservice_url();
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
            $card_id = cp_user_payment_edit();
            if(!is_int($card_id))
            {
                //Card Error
                echo $card_id;
                exit;
            }
            
            $card = getUserCardById($card_id);
        }
        
        require_once(dirname(__FILE__) . "/EwayPayment.php");
        
        $payment = new EwayPayment($customerID, $paymentURL);
        
        $payment->setCustomerFirstname(get_user_meta($user->ID, 'first_name', true)); 
        $payment->setCustomerLastname(get_user_meta($user->ID, 'last_name', true));
        $payment->setCustomerEmail($user->user_email);
        //$payment->setCustomerAddress();
//        $payment->setCustomerPostcode();
//        $payment->setCustomerInvoiceDescription();
//        $payment->setCustomerInvoiceRef();
//        $payment->setTrxnNumber();
        $payment->setCardHoldersName($card->name);
        $payment->setCardNumber($card->card_number);
        list($month, $year) = explode("/", $card->expiry);
        $payment->setCardExpiryMonth(trim($month));
        $payment->setCardExpiryYear(trim($year));
        $payment->setTotalAmount($suite->monthlySubscriptionPrice * 100);
        $payment->setCVN($card->cvc);
        if( $payment->doPayment() == EWAY_TRANSACTION_OK ) {
            update_user_meta($user->ID,'suite_auth_code' . $suite->id, $payment->getAuthCode());
            echo 'success';
        } else {
            echo "Error occurred (".$payment->getError()."): " . $payment->getErrorMessage();
        }
        
        //Create Recurring Payment
        require_once(THE_FUNCTION . '/soap/nusoap.php');
        
        $client = new nusoap_client($webserviceURL, false);
        if ($err) {
            echo 'Constructor error: ' . $err . '';
            exit();
        }
        //First Create Direct Payment
        
        $client->namespaces['man'] = 'http://www.eway.com.au/gateway/rebill/manageRebill';
        $headers = "<man:eWAYHeader><man:eWAYCustomerID>" . $customerID . "</man:eWAYCustomerID><man:Username>" . $userName . "</man:Username><man:Password>" . $userPWD . "</man:Password></man:eWAYHeader>";        
        $client->setHeaders($headers);
        
        //Check if this user has Rebill Customer ID or not
        $rebillCustomerID = get_user_meta($user->ID, 'rebill_customer_id', true);
        if(!$rebillCustomerID)
        {
            //Create Rebill Customer
            $requestbody = array(        
                'man:customerTitle' => 'Dear',
                'man:customerFirstName' => get_user_meta($user->ID, 'first_name', true),
                'man:customerLastName' => get_user_meta($user->ID, 'last_name', true),
                'man:customerRef' => 'CP' . $user->ID,
                'man:customerAddress' => '',
                'man:customerSuburb' => '',
                'man:customerState' => '',
                'man:customerCompany' => '',
                'man:customerPostCode' => '',
                'man:customerCountry' => '',
                'man:customerEmail' => $user->user_email,
                'man:customerFax' => '',
                'man:customerPhone1' => '',
                'man:customerPhone2' => '',
                'man:customerJobDesc' => '',
                'man:customerComments' => '',
                'man:customerURL' => ''
            );
            $soapaction = 'http://www.eway.com.au/gateway/rebill/manageRebill/CreateRebillCustomer';
            $result = $client->call('man:CreateRebillCustomer', $requestbody, '', $soapaction);
            if($result['Result'] == 'Fail')
            {
                echo $result['ErrorSeverity'] . ": " . $result['ErrorDetails'];
                exit;
            }else{
                $rebillCustomerID = $result['RebillCustomerID'];
                update_user_meta($user->ID, 'rebill_customer_id', $rebillCustomerID);
            }
        }
        $end_date = date("m/d/Y" ,strtotime($month . "/" . date('d') . "/" . $year));
        
        //Create Recurring Payment Event
        $requestbody = array(
            'man:RebillCustomerID' => $rebillCustomerID,
            'man:RebillInvRef' => '',
            'man:RebillInvDes' => '',
            'man:RebillCCName' => $card->name,
            'man:RebillCCNumber' => $card->card_number,
            'man:RebillCCExpMonth' => $month,
            'man:RebillCCExpYear' => $year,
            'man:RebillInitAmt' => 0,
            'man:RebillInitDate' => date('m/d/Y'),
            'man:RebillRecurAmt' => $suite->monthlySubscriptionPrice * 100,
            'man:RebillStartDate' => date('m/d/Y'),
            'man:RebillInterval' => 1,
            'man:RebillIntervalType' => 3,
            'man:RebillEndDate' => $end_date //Card Expiry Date
        );
        $soapaction = 'http://www.eway.com.au/gateway/rebill/manageRebill/CreateRebillEvent';
        $result = $client->call('man:CreateRebillEvent', $requestbody, '', $soapaction);
        if($result['Result'] == 'Fail')
        {
            echo $result['ErrorSeverity'] . ": " . $result['ErrorDetails'];
            exit;
        }else{
            $rebillCustomerID = $result['RebillCustomerID'];
            $rebillID = $result['RebillID'];
            
            //Create MSH Datas 
            $msh = array('mode' => 'PUSH', 'url' => '', 'username' => $user->user_login . "_" . $suite->id, 'password' => wp_generate_password(15, true));
            
            //Save Billing Data to Database
            $id = $wpdb->insert($wpdb->prefix . "users_purchases", array(
                'user_id' => $user->ID,
                'suite_id' => $suite->id,
                'rebill_customer_id' => $rebillCustomerID,
                'rebill_id' => $rebillID,
                'esb_username' => $msh['username'],
                'msh_p_mode' => $msh['mode'],
                'msh_url' => $msh['url'],
                'msh_username' => $msh['username'],
                'msh_password' => $msh['password'],
                'status' => 'Active',
                'expiry_date' => date("Y-m-d", strtotime($end_date)),
                'created_date' => date('Y-m-d H:i:s')
            ));
            //Make this customer a member of the group
            if(!groups_is_user_member($user->ID, $suite->community_id))
            {
                groups_join_group($suite->community_id);
            }
            
            $group = groups_get_group( array('group_id' => $suite->community_id));
            
            //Create Backend Customer Using SOAP
            /*$esb_client = new nusoap_client('http://esb.test.compliancetest.net:8280/api/integrationAPI', false);            
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
            $soapaction = 'http://esb.test.compliancetest.net:8280/api/integrationAPI/user/create';
            $result = $client->call('msg:createUserRequest', $requestbody, '', $soapaction);
            */
            
            if(!$id)
            {
                echo 'Your payment successfully sent! But there was an error while storing your purchase.';
            }else{
                echo $success;    
            }
            
        }
        exit;
    }
    
}


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
        
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . 'users_purchases WHERE user_id=%d AND id=%d', $user->ID, $pId);
        $purchase = $wpdb->get_row($query);
        if(!$purchase)
        {
            addMessage('Invalid Request!', 'error');
            wp_redirect($return);
            exit;
        }
        
        //Change status to canceled 
        $wpdb->update($wpdb->prefix . 'users_purchases', array('status' => 'Cancelled'), array('id' => $purchase->id));
        
        //Delete Customer Account on the backend using SOAP
        
    }    
}