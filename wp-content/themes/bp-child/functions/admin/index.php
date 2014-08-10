<?php
/**
* Manage Admin Actions
*/

require_once (dirname(__FILE__) . "/view.php");
require_once (dirname(__FILE__) . "/controller.php");

//Create Tmp Menu For Admin Page
add_action('admin_menu', 'ct_add_admin_action_menu');
function ct_add_admin_action_menu()
{
    add_menu_page('Process Admin Actions', 'Process Admin Actions', 'administrator', 'admin-actions', 'ct_process_admin_actions_page');
}

function ct_admin_action_page_css()
{
    ?>
    <style type="text/css">
        #toplevel_page_admin-actions{
            display: none !important;
        }
        .current-action-progress{
            padding-left: 30px;
            background: url('/wp-admin/images/loading.gif') left top no-repeat;
        }
        .action-completed{
            background: url('/wp-admin/images/yes.png') left top no-repeat;
        }
    </style>
    <?php
}

function ct_process_admin_actions_page()
{
    $action = $_REQUEST['admin-action'];
    
    add_action("admin_head", "ct_admin_action_page_css");
    
    if (wp_verify_nonce($action, 'sync-users-to-mailchimp')) {
        ct_sync_users_to_mailchimp_page();
    } else if(wp_verify_nonce($action, 'sync-users-to-mailchimp2')) {
        ct_sync_users_to_mailchimp_page2();
    } else if(wp_verify_nonce($action, 'pay-cc-invoices')) {
        ct_process_cc_invoice_via_eway($_REQUEST['id'], $_POST['page']);
    } 
}

add_action('admin_init', 'ct_process_admin_actions');
function ct_process_admin_actions()
{
    $action = $_REQUEST['admin-action'];
    if(wp_verify_nonce($action, 'remove-current-subscribers')) {
        ct_remove_subscribers(DEFAULT_MAILCHIMP_LIST_ID, $_POST['page']);
    } else if(wp_verify_nonce($action, 'add-users-to-mailchimp')) {
        ct_add_users_to_mailchimp(DEFAULT_MAILCHIMP_LIST_ID, $_POST['page']);
    } else if(wp_verify_nonce($action, 'remove-current-subscribers2')) {
        $list_id = groups_get_groupmeta($_REQUEST['id'], 'community_mailchimp_list_id');
        ct_remove_subscribers($list_id, $_POST['page']);
    } else if(wp_verify_nonce($action, 'add-users-to-mailchimp2')) {
        ct_add_members_to_mailchimp($_REQUEST['id'], $_POST['page']);
    } 
}

add_action('admin_head', 'ct_admin_action_page_css');