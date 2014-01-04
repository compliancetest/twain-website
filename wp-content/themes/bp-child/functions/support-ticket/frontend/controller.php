<?php
/**
* Process Support Ticket Frontend Action
*/

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
    
    $priority_id = $_POST['priority'];
    $category_id = $_POST['category'];
    
    $priorities = $ct_ticket_priority->getPriorities();
    $categories = $ct_ticket_category->getCategories();
    
    $error = array();
    if(!$subject)
        $error[] = 'Ticket subject should not be empty.';
    
    if(!$content)
        $error[] = 'Please describe about your problem.';
        
    if(!$priority_id || !($priority = $ct_ticket_priority->getPriorityById($priority_id)))
        $error[] = 'Ticket priority is not valid.';
    
    if(!$category_id || !($category = $ct_ticket_category->getCategoryById($category_id)))
        $error[] = 'Ticket type is not valid.';
    
    if(count($error) > 0)
    {
        addMessage(implode("<br />", $error), 'error');
        wp_redirect("/my-support-tickets");
        exit;
    }
    
    $data = array(
        'customer_id' => $user_id,
        'support_id' => 0,
        'title' => $subject,
        'content' => $content,
        'category_id' => $category->id,
        'priority_id' => $priority->id,
        'status_id' => TICKET_STATUS_NEW,
        'ttresolve' => $priority->ttresolve,
        'ttresponse' => $priority->ttresponse,
        'price' => $category->has_fee ? $priority->price : 0.00,
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
    
    /***************** Begin Send Mail ***************************/
    $customer = get_userdata($user_id);
    
    //Send Email Notification to Support
    sendTicketEmail('ticket_created_support', 'support', $wpdb->insert_id, null, $user_id, null);
    //Send Email Notification to the Customer
    sendTicketEmail('ticket_created', 'customer', $wpdb->insert_id, null, $user_id, null);
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
    
    $query = "SELECT t.*, ts.status AS status_title, tc.category_title, tp.priority AS priority_title FROM " . TABLE_TICKETS . " AS t "
           . "LEFT JOIN " . TABLE_TICKET_STATUSES . " AS ts ON ts.id=t.status_id "
           . "LEFT JOIN " . TABLE_TICKET_CATEGORIES . " AS tc ON tc.id=t.category_id "
           . "LEFT JOIN " . TABLE_TICKET_PRIORITIES . " AS tp ON tp.id=t.priority_id ";
    
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
    
    $query = "SELECT t.*, ts.status AS status_title, tc.category_title, tp.priority AS priority_title FROM " . TABLE_TICKETS . " AS t "
           . "LEFT JOIN " . TABLE_TICKET_STATUSES . " AS ts ON ts.id=t.status_id "
           . "LEFT JOIN " . TABLE_TICKET_CATEGORIES . " AS tc ON tc.id=t.category_id "
           . "LEFT JOIN " . TABLE_TICKET_PRIORITIES . " AS tp ON tp.id=t.priority_id ";
    
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
    if(!$ticket_id || !($ticketDetail = getTicketById($ticket_id)))
    {
        addMessage("Invalid Request!", "error");
        wp_redirect("/my-support-tickets");
        exit;
    }
    
    //Validate that the term was not created by this user
    if($user_id == $ticketDetail->term_creator_id)
    {
        wp_redirect("/my-support-tickets/" . $ticketDetail->id);
        exit;
    }
    
    $message = "<b>" . $user->display_name . "</b> has accepted the term.";
    
    //Save Message    
    $messageData = array(
        'ticket_id' => $ticketDetail->id,
        'sender' => $user_id,
        'message' => $message,
        'is_new' => 1,
        'message_type' => 'term',
        'receiver' => $ticketDetail->customer_id != $user_id ? $ticketDetail->customer_id : $ticketDetail->support_id,
        'created_date' => date("Y-m-d H:i:s")
    );
    
    if(!$wpdb->insert(TABLE_TICKET_MESSAGES, $messageData))
    {
        addMessage($wpdb->last_error, "error");        
    }else{
        $messageID = $wpdb->insert_id;
        
        //Accept Term
        $wpdb->update(TABLE_TICKETS, array('term_accepted' => 1, 'last_message_id' => $messageID, 'status_id' => TICKET_STATUS_IN_PROGRESS, 'last_updated' => date("Y-m-d H:i:s")), array('id' => $ticketDetail->id));
        $wpdb->insert(TABLE_TICKET_STATUS_HISTORY, array('ticket_id' => $ticketDetail->id, 'status_id' => TICKET_STATUS_IN_PROGRESS, 'created_date' => date("Y-m-d H:i:s")));
        
        if($ticketDetail->customer_id != $user_id)
        {
            //Set Support ID
            $wpdb->update(TABLE_TICKETS, array('support' => $user_id), array('id' => $ticketDetail->id));
        }
        
        /***************** Begin Send Mail ***************************/
        if($ticketDetail->customer_id == $user_id)
        {
            //Send To Support
            sendTicketEmail("ticket_updated_support", 'support', $ticketDetail->id, $messageID, $ticketDetail->customer_id, $ticketDetail->support_id);
        }else{
            //Send to Customer
            sendTicketEmail("ticket_updated", 'customer', $ticketDetail->id, $messageID, $ticketDetail->customer_id, $ticketDetail->support_id);
        }        
        
        //Send ticket Started Notification
        sendTicketEmail("ticket_started", 'customer', $ticketDetail->id, $messageID, $ticketDetail->customer_id, $ticketDetail->support_id);
        
        /***************** End Send Mail *****************************/
        
        addMessage("The term has been accepted.", "success");
    }
    
    wp_redirect("/my-support-tickets/" . $ticketDetail->id);
    exit;    
}

/**
* Change Ticket Term
* 
*/
function changeTicketTerm()
{
    global $wpdb;
    
    $user_id = get_current_user_id();
    
    $ticket_id = $_POST['id'];
    $ttpay = $_POST['ttpay'];
    $ttresponse = $_POST['ttresponse'];
    $ttresolve = $_POST['ttresolve'];
    $comment = stripslashes_deep($_POST['content']);
    
    if(!$ticket_id || !($ticketDetail = getTicketById($ticket_id)))
    {
        addMessage("Invalid Request!", "error");
        wp_redirect("/my-support-tickets");
        exit;        
    }
    
    $user = get_userdata($user_id);
    
    $message = "<b>" . $user->display_name . "</b> has updated the term.\r\n\r\n";
    $message .= "<b>Time to Pay:</b> " . $ttpay . "\r\n";
    $message .= "<b>Time to Resolve:</b> " . $ttresolve . "\r\n";
    $message .= "<b>Time to Response:</b> " . $ttresponse . "\r\n";
    
    if($comment)
        $message .= "\r\n" . $comment . "\r\n" ;
    
    //Save Message
    $messageData = array(
        'ticket_id' => $ticketDetail->id,
        'sender' => $user_id,
        'message' => $message,
        'is_new' => 1,
        'message_type' => 'term',
        'receiver' => $ticketDetail->customer_id != $user_id ? $ticketDetail->customer_id : $ticketDetail->support_id,
        'created_date' => date("Y-m-d H:i:s")
    );
    
    if(!$wpdb->insert(TABLE_TICKET_MESSAGES, $messageData))
    {
        addMessage($wpdb->last_error, "error");        
    }else{
        $messageID =$wpdb->insert_id;
        
        if($ticketDetail->customer_id != $user_id)
            $ticketDetail->support_id = $user_id;
        //Update Term
        $wpdb->update(TABLE_TICKETS, array('ttpay' => $ttpay, 'ttresolve' => $ttresolve, 'ttresponse' => $ttresponse, 'term_accepted' => 0, 'term_creator_id' => $user_id, 'support_id' => $ticketDetail->support_id, 'last_message_id' => $messageID, 'last_updated' => date("Y-m-d H:i:s")), array('id' => $ticketDetail->id));
        
        //Update New Message Count
        if($ticketDetail->customer_id != $user_id)
            $query = "UPDATE " . TABLE_TICKETS . " SET `customer_new_messages`=`customer_new_messages` + 1 WHERE id=" . $ticketDetail->id;
        else
            $query = "UPDATE " . TABLE_TICKETS . " SET `support_new_messages`=`support_new_messages` + 1 WHERE id=" . $ticketDetail->id;
        $wpdb->query($query);
        
        /******* Send Term Updated Email **********/
        if($ticketDetail->customer_id == $user_id)
        {
            //Send To Support
            sendTicketEmail("ticket_updated_support", 'support', $ticketDetail->id, $messageID, $ticketDetail->customer_id, $ticketDetail->support_id);
        }else{
            //Send to Customer
            sendTicketEmail("ticket_updated", 'customer', $ticketDetail->id, $messageID, $ticketDetail->customer_id, $ticketDetail->support_id);
        }
        
        addMessage("Your ticket has been updated.", "success");
        wp_redirect("/my-support-tickets/" . $ticketDetail->id);
        exit;
    }
    
}

/**
* Send Ticket Message
*/
function sendTicketMessage()
{
    global $wpdb;
    
    $user_id = get_current_user_id();
    
    $ticket_id = $_POST['id'];
    
    if(!$ticket_id || !($ticketDetail = getTicketById($ticket_id)))
    {
        addMessage("Invalid Request!", "error");
        wp_redirect("/my-support-tickets");
        exit;        
    }
    
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
    
    if(isset($_POST['status_change']))
    {
        if($_POST['status_change'] == 'in_progress' && $ticketDetail->status_id != TICKET_STATUS_IN_PROGRESS)
        {
            $messageData['message'] = '<i>Ticket status been updated to <b>In Progress</b></i>' . "\n\r\n\r" . $message;             
            $status_changed = true;
            $new_status = TICKET_STATUS_IN_PROGRESS;
        }else if($_POST['status_change'] == 'feedback' && $ticketDetail->status_id != TICKET_STATUS_FEEDBACK){
            $messageData['message']  = '<i>Ticket status been updated to <b>Feedback</b></i>' . "\n\r\n\r" . $message ; 
            $status_changed = true;
            $new_status = TICKET_STATUS_FEEDBACK;
        }else if($_POST['status_change'] == 'resolved' && $ticketDetail->status_id != TICKET_STATUS_RESOLVED){
            $messageData['message']  = '<i>Ticket status been updated to <b>Resolved</b></i>' . "\n\r\n\r" . $message ; 
            $status_changed = true;
            $new_status = TICKET_STATUS_RESOLVED;
        }else if($_POST['status_change'] == 'closed' && $ticketDetail->status_id != TICKET_STATUS_CLOSED){
            $messageData['message']  = '<i>Ticket status been updated to <b>closed</b></i>' . "\n\r\n\r" . $message ; 
            $status_changed = true;
            $new_status = TICKET_STATUS_CLOSED;
        }
    }
    
    if(isset($_POST['resolved']) && $_POST['resolved'])
    {
        $messageData['message'] = '<i>Ticket status been updated to <b>Resolved</b></i>' . "\n\r\n\r" . $message ; 
        $status_changed = true;
        $new_status = TICKET_STATUS_RESOLVED;
        break;
    }
    
    
    
    if(!$wpdb->insert(TABLE_TICKET_MESSAGES, $messageData))
    {
        addMessage($wpdb->last_error, "error");        
    }else{
        $messageID = $wpdb->insert_id;
        
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
                    if(!is_dir(TICKET_ATTACHMENTS_DIR . "/" . $ticketDetail->id))
                        mkdir(TICKET_ATTACHMENTS_DIR . "/" . $ticketDetail->id, 0777);
                    
                    $name = $_FILES['attachments']['name'][$i];
                    $k = 1;
                    while(file_exists(TICKET_ATTACHMENTS_DIR . "/" . $ticketDetail->id . "/" . $name))
                    {
                        $name = $k . "_" . $_FILES['attachments']['name'][$i];
                        $k++;
                    }
                    
                    if(move_uploaded_file($_FILES['attachments']['tmp_name'][$i], TICKET_ATTACHMENTS_DIR . "/" . $ticketDetail->id . "/" . $name))
                    {
                        //Store Data to the Table
                        $wpdb->insert(TABLE_TICKET_ATTACHMENTS, array('ticket_id' => $ticketDetail->id, 'message_id' => $messageID, 'file_name' => $name, 'created_date' => date("Y-m-d H:i:s"), 'token' => sha1($ticket_id . "_" . $messageID . "_" . rand(0, 999999) . "_" . $name . "_" . time() . "_" . rand(0, 999999))));                        
                        $has_attachment = 1;
                    }
                    
                    
                }
            }
        }
        
        if($has_attachment)
            $wpdb->update(TABLE_TICKET_MESSAGES, array('has_attachment' => 1), array('id' => $messageID));
        
        //Update New Message Count
        if($ticketDetail->customer_id != $user_id)
            $query = "UPDATE " . TABLE_TICKETS . " SET `customer_new_messages`=`customer_new_messages` + 1, `last_updated`='" . date("Y-m-d H:i:s") . "', last_message_id=" . $messageID . " WHERE id=" . $ticketDetail->id;
        else
            $query = "UPDATE " . TABLE_TICKETS . " SET `support_new_messages`=`support_new_messages` + 1, `last_updated`='" . date("Y-m-d H:i:s") . "', last_message_id=" . $messageID . " WHERE id=" . $ticketDetail->id;
            
        $wpdb->query($query);
        
        ///Send Email Notification
        if($ticketDetail->customer_id == $user_id)
        {
            //Send To Support
            sendTicketEmail("ticket_updated_support", 'support', $ticketDetail->id, $messageID, $ticketDetail->customer_id, $ticketDetail->support_id);
        }else{
            //Send to Customer
            sendTicketEmail("ticket_updated", 'customer', $ticketDetail->id, $messageID, $ticketDetail->customer_id, $ticketDetail->support_id);
        }
        
        if($status_changed)
        {
            $wpdb->query("UPDATE " . TABLE_TICKETS . " SET status_id=" . $new_status . " WHERE id=" . $ticketDetail->id);
            $wpdb->insert(TABLE_TICKET_STATUS_HISTORY, array('ticket_id' => $ticketDetail->id, 'status_id' => $new_status, 'created_date' => date("Y-m-d H:i:s")));
            
            if($new_status == TICKET_STATUS_RESOLVED)
            {
                //Send Notification of status resolved to the user 
                sendTicketEmail("ticket_solved", 'customer', $ticketDetail->id, $messageID, $ticketDetail->customer_id, $ticketDetail->support_id);
                sendTicketEmail("ticket_solved_admin", 'support', $ticketDetail->id, $messageID, $ticketDetail->customer_id, $ticketDetail->support_id);
            }else if($new_status == TICKET_STATUS_CLOSED){
                //Send Notification of status resolved to the user 
                sendTicketEmail("ticket_closed", 'customer', $ticketDetail->id, $messageID, $ticketDetail->customer_id, $ticketDetail->support_id);
                sendTicketEmail("ticket_closed_admin", 'support', $ticketDetail->id, $messageID, $ticketDetail->customer_id, $ticketDetail->support_id);
            }
        }
        
        addMessage("Your ticket has been updated.", "success");
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