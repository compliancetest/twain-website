<?php
/**
* Including external actions  
*/

add_action('init', 'process_external_actions');
function process_external_actions()
{
    global $wpdb;
    
    $action = isset($_GET['ext-action']) ? $_GET['ext-action'] : null;
    if($action == 'add-users-to-mailchimp') //Add Users to All Subscribe List
    {
        $mailChimp = new Mailchimp(get_mailchimp_api_key(), array('ssl_verifypeer' => false));
        $mailChimpList = new Mailchimp_Lists($mailChimp);
        
        $query = "SELECT * FROM $wpdb->users WHERE user_status=0";
        $rows = $wpdb->get_results($query);
        foreach($rows as $user) 
        {
            try{
                echo $user->user_email ."<br />";
                $result = $mailChimpList->subscribe(DEFAULT_MAILCHIMP_LIST_ID, array('email' => $user->user_email), array('FNAME' => get_user_meta($user->ID, "first_name", true), 'LNAME' => get_user_meta($user->ID, "last_name", true)), 'html', false);
            }catch(Exception $e){
                
            }                   
        }
        
        exit;
    }else if($action == 'add-users-to-mailchimp2'){ //Add Members to Subscription List
        $mailChimp = new Mailchimp(get_mailchimp_api_key(), array('ssl_verifypeer' => false));
        $mailChimpList = new Mailchimp_Lists($mailChimp);
        
        $groups = groups_get_groups();
        foreach($groups['groups'] as $group)
        {
            $list_id = groups_get_groupmeta($group->id, 'community_mailchimp_list_id');
            echo "<b>" . $group->name . "</b><br />";
            //Getting Memebers
            $members = groups_get_group_members($group->id);
            if($members){
                foreach($members['members'] as $member)
                {
                    $user = get_userdata($member->user_id);                          
                    try{
                        echo $user->user_email ."<br />";
                        $result = $mailChimpList->subscribe($list_id, array('email' => $user->user_email), array('FNAME' => $user->first_name, 'LNAME' => $user->last_name), 'html', false);        
                    }catch(Exception $e){
                        
                    }
                }
            }
        }
        
        exit;
    }else if($action == 'recurring-payment'){
        //Getting All Expired Payments
        $query = "SELECT * FROM " . $wpdb->prefix . "users_purchases WHERE `status`='Active' AND `customer_id` > 0 AND `expiry_date` <= '" . date('Y-m-d') . "'";
        $rows = $wpdb->get_results($query);
        
        foreach($rows as $row)
        {
            //Send Payment
            $current_price = get_post_meta($row->suite_id, 'monthly_subscription_price', true);
            $price = $current_price < $row->price ? $current_price : $row->price;
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
                'man:managedCustomerID' => $row->customer_id,
                'man:amount' => $price * 100,
//                'man:cvn' => $card->cvn,
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
                
            }
        }
    }
}

