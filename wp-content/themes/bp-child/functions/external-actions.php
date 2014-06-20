<?php
/**
* Including external actions  
*/

add_action('init', 'process_external_actions');
function process_external_actions()
{
    global $wpdb;
    
    if(!is_admin() && !is_super_admin())
    {
        return;
    }
    $action = isset($_GET['ext-action']) ? $_GET['ext-action'] : null;
    if($action == 'add-users-to-mailchimp') //Add Users to All Subscribe List
    {
        $mailChimp = new Mailchimp(get_mailchimp_api_key(), array('ssl_verifypeer' => false));
        $mailChimpList = new Mailchimp_Lists($mailChimp);
        
        do{
            $subscribers = $mailChimpList->members(DEFAULT_MAILCHIMP_LIST_ID);  
            foreach($subscribers['data'] as $srow)
            {
                $mailChimpList->unsubscribe(DEFAULT_MAILCHIMP_LIST_ID, array('email' => $srow['email']), true);
            }
        }while($subscribers['total'] > count($subscribers['data']));
        
        //Delete Unsubscribed Members
        do{
            $subscribers = $mailChimpList->members(DEFAULT_MAILCHIMP_LIST_ID, 'unsubscribed');  
            foreach($subscribers['data'] as $srow)
            {
                $mailChimpList->unsubscribe(DEFAULT_MAILCHIMP_LIST_ID, array('email' => $srow['email']), true);
            }
        }while($subscribers['total'] > count($subscribers['data']));
        
        echo "<b>Users</b><br />";
        
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
        
        echo "<br /><br />Process finished, please close this window.";
        
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
        echo "<br /><br />Process finished, please close this window.";
        exit;
    }else if($action == 'add-users-to-mailchimp3'){ //Add the community members to the selected List
        $mailChimp = new Mailchimp(get_mailchimp_api_key(), array('ssl_verifypeer' => false));
        $mailChimpList = new Mailchimp_Lists($mailChimp);
        
        $community_id = $_GET['id'];
        
        $list_id = groups_get_groupmeta($community_id, 'community_mailchimp_list_id');
        
        echo "<b>Members</b><br />";
        
        //Delete Unsubscribed Members
        /*echo "Removing Unsubscribed Members";
        do{
            $subscribers = $mailChimpList->members($list_id, 'unsubscribed');  
            foreach($subscribers['data'] as $srow)
            {
                $mailChimpList->unsubscribe($list_id, array('email' => $srow['email']), true);
            }
        }while($subscribers['total'] > count($subscribers['data']));
        */
        //Getting Memebers
        $members = groups_get_group_members($community_id, false, false, false);        
        var_dump($members);
        if($members){
            
            do{
                $subscribers = $mailChimpList->members($list_id);  
                var_dump($subscribers);exit;
                foreach($subscribers['data'] as $srow)
                {
                    $isExists = false;
                    foreach($members['members'] as $member)
                    {
                        if($member->user_email == $srow['email'])
                        {
                            $isExists = true;
                            break;
                        }
                    }
                    if(!$isExists)
                        $mailChimpList->unsubscribe($list_id, array('email' => $srow['email']), true);
                }
            }while($subscribers['total'] > count($subscribers['data']));
            
            
            foreach($members['members'] as $member)
            {
                $user = get_userdata($member->user_id);                          
                try{
                    echo $user->user_email ."<br />";
                    $result = $mailChimpList->subscribe($list_id, array('email' => $user->user_email), array('FNAME' => $user->first_name, 'LNAME' => $user->last_name), 'html', false);        
                }catch(Exception $e){
                    
                }
            }
            
        }else{
            //Remove All Subscribers
            do{
                $subscribers = $mailChimpList->members($list_id);  
                foreach($subscribers['data'] as $srow)
                {
                    $isExists = false;
                    foreach($members['members'] as $member)
                    {
                        if($member->user_email == $srow['email'])
                        {
                            $isExists = true;
                            break;
                        }
                    }
                    if(!$isExists)
                        $mailChimpList->unsubscribe($list_id, array('email' => $srow['email']), true);
                }
            }while($subscribers['total'] > count($subscribers['data']));
            
        }
        
        echo "<br /><br />Process finished, please close this window.";
        exit;
    }else if($action == 'recurring-payment'){
        require_once(THE_FUNCTION . '/soap/nusoap.php');
        
        $webserviceURL = get_eway_token_webservice_url();
        $customerID = get_eway_customer_id();
        $userName = get_eway_user_name();
        $userPWD = get_eway_user_pwd();
        
        //Getting All Expired Payments
        $query = "SELECT * FROM " . $wpdb->prefix . "users_purchases WHERE `status`='Active' AND `customer_id` > 0 AND `expiry_date` <= '" . date('Y-m-d') . "'";
        $rows = $wpdb->get_results($query);
        
        
        foreach($rows as $row)
        {           
            $user = get_userdata($row->user_id);
            
            $suite_name = get_post_meta($row->suite_id, 'ts_name', true);
            //Send Payment
            $current_price = get_post_meta($row->suite_id, 'monthly_subscription_price', true);
            $price = $current_price < $row->monthly_fee ? $current_price : $row->monthly_fee;
            
        
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
                'man:invoiceDescription' => 'Recurring Monthly Bill for ' . $suite_name
            );
            $soapaction = 'https://www.eway.com.au/gateway/managedpayment/ProcessPayment';
            $result = $client->call('man:ProcessPayment', $requestbody, '', $soapaction);
            
            if(!$result || $result['ewayTrxnStatus'] == 'False')
            {
                //Make the subscription expired
                $payment_error = $result['ewayTrxnError'];
                //Send Email
                $emailData = array(
                    '[name]' => cp_get_user_fullname($row->user_id),
                    '[email]' => $user->user_email,
                    '[suite_name]' => $suite_name,
                    '[suite_url]' => get_permalink($row->suite_id),
                    '[paid_amount]' => $price
                );
                cp_send_email(array('name' => $emailData['[name]'], 'email' => $emailData['[email]']), 'expire_subscription', $emailData);
                cp_send_email_to_admin('expire_subscription_admin', $emailData);
            }else{            
                //Expand the expiry
                $wpdb->update($wpdb->prefix . "users_purchases", array('expiry_date' => date("Y-m-d", strtotime('+1 month')), 'Status' => 'Active'), array('id' => $row->id));
            }
        }
        
        exit;
    }
}

