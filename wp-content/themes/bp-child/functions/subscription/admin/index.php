<?php
require_once(THE_FUNCTION . '/subscription/admin/organisations-subscriptions-list-table.php');
require_once(THE_FUNCTION . '/subscription/admin/users-subscriptions-list-table.php');
require_once(THE_FUNCTION . '/subscription/admin/users-payments-logs-list-table.php');

require_once(dirname(__FILE__) . "/controller.php");
require_once(dirname(__FILE__) . "/view.php");

add_action('admin_menu', 'ct_add_manage_subscriptions_menu');

function ct_add_manage_subscriptions_menu()
{
    add_menu_page("Organisation Subscriptions", "Organisation Subscriptions", "manage_options", "organisation-subscriptions", "ct_manage_organisation_subscriptions_list");
    add_submenu_page("organisation-subscriptions", "All Subscriptions", "All Organisations", "manage_options", "organisation-subscriptions", "ct_manage_organisation_subscriptions_list");
    add_submenu_page("organisation-subscriptions", "Organisation Subscription", "Add Subscription", "manage_options", "add-organisation-subscription", "ct_add_organisation_subscription");
//    add_submenu_page("manage-subscriptions", "Users Subscriptions", "Users", "manage_options", "users-subscriptions", "ct_manage_users_subscriptions_list");
    
    wp_enqueue_style("manage-payments", get_stylesheet_directory_uri() . "/functions/subscription/admin/manage-payments.css");
}

add_action('admin_init', 'ct_process_subscriptions_admin_actions');
function ct_process_subscriptions_admin_actions()
{
    if(!is_super_admin())
        return;
        
    if (isset($_REQUEST['subscription_admin_action'])) {
        $action = $_REQUEST['subscription_admin_action'];
        
        if (wp_verify_nonce($action, 'save-subscription')) {
            ct_save_subscription_on_admin();    
        }
    }
}