<?php
/**
* Process Support Ticket Frontend Action
*/

/**
* Validate the user inputs to create ticket
* 
* 
*/
function getCreateTicketErrors()
{
    global $ct_ticket_status, $ct_ticket_priority, $ct_ticket_category, $wpdb;
    
    //Remove Spaces from start and end
    $subject = stripslashes_deep(trim($_POST['subject']));
    
    //Remove Spaces from start and end, strip html tags
    $content = stripslashes_deep(strip_tags(trim($_POST['question'])));
    
    $suite_id = $_POST['suite_id'];
    $card_id = $wpdb->get_var( $wpdb->prepare("SELECT id FROM {$wpdb->prefix}organisations_payment_methods WHERE organisation_id IN ( SELECT organisation_id FROM {$wpdb->prefix}organisations_members WHERE user_id = %d) AND is_default = 1", get_current_user_id() ) );
    $priority_id = $_POST['priority'];
    $category_id = $_POST['category'];
    
    $error = array();
    if(!$suite_id)
        $error[] = 'Please select a test suite.';
    
    if(!$subject)
        $error[] = 'Ticket subject should not be empty.';
    
    if(!$content)
        $error[] = 'Please describe about your problem.';
        
    if(!$priority_id || !($priority = $ct_ticket_priority->getPriorityById($priority_id)))
        $error[] = 'Ticket priority is not valid.';
                   
    if(!$category_id || !($category = $ct_ticket_category->getCategoryById($category_id)))
        $error[] = 'Ticket type is not valid.';    
        
    if($priority && $category && $category->has_fee == 1 && $priority->price > 0)
    {
        if(!$card_id){
            $error[] = 'At present, your organisation has not defined a payment method to be used for support tickets. Please talk to your administrator ( '.$wpdb->get_var($wpdb->prepare("SELECT contact_email FROM {$wpdb->prefix}organisations WHERE id = ( SELECT organisation_id FROM {$wpdb->prefix}organisations_members WHERE user_id = %d) ", get_current_user_id() ) ).' ).';
        }
        else if(!($card = getUserCardById($card_id)))
            $error[] = 'Please select a valid payment method';
    }

    return $error;    
}

/***
* Create Support Tickets
* 
*/
function createSupportTicket()
{
    global $ct_ticket_status, $ct_ticket_priority, $ct_ticket_category, $wpdb;
    
    if(!is_user_logged_in())
    {
        addMessage("Invalid Request!", 'error');
        wp_redirect(get_site_url());
        exit;
    }
    
    $user_id = get_current_user_id();
    
    //Remove Spaces from start and end
    $subject = stripslashes_deep(trim($_POST['subject']));
    
    //Remove Spaces from start and end, strip html tags
    $content = stripslashes_deep(strip_tags(trim($_POST['question'])));
    
    $suite_id = $_POST['suite_id'];
    $priority_id = $_POST['priority'];
    $category_id = $_POST['category'];
    
    $error = getCreateTicketErrors();
    
    $priority = $ct_ticket_priority->getPriorityById($priority_id);
    $category = $ct_ticket_category->getCategoryById($category_id);
    
    if(count($error) > 0)
    {
        addMessage(implode("<br />", $error), 'error');
        wp_redirect("/my-support-tickets");
        exit;
    }
    $card_id = $wpdb->get_var( $wpdb->prepare("SELECT id FROM {$wpdb->prefix}organisations_payment_methods WHERE organisation_id IN ( SELECT organisation_id FROM {$wpdb->prefix}organisations_members WHERE user_id = %d) AND is_default = 1", get_current_user_id() ) );
    if( ! $card_id ){
        $card_id = $wpdb->get_var( $wpdb->prepare("SELECT id FROM {$wpdb->prefix}organisations_payment_methods WHERE organisation_id IN ( SELECT organisation_id FROM {$wpdb->prefix}users_subscriptions WHERE user_id = %d) AND is_default = 1", get_current_user_id() ) );
    }
    if( ! $card_id )
    {
        $card_id = $wpdb->get_var( $wpdb->prepare("SELECT id FROM {$wpdb->prefix}organisations_payment_methods WHERE organisation_id IN ( SELECT organisation_id FROM {$wpdb->prefix}organisations_members WHERE user_id = %d) AND is_default = 0", get_current_user_id() ) );
        if( ! $card_id ){
            $card_id = $wpdb->get_var( $wpdb->prepare("SELECT id FROM {$wpdb->prefix}organisations_payment_methods WHERE organisation_id IN ( SELECT organisation_id FROM {$wpdb->prefix}users_subscriptions WHERE user_id = %d) AND is_default = 0", get_current_user_id() ) );
        }
    }
    $community_id = get_post_meta($suite_id, 'community_id', true);
    $data = array(
        'customer_id' => $user_id,
        'support_id' => 0,
        'community_id' => $community_id,
        'card_id' => !$card_id ? 0 : $card_id,
        'title' => $subject,
        'content' => $content,
        'category_id' => $category->id,
        'priority_id' => $priority->id,
        'status_id' => TICKET_STATUS_NEW,
        'ttresolve' => $priority->ttresolve,
        'ttresponse' => $priority->ttresponse,        
        'price' => $category->has_fee ? get_ticket_price( $priority->id ) : 0,
        'total_price' => $category->has_fee ? get_ticket_price($priority->id) : 0,
        'term_accepted' => 0,
        'term_creator_id' => $user_id,
        'customer_new_messages' => 0,
        'support_new_messages' => 1,
        'last_message_id' => 0,
        'created_date' => date('Y-m-d H:i:s'),
        'last_updated' => date('Y-m-d H:i:s'),
        'solved_date' => '0000-00-00 00:00:00',
    );
    if(!$wpdb->insert(TABLE_TICKETS, $data))
    {
        addMessage($wpdb->last_error, 'error');
        wp_redirect("/my-support-tickets");
        exit;
    }
    $tID = $wpdb->insert_id;
    //Upload Files
    if(!is_dir(TICKET_ATTACHMENTS_DIR))
    {
        mkdir(TICKET_ATTACHMENTS_DIR, 0777);
        //Add .htaccess to prevent direct access
        $fp = fopen(TICKET_ATTACHMENTS_DIR . "/.htaccess", "w");
        fwrite($fp, "deny from all");
        fclose($fp);
    }
    $has_attachment = 0;
    if($_FILES['attachments']['error'])
    {
        foreach($_FILES['attachments']['error'] as $i => $error)
        {
            if($error == UPLOAD_ERR_OK)
            {
//                if(!is_dir(TICKET_ATTACHMENTS_DIR . "/" . $tID))
//                    mkdir(TICKET_ATTACHMENTS_DIR . "/" . $tID, 0777);

                $name = $_FILES['attachments']['name'][$i];
                $k = 1;
//                while(file_exists(TICKET_ATTACHMENTS_DIR . "/" . $tID . "/" . $name))
//                {
//                    $name = $k . "_" . $_FILES['attachments']['name'][$i];
//                    $k++;
//                }

//                if(move_uploaded_file($_FILES['attachments']['tmp_name'][$i], TICKET_ATTACHMENTS_DIR . "/" . $tID . "/" . $name))
//                {
//                    //Store Data to the Table
//
//                }
                $token = sha1( $tID . "_" . rand(0, 999999) . "_" . $name . "_" . time() . "_" . rand( 0, 999999 ) );
                $wpdb->insert(TABLE_TICKET_ATTACHMENTS, array('ticket_id' => $tID, 'file_name' => $name, 'created_date' => date("Y-m-d H:i:s"), 'token' => $token ) );
                $has_attachment = 1;
                $s3 = new S3Wrapper();
                $s3->putObject('/attachments/tickets/' . $token . '/'. $name, file_get_contents( $_FILES['attachments']['tmp_name'][$i] ), 'application/'.end( explode( '.', $name ) ));

            }
        }
    }

    if($has_attachment)
        $wpdb->update(TABLE_TICKETS, array('has_attachment' => 1), array('id' => $tID));
    
    /***************** Begin Send Mail ***************************/
    $ticketDetail = getTicketById( $tID );
    //Send Email Notification to the Customer
    ct_send_ticket_email('ticket_created', 'customer', $ticketDetail);    
    //Send Email Notification to Support
    ct_send_ticket_email('ticket_created_support', 'support', $ticketDetail);
    //Send Email Notification to Admin
    ct_send_ticket_email('ticket_created_admin', 'admin', $ticketDetail);    
    /***************** End Send Mail *****************************/
    
    addMessage('Your ticket has been submitted successfully.');
    wp_redirect("/my-support-tickets");
    exit;
}

/**
* Get User Tickets
* 
*/
function getUserTickets($category_id = null, $status_id = null, $priority_id = null, $page = 1, $limit = -1, $orderBy = null, $order = 'desc')
{
    global $wpdb;
    
    $user_id = get_current_user_id();
        
    if(!$user_id)
        return array();
        
    $where = array();
    
    $customer_ids = getManagedCustomerWPIDs($user_id);
    
    $query = "SELECT t.*, ts.status AS status_title, tc.category_title, tp.priority AS priority_title, u.display_name AS customer_name, um.meta_value as organisation, o.organisation_name AS organisation1 FROM " . TABLE_TICKETS . " AS t "
           . "LEFT JOIN " . TABLE_TICKET_STATUSES . " AS ts ON ts.id=t.status_id "
           . "LEFT JOIN " . TABLE_TICKET_CATEGORIES . " AS tc ON tc.id=t.category_id "
           . "LEFT JOIN " . TABLE_TICKET_PRIORITIES . " AS tp ON tp.id=t.priority_id "
           . "LEFT JOIN " . $wpdb->prefix . "organisations_members AS m ON m.user_id=t.customer_id "
           . "LEFT JOIN " . $wpdb->prefix . "organisations AS o ON o.id=m.organisation_id "
           . "LEFT JOIN " . $wpdb->users . " AS u ON t.customer_id=u.ID "
           . "LEFT JOIN " . $wpdb->usermeta . " AS um ON t.customer_id=um.user_id AND um.meta_key='user_organisation' ";
    
    $customer_ids[] = $user_id;
    $where[] = " t.customer_id IN (" . implode(", ", $customer_ids) . ")";
    
    if($category_id !== null && $category_id != "")
    {        
        $where[] = $wpdb->prepare(" t.category_id=%d", $category_id);    
    }
    
    if($status_id !== null && $status_id != "")
    {        
        $where[] = $wpdb->prepare(" t.status_id=%d", $status_id);    
    }
    
    if($priority_id !== null && $priority_id != "")
    {        
        $where[] = $wpdb->prepare(" t.priority_id=%d", $priority_id);    
    }
    
    $orderQuery = "";
    switch($orderBy)
    {
        case "id":
        case "title":
        case "created_date":
        case "category_id":
        case "status_id":
        case "priority_id":
        case "solved_date":
        case "last_updated":
        case "customer_name":
        case "organisation":
            $orderQuery = " ORDER BY $orderBy $order";
            break;
        default:
            $orderQuery = " ORDER BY last_updated DESC";
            break;
    }
    
    $query .= " WHERE " . implode(" AND  ", $where) . $orderQuery;
    
    if($limit > 0)
    {
        $tQuery = "SELECT count(id) FROM " . TABLE_TICKETS . " AS t WHERE " . implode(" AND  ", $where);
        $totalItems = $wpdb->get_var($tQuery);        
        $query .= " LIMIT " . ($page - 1) * $limit . ", $limit";
    }
    $rows = $wpdb->get_results($query);
    
    if(!isset($totalItems))
        $totalItems = count($rows);
    
    return array('total' => $totalItems, 'data' => $rows);
    
}

/**
* Getting Ticket Details By Id
* 
* @param mixed $ticket
*/
function getTicketById($ticket_id)
{
    global $wpdb;
        
    $user_id = get_current_user_id();
        
    if(!$user_id)
        return array();
        
    $where = array();
    
    $customer_ids = getManagedCustomerWPIDs($user_id);
    
    $query = "SELECT t.*, ts.status AS status_title, tc.category_title, tp.priority AS priority_title, xi.unit_price FROM " . TABLE_TICKETS . " AS t "
           . "LEFT JOIN " . TABLE_TICKET_STATUSES . " AS ts ON ts.id=t.status_id "
           . "LEFT JOIN " . TABLE_TICKET_CATEGORIES . " AS tc ON tc.id=t.category_id "
           . "LEFT JOIN " . TABLE_TICKET_PRIORITIES . " AS tp ON tp.id=t.priority_id "
           . "LEFT JOIN " . $wpdb->prefix . "xeroitems AS xi ON xi.code=tp.item_code ";
    
    $customer_ids[] = $user_id;
    $where[] = " t.customer_id IN (" . implode(", ", $customer_ids) . ")";
    $where[] = $wpdb->prepare(" t.id=%d ", $ticket_id);
    
    $query .= " WHERE " . implode(" AND ", $where);
    
    $row = $wpdb->get_row($query);
    
    return $row;
}

/**
* Get Message By DI
* 
* @param Int $message_id
*/
function getTicketMessageById($message_id)
{
    global $wpdb;
    
    $query  = $wpdb->prepare("SELECT * FROM " . TABLE_TICKET_MESSAGES . " WHERE id=%d", $message_id);
    $row = $wpdb->get_row($query);
    
    return $row;
}

/**
* Get Ticket Messages Using Ticket ID
* 
* @param Int $ticket_id
* @return []
*/
function getTicketMessagesByTicketId($ticket_id)
{
    global $wpdb;
    
    $user = get_current_user_id();
    
    $query = $wpdb->prepare("SELECT *  FROM " . TABLE_TICKET_MESSAGES . " WHERE ticket_id=%d ORDER BY created_date", $ticket_id);
    
    $rows = $wpdb->get_results($query);
    
    return $rows;
}

/**
 * This function used to get tickets attachments ( without ticket's messages attachments)
 * @param $ticketID - integer
 * @return mixed
 */
function getAttachmentsByTicketId( $ticketID )
{
    global $wpdb;
    $rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . TABLE_TICKET_ATTACHMENTS . " WHERE ticket_id = %d AND message_id IS NULL", $ticketID ) );
    return $rows;
}

function getAttachmentsByMessageId($message_id)
{
    global $wpdb;

    $query  = $wpdb->prepare("SELECT * FROM " . TABLE_TICKET_ATTACHMENTS . " WHERE message_id=%d", $message_id);
    $rows = $wpdb->get_results($query);

    return $rows;
}


/**
* Accept Terms
*/
function acceptTerm()
{
    global $wpdb;    
    
    $ticket_id = $_REQUEST['id'];
    
    $user_id = get_current_user_id();
    
    $user = get_userdata($user_id);
    
    //Validate the ticket id
    if(!($ticket = getTicketById($ticket_id)))
    {
        addMessage('Invalid Request!', 'error');
        wp_redirect("/my-support-tickets");
    }
    
    if(!($is_support = ct_is_support($ticket_id)) && $ticket->customer_id != $user_id)
    {
        addMessage('Invalid Request!', 'error');
        wp_redirect("/my-support-tickets");
        exit;
    }
    
    //Validate that the term was not created by this user
    if($user_id == $ticket->term_creator_id)
    {
        wp_redirect("/my-support-tickets/" . $ticket->id);
        exit;
    }
    
    if($is_support)    
        $message = "<b>" . $user->display_name . "</b> has accepted the term.";
    else
        $message = "<b>[customer]</b> has accepted the term.";
    
    $messageID = ct_send_ticket_message($ticket->id, $user_id, $ticket->customer_id != $user_id ? $ticket->customer_id : $ticket->support_id, $message, 'term');
    
    if($messageID)
    {
        ct_update_ticket_status($ticket->id, TICKET_STATUS_IN_PROGRESS, $user->display_name . ' Accepted term.');
        //Accept Term
        $wpdb->update(TABLE_TICKETS, array('term_accepted' => 1, 'support_id' => $ticket->customer_id == $user_id ? $ticket->support_id : $user_id), array('id' => $ticket->id));
        
        $ticket = getTicketById($ticket->id);
        
        /***************** Begin Send Mail ***************************/
        
        if($is_support)
        {
            //Send to Customer
            ct_send_ticket_email("ticket_updated",'customer', $ticket, $messageID);
        }else{
            //Send To Support
            ct_send_ticket_email("ticket_updated_support", 'support', $ticket, $messageID);
        }        
        
        ct_send_ticket_email("ticket_updated_admin", 'admin', $ticket, $messageID);
        
        //Send ticket Started Notification
        ct_send_ticket_email("ticket_started", 'customer', $ticket, $messageID);
        ct_send_ticket_email("ticket_started_support", 'support', $ticket, $messageID);
        ct_send_ticket_email("ticket_started_admin", 'admin', $ticket, $messageID);        
        
        /***************** End Send Mail *****************************/        
        addMessage("The term has been accepted.", "success");
    }
    
    wp_redirect("/my-support-tickets/" . $ticket->id);
    exit;    
}

/**
* Change Ticket Term
* 
*/
function changeTicketTerm()
{
    global $wpdb, $ct_ticket_category, $ct_ticket_priority;
    
    $user_id = get_current_user_id();
    
    $ticket_id = $_POST['id'];
    
    $ticket = getTicketById($ticket_id);
    
    if(!$ticket)
    {
        addMessage('Invalid Request!', 'error');
        wp_redirect("/my-support-tickets");
    }
    
    if(!($is_support = ct_is_support($ticket_id)) && $ticket->customer_id != $user_id)
    {
        addMessage('Invalid Request!', 'error');
        wp_redirect("/my-support-tickets");
        exit;
    }
    
    $userData = get_userdata($user_id);
    
    $comment = trim(stripslashes_deep($_POST['content']));
    
    $message = '';
    
    $category = $ct_ticket_category->getCategoryById($ticket->category_id);
    
    $newPriority = $ct_ticket_priority->getPriorityById($_POST['priority']);
    $oldPriority = $ct_ticket_priority->getPriorityById($ticket->priority_id);

    if (is_super_admin() || ct_is_support($ticket_id)) {
        $newCategory = $ct_ticket_category->getCategoryById($_POST['category']);
        $oldCategory = $ct_ticket_category->getCategoryById($ticket->category_id);
    }
    
    $is_changed = false;
    //================================== Customer ==================================
    if($ticket->customer_id == $user_id)
    {
        //Customer can change only priority
        if($_POST['priority'] != $ticket->priority_id)
        {
            $message = "<p>" . "Term has been updated by " . ($is_support ? "<b>{$userData->first_name} {$userData->last_name}</b>" : "[customer]") . "</p>";
            $message .= "<blockquote>";
            $message .= "Status: <i>" . $oldPriority->priority . "</i> -&gt; <i>" . $newPriority->priority . "</i> <br />";
            $message .= "Time to pay: " . $ticket->ttpay . " hours <br />";
            $message .= "Time to response:  " . $newPriority->ttresponse . " hours <br />";
            $message .= "Time to resolve:  " . $newPriority->ttresolve . " hours <br />";
            $message .= "</blockquote>";
            if($comment)
                $message .= "<p>" . $comment . "</p>" ;
            $message_type = 'term';
            
            $is_changed = true;
            $ttpay = $ticket->ttpay;
            $price = $category->has_fee ? get_ticket_price( $newPriority->id ) : 0;
            $ttresponse = $newPriority->ttresponse;
            $ttresolve = $newPriority->ttresolve;
            
        }else if($comment){
            $message =  $comment;
            $message_type = 'message';
        }else{
            addMessage('Nothing was changed.', 'notice');
            wp_redirect('/my-support-tickets/' . $ticket->id);
            exit;
        }
        
        $messageID = ct_send_ticket_message($ticket->id, $user_id, $ticket->customer_id == $user_id ? $ticket->support_id : $ticket->customer_id, $message, $message_type);
        if(!$messageID)
        {
            wp_redirect('/my-support-tickets/' . $ticket->id);
            exit;
        }
        
    }else{
    //================================== Support ==================================
        //Update Support ID
        $ticket->support_id = $user_id;
        
        if($_POST['priority'] != $ticket->priority_id)
        {
            $message .=  "Status: <i>" . $oldPriority->priority . "</i> -&gt; <i>" . $newPriority->priority . "</i>\r\n";
            $is_changed = true;
        }
        if($newCategory->id != $ticket->category_id)
        {
            $message .=  "Type: <i>" . $oldCategory->category_title . "</i> -&gt; <i>" . $newCategory->category_title . "</i>\r\n";
            $is_changed = true;
        }
        
        
        $ttpay = $_POST['ttpay'];
        $ttresponse = $_POST['ttresponse'];
        $ttresolve = $_POST['ttresolve'];    
        $price = $newCategory->has_fee ? get_ticket_price( $newPriority->id ) : 0;
        
        if($ticket->ttpay != $ttpay || $ticket->ttresponse != $ttresponse || $ticket->ttresolve != $ttresolve)
        {            
            $is_changed = true;
        }
        if($is_changed)
        {
            $message .= "Time to pay: " . $ttpay . " hours\r\n";
            $message .= "Time to response:  " . $ttresponse . " hours\r\n";
            $message .= "Time to resolve:  " . $ttresolve . " hours\r\n";
        }
        
        if($is_changed){
            $message = "<p>" . "Term has been updated by " . ($is_support ? "<b>{$userData->first_name} {$userData->last_name}</b>" : "[customer]") . "</p>" . 
                        "<blockquote>" . $message . "</blockquote>";
            if($comment)
                $message .= "<p>" . $comment . "</p>";
                
            $message_type = 'term';
        }else if($comment){
            $message = "<p>" . $comment . "<p>";
            $message_type = 'message';
        }else{
            addMessage('Nothing was changed.', 'notice');
            wp_redirect('/my-support-tickets/' . $ticket->id);
            exit;
        }
        $messageID = ct_send_ticket_message($ticket->id, $user_id, $ticket->customer_id == $user_id ? $ticket->support_id : $ticket->customer_id, $message, $message_type);
        if(!$messageID)
        {
            wp_redirect('/my-support-tickets/' . $ticket->id);
            exit;
        }
    }
    if($is_changed) //Term has been updated
    {
        //Update ticket status to feedback if it already has started
        if($ticket->status_id != TICKET_STATUS_NEW && $ticket->status_id != TICKET_STATUS_FEEDBACK)
        {
            ct_update_ticket_status($ticket->id, TICKET_STATUS_FEEDBACK, 'Ticket term has been changed.');            
            $ticket->status_id = TICKET_STATUS_FEEDBACK;
        }
        $wpdb->update(TABLE_TICKETS, array('ttpay' => $ttpay, 'ttresolve' => $ttresolve, 'ttresponse' => $ttresponse, 'total_price' => $ttpay * $price, 'price' => $price, 'term_accepted' => 0, 'priority_id' => $_POST['priority'], 'category_id' => isset($newCategory) ? $newCategory->id : $ticket->category_id, 'term_creator_id' => $user_id, 'support_id' => $ticket->support_id, 'last_updated' => date("Y-m-d H:i:s"), 'status_id' => $ticket->status_id), array('id' => $ticket->id));
    }
    
    $ticket = getTicketById($ticket->id);
    
    //Send Email
    ct_send_ticket_email("ticket_updated", $ticket->customer_id == $user_id ? 'support' : 'customer', $ticket, $messageID);
    ct_send_ticket_email("ticket_updated_admin", 'admin', $ticket, $messageID);
    
    
    addMessage("Your ticket has been updated.", "success");
    wp_redirect("/my-support-tickets/" . $ticket->id);
    exit;
}



/**
* Send Ticket Message
*/
function sendTicketMessage()
{
    global $wpdb;
    
    $user_id = get_current_user_id();
    
    $has_error = false;
    
    $ticket_id = $_POST['id'];
    
    $ticketDetail = getTicketById($ticket_id);

    if(!$ticketDetail)
    {
        addMessage('Invalid Request!', 'error');
        wp_redirect("/my-support-tickets");
    }
    
    if(!($is_support = ct_is_support($ticket_id)) && $ticketDetail->customer_id != $user_id)
    {
        addMessage('Invalid Request!', 'error');
        wp_redirect("/my-support-tickets");
        exit;
    }
    
    $userData = get_userdata($user_id);
    
    $message = stripslashes_deep($_POST['content']);
    
    //Save Message
    $messageData = array(
        'ticket_id' => $ticketDetail->id,
        'sender' => $user_id,
        'message' => $message,
        'is_new' => 1,
        'message_type' => 'message',
        'has_attachment' => 0,
        'receiver' => $ticketDetail->customer_id != $user_id ? $ticketDetail->customer_id : $ticketDetail->support_id,
        'created_date' => date("Y-m-d H:i:s")
    );
    
    $status_changed = false;
    $new_status = null;
    
    if($ticketDetail->customer_id == $user_id) //Customer
    {        
        if(isset($_POST['resolved']) && $_POST['resolved'])
        {
            $messageData['message'] = '<i>Ticket status been updated to <b>Resolved</b> by ' . ($is_support ? "<b>{$userData->first_name} {$userData->last_name}</b>" : "[customer]") . '</i>' . "<br /><br />" . $message ;
            $status_changed = true;
            $new_status = TICKET_STATUS_RESOLVED;
            
            //Update Ticket Status
            ct_update_ticket_status($ticketDetail->id, $new_status);
        }
    }else{ //Support
        if(isset($_POST['status_change']))
        {
            if($_POST['status_change'] == 'in_progress' && $ticketDetail->status_id != TICKET_STATUS_IN_PROGRESS)
            {
                $message = '<i>Ticket status been updated to <b>In Progress</b> by ' . ($is_support ? "<b>{$userData->first_name} {$userData->last_name}</b>" : "[customer]") . '</i>' . "<br /><br />" . $message;             
                $status_changed = true;
                $new_status = TICKET_STATUS_IN_PROGRESS;
            }else if($_POST['status_change'] == 'feedback' && $ticketDetail->status_id != TICKET_STATUS_FEEDBACK){
                $message  = '<i>Ticket status been updated to <b>Feedback</b> by ' . ($is_support ? "<b>{$userData->first_name} {$userData->last_name}</b>" : "[customer]") . '</i>' . "<br /><br />" . $message ; 
                $status_changed = true;
                $new_status = TICKET_STATUS_FEEDBACK;
            }else if($_POST['status_change'] == 'resolved' && $ticketDetail->status_id != TICKET_STATUS_RESOLVED){
                $message  = '<i>Ticket status been updated to <b>Resolved</b> by ' . ($is_support ? "<b>{$userData->first_name} {$userData->last_name}</b>" : "[customer]") . '</i>' . "<br /><br />" . $message ; 
                $status_changed = true;
                $new_status = TICKET_STATUS_RESOLVED;
            }else if($_POST['status_change'] == 'closed' && $ticketDetail->status_id != TICKET_STATUS_CLOSED){
                $message  = '<i>Ticket status been updated to <b>closed</b> by ' . ($is_support ? "<b>{$userData->first_name} {$userData->last_name}</b>" : "[customer]") . '</i>' . "<br /><br />" . $message ; 
                $status_changed = true;
                $new_status = TICKET_STATUS_CLOSED;
            }
            
            //Update Ticket Status
            if($status_changed)
                ct_update_ticket_status($ticketDetail->id, $new_status);
        }    
            
    }
    
    $messageID = ct_send_ticket_message($ticketDetail->id, $user_id, $ticketDetail->customer_id != $user_id ? $ticketDetail->customer_id : $ticketDetail->support_id, $message, 'message');
    
    if(!$messageID)
    {
        addMessage($wpdb->last_error, "error");        
    }else{
        
        //Upload Files
        if(!is_dir(TICKET_ATTACHMENTS_DIR))
        {
            mkdir(TICKET_ATTACHMENTS_DIR, 0777);
            //Add .htaccess to prevent direct access
            $fp = fopen(TICKET_ATTACHMENTS_DIR . "/.htaccess", "w");
            fwrite($fp, "deny from all");
            fclose($fp);
        }
        $has_attachment = 0;
        if($_FILES['attachments']['error'])
        {
            foreach($_FILES['attachments']['error'] as $i => $error)
            {
                if($error == UPLOAD_ERR_OK)
                {
//                    if(!is_dir(TICKET_ATTACHMENTS_DIR . "/" . $ticketDetail->id))
//                        mkdir(TICKET_ATTACHMENTS_DIR . "/" . $ticketDetail->id, 0777);
                    
                    $name = $_FILES['attachments']['name'][$i];
                    $k = 1;
//                    while(file_exists(TICKET_ATTACHMENTS_DIR . "/" . $ticketDetail->id . "/" . $name))
//                    {
//                        $name = $k . "_" . $_FILES['attachments']['name'][$i];
//                        $k++;
//                    }
                    
//                    if(move_uploaded_file($_FILES['attachments']['tmp_name'][$i], TICKET_ATTACHMENTS_DIR . "/" . $ticketDetail->id . "/" . $name))
//                    {
                        //Store Data to the Table
                        $token = sha1( $ticketDetail->id . "_" . rand(0, 999999) . "_" . $name . "_" . time() . "_" . rand( 0, 999999 ) );

                        $wpdb->insert(TABLE_TICKET_ATTACHMENTS, array('ticket_id' => $ticketDetail->id, 'message_id' => $messageID, 'file_name' => $name, 'created_date' => date("Y-m-d H:i:s"), 'token' => $token ) );
                        $has_attachment = 1;
                        $s3 = new S3Wrapper();
                        $s3->putObject('/attachments/tickets/' . $token . '/'. $name, file_get_contents( $_FILES['attachments']['tmp_name'][$i] ), 'application/'.end( explode( '.', $name ) ));

//                    }
                    
                    
                }
            }
        }
        
        if($has_attachment)
            $wpdb->update(TABLE_TICKET_MESSAGES, array('has_attachment' => 1), array('id' => $messageID));
        
        //Reload Ticket
        $ticketDetail = getTicketById($ticketDetail->id);
        
        if(!$status_changed || ($new_status != TICKET_STATUS_RESOLVED && $new_status != TICKET_STATUS_CLOSED))
        {
            //There are separate email templates for Resolved and Closed Tickets
            ///Send Email Notification
            if($is_support)
            {
                //Send to Customer
                ct_send_ticket_email("ticket_updated", 'customer', $ticketDetail, $messageID);            
            }else{
                //Send To Support
                ct_send_ticket_email("ticket_updated_support", 'support', $ticketDetail, $messageID);
            }
            ct_send_ticket_email("ticket_updated_admin", 'admin', $ticketDetail, $messageID);
            //Add Success Message
            addMessage("Ticket has been updated.", "success");
        }else if($status_changed){
            if($new_status == TICKET_STATUS_RESOLVED)
            {                
                //Send Notification of status resolved to the user 
                ct_send_ticket_email("ticket_solved", 'customer', $ticketDetail, $messageID);
                ct_send_ticket_email("ticket_solved_support", 'support', $ticketDetail, $messageID);
                ct_send_ticket_email("ticket_solved_admin", 'admin', $ticketDetail, $messageID);
                //Add Success Message
                addMessage("Ticket has been updated.", "success");
            }else if($new_status == TICKET_STATUS_CLOSED){
                //Send Ticket Closed notification to the user 
                ct_send_ticket_email("ticket_closed", 'customer', $ticketDetail, $messageID);
                ct_send_ticket_email("ticket_closed_support", 'support', $ticketDetail, $messageID);
                ct_send_ticket_email("ticket_closed_admin", 'admin', $ticketDetail, $messageID);

                addMessage('Ticket has been closed successfully.', 'success');
                
                updateTicketHours($ticketDetail->id);                
            }       
        }
        
        wp_redirect("/my-support-tickets/" . $ticketDetail->id);
        exit;
    }
}

function downloadAttachment()
{
    global $wpdb;
    
    $token = $_GET['file'];
    
    if(!$token)
    {
        addMessage("Invalid Request!", 'error');
        wp_redirect("/");
        exit;
    }
    
    //Getting File from The Token
    $query = $wpdb->prepare("SELECT * FROM " . TABLE_TICKET_ATTACHMENTS . " WHERE token=%s", $token);
    $file = $wpdb->get_row($query);
    if(!$file)
    {
        addMessage("Invalid Request!", 'error');
        wp_redirect("/");
        exit;
    }
    
    //Check Permission
    $ticketDetail = getTicketById($file->ticket_id);
    
    if(!$ticketDetail)
    {
        addMessage("Invalid Request!", 'error');
        wp_redirect("/");
        exit;
    }
    
    if(!file_exists(TICKET_ATTACHMENTS_DIR . "/" . $file->ticket_id . "/" . $file->file_name))
    {
        addMessage("File not found!", 'error');
        wp_redirect("/");
        exit;
    }
    
    header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
    header("Last-Modified: " . gmdate("D,d M Y H:i:s") . " GMT");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("Content-Type: Application/octet-stream");
    header("Content-disposition: attachment; filename=" . $file->file_name);
    
    $fp = fopen(TICKET_ATTACHMENTS_DIR . "/" . $file->ticket_id . "/" . $file->file_name, "r");
    while (!feof($fp))
    {
        echo fread($fp, 65536); 
        flush();
    }  
    fclose($fp); 
    
    exit;
    
}


function updateTicketHours($ticket_id)
{
    global $wpdb, $ct_ticket_category, $ct_ticket_priority;
    
    $ticketDetail = getTicketById($ticket_id);
    
    $priority = $ct_ticket_priority->getPriorityById($ticketDetail->priority_id);

    $category = $ct_ticket_category->getCategoryById($ticketDetail->category_id);

    $price = $category->has_fee ? get_ticket_price( $priority->id ) : 0;

    if( $price ){
        $totalTime = $ticketDetail->ttpay;
        $pendingTime = $ticketDetail->pending_amount / $price;

        $totalFieldID = 'total_ticket_hours_' . strtolower($priority->priority);
        $pendingFieldID = 'pending_ticket_hours_' . strtolower($priority->priority);

        $query = "UPDATE {$wpdb->prefix}users_extra SET `" . $totalFieldID . "` = `" . $totalFieldID . "` + " . $totalTime . ", `" . $pendingFieldID . "` = `" . $pendingFieldID . "` + " . $pendingTime . " WHERE userID=" . $ticketDetail->customer_id;

        $wpdb->query($query);
    }
}