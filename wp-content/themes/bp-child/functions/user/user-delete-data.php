<?php
/***
* Remove All Data Related with the users section
*/

//Remove User Products
add_filter('delete_user', 'ct_delete_user_data');
function ct_delete_user_data($user_id)
{
    global $wpdb;
    
    //Delete User Products
    $args = array(
        'post_type' => 'product-service', 
        'posts_per_page' => -1,
        'author' => $user_id
    );
        
    $rows = get_posts($args);
    
    foreach($rows as $p)
    {
        wp_delete_post($p->ID);
    }
    
    //Delete Payment Logs
    $wpdb->delete($wpdb->prefix . "users_transactions", array('user_id' => $user_id));
    //Delete Subscriptions
    $wpdb->query("DELETE s FROM {$wpdb->prefix}users_subscriptions AS s LEFT JOIN {$wpdb->prefix}users_purchases AS p ON p.id=s.purchase_id WHERE p.user_id=$user_id");
    //Delete Purchases
    $wpdb->delete($wpdb->prefix . "users_purchases", array('user_id' => $user_id));
    //Delete Payment Methods
    $wpdb->delete($wpdb->prefix . "users_cards", array('user_id' => $user_id));
    //Delete Test Plans
    $wpdb->delete($wpdb->prefix . "test_plans", array('creator_id' => $user_id));
    //Delete Compliance Claims
    $wpdb->delete($wpdb->prefix . "compliance_claims", array('creator_id' => $user_id));
    //Delete Profile Instances
    $wpdb->delete($wpdb->prefix . "community_profile_instances", array('creator_id' => $user_id, 'type' => 'tester'));
    //Delete Transaction Logs
    $esb = new ManageESB();
        
    //Getting ESB IDs
    $query = "SELECT id FROM " . $wpdb->prefix . "users_subscriptions WHERE user_id = $user_id";                
    $esbIds = $wpdb->get_col($query);
    
    if($esbIds)
    {
        $query = "DELETE FROM " . $esb->table_conversation_metadata . " WHERE ID in (" . implode(", ", $esbIds) . ")";            
        ManageESB::$esbdb->query($query);
    }
    
    return $user_id;
}

//Add Clear Transaction Logs Action To User Action Log
add_filter('user_row_actions', 'ct_add_clear_transaction_log_action', 10, 2);
function ct_add_clear_transaction_log_action($actions, $user_object) {
    $actions['clear_transaction'] = "<a class='clear_transaction' href='" . admin_url( "users.php?action=clear-transactions&amp;users=$user_object->ID") . "'>" . __( 'Clear Transactions' ) . "</a>";
    return $actions;
}

add_filter('restrict_manage_users','ct_add_clear_transaction_log_to_bulk_actions', 100);
function ct_add_clear_transaction_log_to_bulk_actions($actions){    
    echo submit_button('Clear Transactions', 'secondary', 'action', false, ' style="margin-left: 10px" ');
}


function my_custom_bulk_actions($actions){
    unset( $actions['delete'] );
    return $actions;
}
add_filter('bulk_actions-users','my_custom_bulk_actions');

add_action('admin_init', 'ct_clear_user_transaction_logs');
function ct_clear_user_transaction_logs()
{
    global $wpdb;
    
    if(!is_admin())
        return;
    
    $user_id = $_GET['users'];
    if(!$user_id)
        return;
    
    if(isset($_GET['action']) && ($_GET['action'] == 'Clear Transactions' || $_GET['action'] == 'clear-transactions'))
    {
        $esb = new ManageESB();
        
        //Getting ESB IDs
        
        
        if(is_array($user_id))
        {            
            $query = "SELECT id FROM " . $wpdb->prefix . "users_subscriptions WHERE user_id in (" . implode(", ", $user_id) . ")";                
        }else{            
            $query = "SELECT id FROM " . $wpdb->prefix . "users_subscriptions WHERE user_id = $user_id";                
        }
        
        $esbIds = $wpdb->get_col($query);
        
        if($esbIds)
        {
            $query = "DELETE FROM " . $esb->table_conversation_metadata . " WHERE CUSTOMER_ID in (" . implode(", ", $esbIds) . ")";            
            ManageESB::$esbdb->query($query);
        }
        wp_redirect(admin_url() . 'users.php?clear-trans=1');
        exit;
    }
}

if(isset($_GET['clear-trans'])){
    add_action( 'admin_notices', 'ct_add_clear_transactions_notice' );
    function ct_add_clear_transactions_notice() {
        ?>
        <div id="message" class="updated">
            <p><?php _e( 'Transactions were removed successfully!', 'ct' ); ?></p>
        </div>
        <?php
    }
}