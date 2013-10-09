<?php
/**
* Admin Section
*/
require_once(dirname(__FILE__) . "/controller.php");
require_once(dirname(__FILE__) . "/view.php");

require_once ABSPATH . "wp-admin/includes/class-wp-list-table.php";
require_once(dirname(__FILE__) . "/list.category.php");
require_once(dirname(__FILE__) . "/list.priority.php");
require_once(dirname(__FILE__) . "/list.status.php");

add_action("admin_menu", "ct_ticket_create_admin_menus");

add_action("admin_init", "ct_process_ticket_admin_actions");

function ct_process_ticket_admin_actions()
{
    $action = isset($_REQUEST['ct-ticket-action']) ? $_REQUEST['ct-ticket-action'] : null;
    if($action && current_user_can('manage_options'))
    {
        if(wp_verify_nonce($action, 'save-ticket-category'))
        {
            ct_save_ticket_category();
            wp_redirect(admin_url('admin.php?page=ct-tickets-categories'));
            exit;
        }else if(wp_verify_nonce($action, 'delete-ticket-category')){            
            ct_delete_ticket_category();    
            wp_redirect(admin_url('admin.php?page=ct-tickets-categories'));
            exit;
        }else if(wp_verify_nonce($action, 'save-ticket-priority')){            
            ct_save_ticket_priority();    
            wp_redirect(admin_url('admin.php?page=ct-tickets-priorities'));
            exit;
        }else if(wp_verify_nonce($action, 'delete-ticket-priority')){            
            ct_delete_ticket_priority();    
            wp_redirect(admin_url('admin.php?page=ct-tickets-priorities'));
            exit;
        }else if(wp_verify_nonce($action, 'save-ticket-status')){            
            ct_save_ticket_status();    
            wp_redirect(admin_url('admin.php?page=ct-tickets-statuses'));
            exit;
        }else if(wp_verify_nonce($action, 'delete-ticket-status')){            
            ct_delete_ticket_status();    
            wp_redirect(admin_url('admin.php?page=ct-tickets-statuses'));
            exit;
        }
    }
}