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
        }else if(wp_verify_nonce($action, 'validate-ticket')){
            $errors = getCreateTicketErrors();
            
            header('Content-type: application/xml');
            echo "<result>";
            if(!$errors)
            {
                echo "<status>success</status>";
            }else{
                echo "<status>error</status>";
                echo "<error><![CDATA[" . implode("<br />", $errors) . "]]></error>";
            }
            echo "</result>";
            exit;
        }else if(wp_verify_nonce($action, 'submit-ticket')){
            createSupportTicket();
        }else if(wp_verify_nonce($action, 'accept-term')){
            acceptTerm();
        }else if(wp_verify_nonce($action, 'change-ticket-term')){
            changeTicketTerm();
        }else if(wp_verify_nonce($action, 'send-ticket-message')){
            sendTicketMessage();
        }else if(wp_verify_nonce($action, 'download-attachment')){
            downloadAttachment();
        }
    }
}