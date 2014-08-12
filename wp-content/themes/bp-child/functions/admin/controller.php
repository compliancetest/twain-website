<?php

function ct_remove_subscribers($list_id, $page)
{
    $limit = 10;
    
    $mailChimp = new Mailchimp(get_mailchimp_api_key(), array('ssl_verifypeer' => false));
    $mailChimpList = new Mailchimp_Lists($mailChimp);
    
    $subscribers = $mailChimpList->members($list_id, 'subscribed', array('start' => 0, 'limit' => $limit));  
    
    $result = array();
    
    if (empty($subscribers['data'])) {
        $result['status'] = 'completed';
    } else {
        foreach($subscribers['data'] as $srow)
        {
            $mailChimpList->unsubscribe($list_id, array('email' => $srow['email']), true);
        }
        
        if (count($subscribers['data']) < $limit) {
            $result['status'] = 'completed';
        } else {
            $result['status'] = 'continue';
            $result['page'] = $page + 1;
        }
        
        $result['total'] = ($page - 1) * $limit + count($subscribers['data']);
    }
    
    echo json_encode($result);
    exit;
}




function ct_add_users_to_mailchimp($list_id, $page)
{
    global $wpdb;
    
    $limit = 10;
    
    $mailChimp = new Mailchimp(get_mailchimp_api_key(), array('ssl_verifypeer' => false));
    $mailChimpList = new Mailchimp_Lists($mailChimp);
    
    $users = $wpdb->get_results("SELECT * FROM {$wpdb->users} WHERE user_status=0 ORDER BY ID LIMIT " . ($page - 1) * $limit . ", " . $limit);
    
    $result = array();
    
    if (empty($users)) {
        $result['status'] = 'completed';
    } else {
        foreach($users as $user) 
        {
            try{
                $result = $mailChimpList->subscribe($list_id, array('email' => $user->user_email), array('FNAME' => get_user_meta($user->ID, "first_name", true), 'LNAME' => get_user_meta($user->ID, "last_name", true)), 'html', false);
                
            }catch(Exception $e){
                
            }                   
        }
        
        if (count($users) < $limit) {
            $result['status'] = 'completed';
        } else {
            $result['status'] = 'continue';
            $result['page'] = $page + 1;
        }
        
        $result['total'] = ($page - 1) * $limit + count($users);
    }
    
    echo json_encode($result);
    exit;
}



function ct_add_members_to_mailchimp($group_id, $page)
{
    global $wpdb;
    
    $list_id = groups_get_groupmeta($group_id, 'community_mailchimp_list_id');
    
    $limit = 10;
    
    $mailChimp = new Mailchimp(get_mailchimp_api_key(), array('ssl_verifypeer' => false));
    $mailChimpList = new Mailchimp_Lists($mailChimp);
    
    $members = groups_get_group_members($group_id, $limit, $page, false);                
    
    $result = array();
    echo $list_id;
    if (empty($members['members'])) {
        $result['status'] = 'completed';
    } else {
        foreach($members['members'] as $member) 
        {
            $user = get_userdata($member->user_id);var_dump($user);
            try{
                $result = $mailChimpList->subscribe($list_id, array('email' => $user->user_email), array('FNAME' => $user->first_name, 'LNAME' => $user->last_name), 'html', false);
                
            }catch(Exception $e){
                
            }                   
        }
        
        if (count($users) < $limit) {
            $result['status'] = 'completed';
        } else {
            $result['status'] = 'continue';
            $result['page'] = $page + 1;
        }
        
        $result['total'] = ($page - 1) * $limit + count($members['members']);
    }
    
    echo json_encode($result);
    exit;
}

function ct_process_cc_payment()
{
    global $wpdb;
    
    $id = $_REQUEST['id'];
    
    $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}organisations_payments WHERE id=%d", $id);    
    $payment = $wpdb->get_row($query);
    
    $payment_method = ct_get_payment_method_by_id($payment->payment_method_id);
    
    $result = processEwayPayment($payment_method->customer_id, $payment->amount, $payment->invoice_number);
    
    header("content-type: application/xml");
    
    if($result['ewayTrxnStatus'] == 'True')
    {
        $eWayReference = $result['ewayTrxnNumber'];
        
        //Save reference id
        $wpdb->update($wpdb->prefix . "organisations_payments", 
                      array("is_paid" => 1, "date_paid" => date("Y-m-d H:i:s"), "reference" => $eWayReference),
                      array("id" =>  $payment->id),
                      array("%d", "%s", "%s"),
                      array("%d")
        );
        
        echo '<result><status>success</status></result>';
        
    } else {
        if(isset($result['ewayTrxnError']))
            $error = $result['ewayTrxnError'];
        else if(isset($result['faultstring']))
            $error = $result['faultstring'];
        
        $orgClass = new CT_Organisation($payment->organisation_id);
        $user = get_userdata($orgClass->admin_id);
        
        //Change Payment Method to inActive
        $wpdb->update($wpdb->prefix . "organisations_payment_methods", array('Status' => 'Suspended'), array('id' => $payment_method->id));
        
        //Sending Failure Message
        $emailData = array(
            '[name]'            => $user->first_name . " " . $user->last_name,
            '[email]'           => $user->user_email,
            '[paid_amount]'     => $payment->amount,
            '[method_nickname]' => $payment_method->nickname,
            '[organisation]'    => $orgClass->organisation_name,
            '[invoice_identifier]' => $payment->invoice_number
        );
        cp_send_email(array('name' => $emailData['[name]'], 'email' => $emailData['[email]']), 'payment_processing_problem', $emailData);
        cp_send_email_to_admin('payment_processing_problem_admin', $emailData);
        
        echo '<result><status>error</status><msg><![CDATA[' . $error . ']]></msg></result>';
    }
    exit;
}

