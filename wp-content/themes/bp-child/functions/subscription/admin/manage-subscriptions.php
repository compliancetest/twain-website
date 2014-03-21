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
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    if($action == 'view')
    {
        ct_manage_subscriptions_show_user_detail();
    }else{
        ct_manage_subscriptions_show_users_list();
    }
}

function ct_manage_subscriptions_show_users_list()
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

function ct_manage_subscriptions_show_user_detail()
{
    global $wpdb;
    
    $uid = $_GET['id'];
    if(!$uid)
    {
        ?>
        <div class="wrap">
            <h2>User Detail Payment Information</h2>
            <div class="message error-message">Invalid Request!</div>
        </div>
        <?php
        return;
    }
    
    $userData = get_userdata($uid);
    
    //Getting User Payment Methods    
    $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}users_cards WHERE user_id=%d", $uid);
    $cards = $wpdb->get_results($query);
    
    //Getting User Subscriptions
    $query = $wpdb->prepare("SELECT s.*, p.user_id AS purchaser_id, p.expiry_date, ts.family_mark, p.monthly_fee FROM {$wpdb->prefix}users_subscriptions AS s 
                            LEFT JOIN {$wpdb->prefix}users_purchases AS p ON s.purchase_id=p.id
                            LEFT JOIN {$wpdb->prefix}test_suites AS ts ON ts.suite_id=s.suite_id
                            WHERE s.user_id=%d ORDER BY ts.family_mark, ts.version_major", $uid);
    $subscriptions = $wpdb->get_results($query);
    
    ?>
    <div class="wrap">
        <h2>User Detail Payment Information</h2>
        <br />
        <h3>Payment Methods</h3>
        <table class="widefat">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Card Number</th>
                    <th>Nickname</th>
                    <th>Email</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($cards as $i => $row): ?>
                <tr <?php echo $i % 2 == 0 ? 'class="alt"' : '' ?>>
                    <td><?php echo $row->id?></td>
                    <td><?php echo $row->card_number?></td>
                    <td><?php echo $row->nickname?></td>
                    <td><?php echo $row->email?></td>
                    <td><?php echo $row->status?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <br />
        <h3>Subscriptions</h3>
        <table class="widefat">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Test Suite</th>
                    <th>Current Payment Amount</th>
                    <th>Next Due Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($subscriptions as $i => $row): ?>
                <tr <?php echo $i % 2 == 0 ? 'class="alt"' : '' ?>>
                    <td><?php echo $row->id?></td>
                    <td><?php echo $row->suite_id?></td>
                    <td><?php echo $row->monthly_fee?></td>
                    <td><?php echo $row->expiry_date?></td>
                    <td><?php echo $row->status?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
    
}