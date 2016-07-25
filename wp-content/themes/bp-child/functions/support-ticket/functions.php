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
    
    $community_id = $ticketDetail->community_id;
    
    return doesUserCommunitySupport( $support_id, $community_id );
    
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
    $emailData['[ticket_status]'] = $ticketDetail->status_title;        
    $emailData['[ticket_content]'] = apply_filters('the_content', $ticketDetail->content);    
    
    $emailData['[time_to_pay]'] = $ticketDetail->ttpay;        
    $emailData['[time_to_response]'] = $ticketDetail->ttresponse;
    $emailData['[time_to_resolve]'] = $ticketDetail->ttresolve;
    
    $emailData['[hourly_price]'] = intval($ticketDetail->price) > 0 ? $ticketDetail->price . ' $/hr' : 'Free';
    $emailData['[ticket_total_price]'] = intval($ticketDetail->total_price) > 0 ? $ticketDetail->total_price . ' $' : 'Free';
    
    if(isset($ticketDetail->paid_amount))
        $emailData['[paid_amount]'] = $ticketDetail->paid_amount;
    
/*    if(isset($ticketDetail->paid_tokens))
        $emailData['[paid_tokens]'] = $ticketDetail->paid_tokens;
    
    if(isset($ticketDetail->purchased_tokens))
        $emailData['[purchased_tokens]'] = $ticketDetail->purchased_tokens;
        
    if(isset($ticketDetail->remained_tokens))
        $emailData['[remained_tokens]'] = $ticketDetail->remained_tokens;*/
    
    
    $emailData['[ticket_created]'] = formatDate($ticketDetail->created_date, "F d, Y h:i A", $email_type == 'customer' ? $ticketDetail->customer_id : $ticketDetail->support_id);
    $emailData['[ticket_updated]'] = formatDate($ticketDetail->last_date, "F d, Y h:i A", $email_type == 'customer' ? $ticketDetail->customer_id : $ticketDetail->support_id);
    
    if($ticketDetail->customer_id)
    {
        $customerDetail = get_userdata($ticketDetail->customer_id);         
        $emailData['[customer]'] = $emailData['[customer_name]'] = $customerDetail->first_name . " " . $customerDetail->last_name;
        $emailData['[customer_email]'] = $customerDetail->user_email;
    }
    
    if($ticketDetail->support_id)
    {
        $supportDetail = get_userdata($ticketDetail->support_id);
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
             $attachments = getAttachmentsByMessageId($message_id);
             foreach($attachments as $file){
                $emailData['[message]'] .= '<a href="' . S3Wrapper::getAttachmentLink( $file->token, $file->file_name, 'tickets' ) . '">' . $file->file_name . '</a><br />';
             }
        }
    }
    
    //
    $emailData['[message]'] = str_replace('[customer]', $emailData['[customer]'], $emailData['[message]']);
    
    if($email_type == 'customer')
    {
        cp_send_email(array('name' => cp_get_user_display_name($customerDetail), 'email' => $customerDetail->user_email), $email_id, $emailData);
    }else if($email_type == 'support'){
        if(!$ticketDetail->support_id)
        {
            $community_id = $ticketDetail->community_id;
            cp_send_email_to_support($community_id, $email_id, $emailData);
        }else{
            cp_send_email(array('name' => cp_get_user_display_name($supportDetail), 'email' => $supportDetail->user_email), $email_id, $emailData);
        }
        
    }else if($email_type == 'admin'){
        $community_id = $ticketDetail->community_id;
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
    $query = $wpdb->prepare("SELECT count(*) FROM {$wpdb->prefix}users_subscriptions WHERE user_id=%d", $user_id);
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
    if( $new_status == TICKET_STATUS_RESOLVED || $new_status == TICKET_STATUS_CLOSED ){
        $ticket_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}tickets WHERE id = %d",$ticket_id ) );
        if( $ticket_data ){
            if( $ticket_data->total_price != 0 ){
                if( ! $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}organisations_charge WHERE reference_type = 'ticket' AND reference_id = %d", $ticket_id) ) ){
                    $data = array(
                        'organisation_id' => $wpdb->get_var($wpdb->prepare("SELECT organisation_id FROM {$wpdb->prefix}organisations_payment_methods WHERE id = %d", $ticket_data->card_id ) ),
                        'payment_id'      => $ticket_data->card_id,
                        'item_code'       => $wpdb->get_var($wpdb->prepare("SELECT code FROM {$wpdb->prefix}xeroitems WHERE unit_price = %d", $ticket_data->price ) ),
                        'quantity'        => $ticket_data->ttpay.'.00',
                        'reference_type'  => 'ticket',
                        'reference_id'    => $ticket_id,
                        'comment'         => 'Ticket #'.$ticket_id.' - '.$ticket_data->title
                    );
                    $chargeClass = new CT_Charge();
                    $chargeClass->bind($data);
                    $chargeClass->save();
                }
            }
        }
    }
    $wpdb->insert(TABLE_TICKET_STATUS_HISTORY, array('ticket_id' => $ticket_id, 'status_id' => $new_status, 'created_date' => date("Y-m-d H:i:s"), 'comment'=> $comment));
    $wpdb->update(TABLE_TICKETS, array('status_id' => $new_status, 'last_updated' => date("Y-m-d H:i:s")), array('id' => $ticket_id));    
}

function get_ticket_price( $priority_id ){
    global $wpdb;
    return $wpdb->get_var($wpdb->prepare("SELECT unit_price FROM {$wpdb->prefix}xeroitems WHERE code = (SELECT item_code FROM {$wpdb->prefix}ticket_priorities WHERE id = %d)", $priority_id ) );
}