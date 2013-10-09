<?php
/**
* Ticket Frontend Section
*/

require_once(dirname(__FILE__) . "/controller.php");
require_once(dirname(__FILE__) . "/view.php");

add_action("init", "ct_process_ticket_frontend_actions");

function ct_process_ticket_frontend_actions()
{
    $action = isset($_REQUEST['ct-ticket-action']) ? $_REQUEST['ct-ticket-action'] : null;
    if(is_user_logged_in())
    {
        if(wp_verify_nonce($action, 'show-submit-form'))
        {
            showSumitTicketBox();            
            exit;
        }
    }
}