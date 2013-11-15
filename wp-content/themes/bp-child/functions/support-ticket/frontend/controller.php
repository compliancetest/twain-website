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
    
    if(!$wpdb->insert($wpdb->prefix . 'tickets', $data))
    {
        addMessage($wpdb->last_error, 'error');
        wp_redirect("/my-support-tickets");
        exit;
    }
    
    /***************** Begin Send Mail ***************************/
    //Send Email Notification to Support
    //Send Email Notification to the Customer
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
    
    $query = "SELECT t.*, ts.status AS status_title, tc.category_title, tp.priority AS priority_title FROM " . $wpdb->prefix . "tickets AS t "
           . "LEFT JOIN $wpdb->prefix" . "ticket_statuses AS ts ON ts.id=t.status_id "
           . "LEFT JOIN $wpdb->prefix" . "ticket_categories AS tc ON tc.id=t.category_id "
           . "LEFT JOIN $wpdb->prefix" . "ticket_priorities AS tp ON tp.id=t.priority_id ";
    
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
        $tQuery = "SELECT count(id) FROM $wpdb->prefix" . "tickets AS t WHERE " . implode(" AND  ", $where);
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
    
    $query = "SELECT t.*, ts.status AS status_title, tc.category_title, tp.priority AS priority_title FROM " . $wpdb->prefix . "tickets AS t "
           . "LEFT JOIN $wpdb->prefix" . "ticket_statuses AS ts ON ts.id=t.status_id "
           . "LEFT JOIN $wpdb->prefix" . "ticket_categories AS tc ON tc.id=t.category_id "
           . "LEFT JOIN $wpdb->prefix" . "ticket_priorities AS tp ON tp.id=t.priority_id ";
    
    $customer_ids[] = $user_id;
    $where[] = " t.customer_id IN (" . implode(", ", $customer_ids) . ")";
    $where[] = $wpdb->prepare(" t.id=%d ", $ticket_id);
    
    $query .= " WHERE " . implode(" AND ", $where);
    
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
    
    $query = $wpdb->prepare("SELECT * FROM $wpdb->prefix" . "ticket_messages WHERE ticket_id=%d ORDER BY created_date", $ticket_id);
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
    
    $user = get_currentuserinfo();
    
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
    
    //Accept Term
    $wpdb->update($wpdb->prefix . "tickets", array('term_accepted' => 1), array('id' => $ticketDetail->id));
    
    if($ticketDetail->customer_id != $user_id)
    {
        //Set Support ID
        $wpdb->update($wpdb->prefix . "tickets", array('support' => $user_id), array('id' => $ticketDetail->id));
    }
    
    /***************** Begin Send Mail ***************************/
    //Send Email Notification to Support
    //Send Email Notification to the Customer
    /***************** End Send Mail *****************************/
    
    addMessage("The term has been accepted.", "success");
    
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
    
    $message = "New Term: \r\n";
    $message .= "Time to Pay: " . $ttpay . "\r\n";
    $message .= "Time to Resolve: " . $ttresolve . "\r\n";
    $message .= "Time to Response: " . $ttresponse . "\r\n";
    
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
    
    if(!$wpdb->insert($wpdb->prefix .  "ticket_messages", $messageData))
    {
        addMessage($wpdb->last_error, "error");        
    }else{
        if($ticketDetail->customer_id != $user_id)
            $ticketDetail->support_id = $user_id;
        //Update Term
        $wpdb->update($wpdb->prefix . 'tickets', array('ttpay' => $ttpay, 'ttresolve' => $ttresolve, 'ttresponse' => $ttresponse, 'term_accepted' => 0, 'term_creator_id' => $user_id, 'support_id' => $ticketDetail->support_id), array('id' => $ticketDetail->id));
        
        /******* Send Term Updated Email **********/
        
        addMessage("Your ticket has been updated.", "success");
        wp_redirect("/my-support-tickets/" . $ticketDetail->id);
        exit;
    }
    
}