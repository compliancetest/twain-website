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
        
        $limit = 50;        
        $page = 0;
        
        do{
            $subscribers = $mailChimpList->members(DEFAULT_MAILCHIMP_LIST_ID, 'subscribed', array('start' => $page, 'limit' => $limit));  
            foreach($subscribers['data'] as $srow)
            {
                $mailChimpList->unsubscribe(DEFAULT_MAILCHIMP_LIST_ID, array('email' => $srow['email']), true);
            }
        }while(count($subscribers['data']) > 0);
        
        //Delete Unsubscribed Members
        do{
            $subscribers = $mailChimpList->members(DEFAULT_MAILCHIMP_LIST_ID, 'unsubscribed');  
            foreach($subscribers['data'] as $srow)
            {
                $mailChimpList->unsubscribe(DEFAULT_MAILCHIMP_LIST_ID, array('email' => $srow['email']), true);
            }
        }while(count($subscribers['data']) > 0);
        
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
        
        if($members){
            //Getting memeber emails
            $members_list = array();
            foreach($members['members'] as $member)
            {
                $user = get_userdata($member->user_id);
                $members_list[$user->user_email] = array('first_name' => $user->first_name, 'last_name' => $user->last_name, 'email' => $user->user_email);
            }
            
            $page = 0;
            $limit = 50;
            
            $allSubscribers = array();
            
            do{
                $subscribers = $mailChimpList->members($list_id, 'subscribed', array('start' => $page, 'limit' => $limit));                  
                
                foreach($subscribers['data'] as $srow)
                {
                    $allSubscribers[] = $srow['email'];                    
                }
                $page++;
            }while(count($subscribers['data']) > 0);
            
            foreach($allSubscribers as $s_email)
            {
                if(!isset($members_list[$s_email]))   
                {
                    //Unsubscribe user
                    try{
                        
                        $mailChimpList->unsubscribe($list_id, array('email' => $s_email), true);
                    }catch(Exception $e){
                        
                    }
                }
            }
                        
            foreach($members_list as $member)
            {
                try{
                    echo $member['email'] ."<br />";
                    $result = $mailChimpList->subscribe($list_id, array('email' => $member['email']), array('FNAME' => $member['first_name'], 'LNAME' => $member['last_name']), 'html', false);
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
    }
}

