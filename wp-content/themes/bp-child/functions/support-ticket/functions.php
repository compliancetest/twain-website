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
function ct_can_manage_customer($customer_id, $support_id = null)
{
    global $wpdb;
    
    if(!$support_id)
        $support_id = get_current_user_id();
    
    $customers = getManagedCustomerWPIDs($support_id);
    
    return in_array($support_id, $customers);
    
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
function ct_send_ticket_email($email_id, $email_type, $ticket_id, $message_id = null, $customer_id = null, $support_id = null)
{
    global $wpdb;
    
    $emailData = array();
    
    if($ticket_id)
    {
        $ticketDetail = getTicketById($ticket_id);
        $emailData['[ticket_id]'] = $ticketDetail->id;
        $emailData['[ticket_title]'] = $ticketDetail->title;
        $emailData['[ticket_content]'] = apply_filters('the_content', $ticketDetail->content);
        $emailData['[ticket_url]'] = get_site_url(null, "/my-support-tickets/" . $ticketDetail->id, "https");
        $emailData['[ticket_type]'] = $ticketDetail->category_title;
        $emailData['[ticket_priority]'] = $ticketDetail->priority_title;
        $emailData['[ticket_price]'] = doubleval($ticketDetail->price) > 0 ? '$' .$ticketDetail->price . '/hr' : 'Free';
        $emailData['[ticket_ttpay]'] = $ticketDetail->ttpay;
        $emailData['[ticket_ttresolve]'] = $ticketDetail->ttresolve;
        $emailData['[ticket_ttresponse]'] = $ticketDetail->ttresponse;
        $emailData['[ticket_created]'] = formatDate($ticketDetail->created_date, "F d, Y h:i A", $email_type == 'customer' ? $customer_id : $support_id);
        $emailData['[ticket_updated]'] = formatDate($ticketDetail->last_date, "F d, Y h:i A", $email_type == 'customer' ? $customer_id : $support_id);
    }
    
    if($customer_id)
    {
        $customerDetail = get_userdata($customer_id);
        $emailData['[customer_name]'] = cp_get_user_display_name($customerDetail);
        $emailData['[customer_email]'] = $customerDetail->user_email;
    }
    
    if($support_id)
    {
        $supportDetail = get_userdata($support_id);
        $emailData['[support_name]'] = cp_get_user_display_name($supportDetail);
        $emailData['[support_email]'] = $supportDetail->user_email;
    }
    
    if($message_id)
    {
        $messageDetail = getTicketMessageById($message_id);
        $emailData['[message_content]'] = apply_filters("the_content", $messageDetail->message);
        if($messageDetail->has_attachment)
        {
             $emailData['[message_content]'] .= "<br />";
             $attachments = getAttachmentsByMessageId($message->id);
             foreach($attachments as $file){
                $emailData['[message_content]'] .= '<a href="' . get_site_url(null, null, 'https') .'/?action=' . wp_create_nonce('download-ticket-attachment') .'&file=' . $file->token . '">' . $file->file_name . '</a><br />';
             }
        }
    }
    
    if($email_type == 'customer')
    {
        cp_send_email(array('name' => cp_get_user_display_name($customerDetail), 'email' => $customerDetail->user_email), $email_id, $emailData);
    }else{
        if(!$support_id)
        {
            //Getting Customer Communities
            $query = $wpdb->prepare("SELECT pm.meta_value FROM {$wpdb->prefix}users_purchases AS p " .
                     "LEFT JOIN {$wpdb->postmeta} AS pm ON p.suite_id=pm.post_id AND pm.meta_key='community_id' ".
                     "WHERE p.user_id=%d AND p.status='Active' AND p.customer_id > 0", $customer_id);
            
            $community_ids = $wpdb->get_col($query);
            cp_send_email_to_support($community_ids, $email_id, $emailData);
        }else{
            cp_send_email(array('name' => cp_get_user_display_name($supportDetail), 'email' => $supportDetail->user_email), $email_id, $emailData);
        }
        
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