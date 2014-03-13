<?php
/**
* Manage Subscriptions
*/

require_once(THE_FUNCTION . '/subscription/admin/users-subscriptions-list-table.php');

add_action('admin_menu', 'ct_add_manage_subscriptions_menu');

function ct_add_manage_subscriptions_menu()
{
    add_menu_page("Payments", "Payments", "manage_options", "manage-payments", "ct_manage_subscriptions_users_list");
    add_submenu_page("manage-payments", "Users", "Users", "manage_options", "users", "ct_manage_subscriptions_users_list");
    add_submenu_page("manage-payments", "Processing", "Processing", "manage_options", "processing", "ct_manage_subscriptions_users_list");    
    
    wp_enqueue_style("manage-payments", get_stylesheet_directory_uri() . "/functions/subscription/admin/manage-payments.css");
}

function ct_manage_subscriptions_users_list()
{
    $listTable = new CT_Users_Purchases_List_Table();
    $listTable->prepare_items();
    ?>
    <div class="wrap">
        <h2>Users</h2>
        <form name="adminform" action="users.php?page=user_fee_overrides" method="post">
        <?php
            echo $listTable->display();
        ?>
        </form>
    </div>
    <?php   
}