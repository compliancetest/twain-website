<?php
/**
* Support Ticket Functions
*/

function ct_ticket_unique_category_slug($slug, $id)
{
    global $wpdb;
    
    $new_slug = $slug;
    
    $check_sql = "SELECT id FROM " . TABLE_TICKET_CATEGORIES . " WHERE category_name=%s AND id != %d";
    $query = $wpdb->prepare($check_sql, $new_slug, $id);
    $pid = $wpdb->get_var($query);    
    $idx = 2;
    while($pid)
    {
        $new_slug = $slug . "-" . $idx;
        $query = $wpdb->prepare($check_sql, $new_slug, $id);
        $pid = $wpdb->get_var($query);    
        $idx++;
    }
    
    return $new_slug;
}

/**
* Check Can Manage Customer
* 
* @param Int $customer_id
* @param Int $support_id
*/
function ct_is_support($ticket_id, $support_id = null)
{
    global $wpdb;
    
    if(!$support_id)
        $support_id = get_current_user_id();
    
    if(!$support_id)
        return false;
    
    //Getting Ticket Details
    $ticketDetail = getTicketById($ticket_id);
    
    if(!$ticketDetail)
        return false;
    
    $community_id = get_post_meta($ticketDetail->suite_id, 'community_id', true);
    
    return groups_is_user_mod($support_id, $community_id);
    
}

function makeTicketRead($ticket_id, $type)
{
    global $wpdb;
    
    if($type == 'customer')
    {
        $query = $wpdb->prepare("UPDATE " . $wpdb->prefix . "tickets SET customer_new_messages=0 WHERE id=%d", $ticket_id);
    }else if($type == 'support'){
        $query = $wpdb->prepare("UPDATE " . $wpdb->prefix . "tickets SET support_new_messages=0 WHERE id=%d", $ticket_id);
    }
    
    $wpdb->query($query);
    
}



/**
* Send Ticket Email
* 
* @param Int $email_id: 
* @param String $email_type: 'customer' or 'support'
* @param Int $ticket_id
* @param Int $message_id
* @param Int $customer_id
* @param Int $support_id
*/
function ct_send_ticket_email($email_id, $email_type, $ticketDetail, $message_id = null)
{
    global $wpdb;
    
    $emailData = array();
    
    $emailData['[token_price]'] = get_option('token_price');
    
    $emailData['[ticket_id]'] = $ticketDetail->id;
    $emailData['[ticket_title]'] = $ticketDetail->title;
    $emailData['[ticket_url]'] = get_site_url(null, "/my-support-tickets/" . $ticketDetail->id, "https");    
    $emailData['[ticket_type]'] = $ticketDetail->category_title;
    $emailData['[ticket_priority]'] = $ticketDetail->priority_title;
    $emailData['[ticket_content]'] = apply_filters('the_content', $ticketDetail->content);    
    
    $emailData['[time_to_pay]'] = $ticketDetail->ttpay;        
    $emailData['[time_to_response]'] = $ticketDetail->ttresolve;
    $emailData['[time_to_resolve]'] = $ticketDetail->ttresponse;
    
    $emailData['[hourly_price]'] = intval($ticketDetail->price) > 0 ? $ticketDetail->price . ' Tokens/hr' : 'Free';
    $emailData['[ticket_total_price]'] = intval($ticketDetail->total_price) > 0 ? $ticketDetail->total_price . ' Tokens' : 'Free';    
    
    if(isset($ticketDetail->paid_amount))
        $emailData['[paid_amount]'] = $ticketDetail->paid_amount;
    
    if(isset($ticketDetail->paid_tokens))
        $emailData['[paid_tokens]'] = $ticketDetail->paid_tokens;
    
    if(isset($ticketDetail->purchased_tokens))
        $emailData['[purchased_tokens]'] = $ticketDetail->purchased_tokens;
        
    if(isset($ticketDetail->remained_tokens))
        $emailData['[remained_tokens]'] = $ticketDetail->remained_tokens;
    
    
    $emailData['[ticket_created]'] = formatDate($ticketDetail->created_date, "F d, Y h:i A", $email_type == 'customer' ? $customer_id : $support_id);
    $emailData['[ticket_updated]'] = formatDate($ticketDetail->last_date, "F d, Y h:i A", $email_type == 'customer' ? $customer_id : $support_id);
    
    if($ticketDetail->customer_id)
    {
        $customerDetail = get_userdata($customer_id);         
        $emailData['[customer]'] = $emailData['[customer_name]'] = cp_get_user_display_name($customerDetail);
        $emailData['[customer_email]'] = $customerDetail->user_email;
    }
    
    if($ticketDetail->support_id)
    {
        $supportDetail = get_userdata($support_id);
        $emailData['[support_name]'] = cp_get_user_display_name($supportDetail);
        $emailData['[support_email]'] = $supportDetail->user_email;
    }
    
    if($message_id)
    {
        $messageDetail = getTicketMessageById($message_id);
        $emailData['[message]'] = apply_filters("the_content", $messageDetail->message);
        if($messageDetail->has_attachment)
        {
             $emailData['[message]'] .= "<br />";
             $attachments = getAttachmentsByMessageId($message->id);
             foreach($attachments as $file){
                $emailData['[message]'] .= '<a href="' . get_site_url(null, null, 'https') .'/?action=' . wp_create_nonce('download-ticket-attachment') .'&file=' . $file->token . '">' . $file->file_name . '</a><br />';
             }
        }
    }
    
    if($email_type == 'customer')
    {
        cp_send_email(array('name' => cp_get_user_display_name($customerDetail), 'email' => $customerDetail->user_email), $email_id, $emailData);
    }else if($email_type == 'support'){
        if(!$ticketDetail->support_id)
        {
            $community_id = get_post_meta($ticketDetail->suite_id, 'community_id', true);
            cp_send_email_to_support($community_id, $email_id, $emailData);            
        }else{
            cp_send_email(array('name' => cp_get_user_display_name($supportDetail), 'email' => $supportDetail->user_email), $email_id, $emailData);
        }
        
    }else if($email_type == 'admin'){
        $community_id = get_post_meta($ticketDetail->suite_id, 'community_id', true);
        cp_send_email_to_community_admin($community_id, $email_id, $emailData);        
    }
    
}

/**
* Check if the user is paid subscriber
* 
* @param Int $user_id
* 
* @return Boolean 
*/
function ct_can_create_support_ticket($user_id = null)
{
    global $wpdb;
    
    if(!$user_id)
        $user_id = get_current_user_id();        
    
    //Check if the user has purchasement or not
    $query = $wpdb->prepare("SELECT count(*) FROM {$wpdb->prefix}users_purchases WHERE user_id=%d", $user_id);
    $count = $wpdb->get_var($query);
    
    return $count > 0 ? true : false;
}

function ct_get_prepurchased_tokens($user_id = null)
{
    global $wpdb;
    
    if(!$user_id)
        $user_id = get_current_user_id();        
    
    //Check if the user has purchasement or not
    $query = $wpdb->prepare("SELECT purchased_tokens FROM {$wpdb->prefix}users_extra WHERE userID=%d", $user_id);    
    $tokens = $wpdb->get_var($query);
    
    return !$tokens ? 0 : $tokens;
}


function ct_send_ticket_message($ticket_id, $sender, $receiver, $message, $message_type = 'message')
{
    global $wpdb;
    
    //Save Message
    $messageData = array(
        'ticket_id' => $ticket_id,
        'sender' => $sender,
        'receiver' => $receiver,
        'message' => $message,
        'is_new' => 1,
        'message_type' => $message_type,        
        'created_date' => date("Y-m-d H:i:s")
    );
    
    if(!$wpdb->insert(TABLE_TICKET_MESSAGES, $messageData))
    {
        addMessage($wpdb->last_error, "error");        
        
        return false;
    }else{
        $messageID =$wpdb->insert_id;
        
        $ticketDetail = getTicketById($ticket_id);
        
        if($ticketDetail->customer_id == $receiver)
            $query = "UPDATE " . TABLE_TICKETS . " SET `customer_new_messages`=`customer_new_messages` + 1, `last_message_id` = " . $messageID . ", `last_updated` = '" . date("Y-m-d H:i:s") . "' WHERE id=" . $ticket_id;
        else
            $query = "UPDATE " . TABLE_TICKETS . " SET `support_new_messages`=`support_new_messages` + 1, `last_message_id` = " . $messageID . ", `last_updated` = '" . date("Y-m-d H:i:s") . "' WHERE id=" . $ticket_id;
            
        $wpdb->query($query);
        
        return $messageID;
    }
}


function ct_update_ticket_status($ticket_id, $new_status, $comment = '')
{
    global $wpdb;
    
    $wpdb->insert(TABLE_TICKET_STATUS_HISTORY, array('ticket_id' => $ticket_id, 'status_id' => $new_status, 'created_date' => date("Y-m-d H:i:s"), 'comment'=> $comment));
    $wpdb->update(TABLE_TICKETS, array('status_id' => $new_status, 'last_updated' => date("Y-m-d H:i:s")), array('id' => $ticket_id));    
}
