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



