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
    
    $esb = new ManageESB();
        
    //Getting ESB IDs
    $query = "SELECT id FROM " . $wpdb->prefix . "users_subscriptions WHERE user_id = $user_id";                
    $esbIds = $wpdb->get_col($query);
    
    //Delete Transaction Logs
    
    if($esbIds)
    {
        $query = "DELETE FROM " . $esb->table_conversation_metadata . " WHERE CUSTOMER_ID in (" . implode(", ", $esbIds) . ")";            
        ManageESB::$esbdb->query($query);
    }
    
    //Delete Payment Logs
    $wpdb->delete($wpdb->prefix . "users_transactions", array('user_id' => $user_id));
    
    //Decrease Joined User Count For Organisation Users    
    $purchases = $wpdb->get_results("select s.suite_id, p.user_id FROM {$wpdb->prefix}users_subscriptions AS s LEFT JOIN {$wpdb->prefix}users_purchases AS p ON p.id=s.purchase_id WHERE s.user_id=$user_id");
    foreach($purchases as $prow)
    {
        if($prow->user_id == $user_id)
            continue;
        
        $query = $wpdb->prepare("SELECT family_mark FROM {$wpdb->prefix}test_suites WHERE suite_id=%d", $prow->suite_id);
        $familyMark = $wpdb->get_var($query);
        
        //Decrease the joined_user
        $wpdb->query("UPDATE {$wpdb->prefix}users_organisation_pricing SET `joined_count`=`joined_count` - 1 WHERE user_id=" . $prow->user_id . " AND family_mark=" . $familyMark);
    }
    
    //Delete Subscriptions
    $wpdb->query("DELETE s FROM {$wpdb->prefix}users_subscriptions AS s LEFT JOIN {$wpdb->prefix}users_purchases AS p ON p.id=s.purchase_id WHERE p.user_id=$user_id");
    //Delete Purchases
    $wpdb->delete($wpdb->prefix . "users_purchases", array('user_id' => $user_id));
    //Delete Payment Methods
    $wpdb->delete($wpdb->prefix . "organisations_payment_methods", array('user_id' => $user_id));
    //Delete Test Plans
    $wpdb->delete($wpdb->prefix . "test_plans", array('creator_id' => $user_id));
    //Delete Compliance Claims
    $wpdb->delete($wpdb->prefix . "compliance_claims", array('creator_id' => $user_id));
    //Delete Profile Instances
    $wpdb->delete($wpdb->prefix . "community_profile_instances", array('creator_id' => $user_id, 'type' => 'tester'));
    //Delete User Extra Fields
    $wpdb->delete($wpdb->prefix . "users_extra", array("userID" => $user_id));
    
    //Unsubscribe the user from the mailchimp list
    $mailChimp = new Mailchimp(get_mailchimp_api_key(), array('ssl_verifypeer' => false));
    $mailChimpList = new Mailchimp_Lists($mailChimp);
    
    $userData = get_userdata($user_id);
    try{
        $mailChimpList->unsubscribe(DEFAULT_MAILCHIMP_LIST_ID, array('email' => $userData->user_email), true);                    
    }catch(Exception $e){
        return $user_id;
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

add_action('cp_show_user_all_data', 'cp_show_user_all_data', 10, 1);
function cp_show_user_all_data($userID)
{
    global $wpdb;
    
    $userData = get_userdata($userID);
    echo '<style type="text/css">
        .deleted-user-details{
            padding-left: 15px;
            border-bottom: dotted 1px #333;
        }
        .deleted-user-details h3{
            margin-bottom: 5px;
        }
        .deleted-user-details blockquote{
            margin-top: 0;
        }
        .deleted-user-details p{
            margin-top: 0;
            margin-bottom: 5px;
        }
        
    </style>
    ';
    //Echo User Email
    echo " (" . $userData->user_email . ")";
    
    echo '<div class="deleted-user-details">';
    
    //Getting User Joined Communities
    $result = groups_get_user_groups($userID);
    
    $groups = $result['groups'];
    
    echo '<h3 style="margin-bottom: 5px;">Communities</h3>';
    if(!$groups)
    {
        echo "<p><i>User is not joined to any communities.</i></p>";
    }else{
        echo '<ol>';
        foreach($groups as $gid)
        {
            $group = groups_get_group('group_id=' . $gid);
            echo '<li><a href="' . bp_get_group_permalink($group) . '" target="_blank">' . $group->name . '</a></li>';
        }
        echo '</ol>';
    }
    
    //Getting Subscriptions
    echo '<hr /><h3 style="margin-bottom: 5px;">Subscriptions</h3>';
    $subscriptions = getUserSubscriptions($userID, true);
    
    if(!$subscriptions)
    {
        echo '<p><i>User is not subscribed to any test suites.</i></p>';
    }else{
        echo '<ol>';
        foreach($subscriptions as $srow){
            echo '<li><a href="' . get_permalink($srow->suite_id) . '" target="_blank">' . $srow->suite_title . '</a> (Status: ' . $srow->status . ')</li>';
        }
        echo '</ol>';
    }
    
    //Getting Payment Methods
    $cards = getUserCreditCards($userID, false);
    if($cards)
    {
        echo '<hr /><h3 style="margin-bottom: 5px;">Payment Methods</h3>';
        echo '<ol>';
        foreach($cards as $row){
            echo '<li>' . $row->nickname . ' ' . $row->email .  ' (' . $row->card_number . ', Status: ' . $row->status . ')</li>';
        }
        echo '</ol>';
    }
    
    //Getting Products
    $args = array(
        'post_type' => 'product-service', 
        'posts_per_page' => -1,
        'author' => $userID
    );
    $products = get_posts($args);    
    if($products)
    {
        echo '<hr /><h3 style="margin-bottom: 5px;">Products</h3>';
        echo '<ol>';
        foreach($products as $row){
            echo '<li><a href="' . get_permalink($row->ID) . '" target="_blank">' . $row->post_title . '</a></li>';
        }
        echo '</ol>';
    }
    
    //Getting Test Plains
    $query = $wpdb->prepare("SELECT p.*, pm.meta_value as `product_name` FROM " . $wpdb->prefix . "test_plans AS p LEFT JOIN " . $wpdb->postmeta . " as pm on pm.post_id=p.product_id AND pm.meta_key='product_name'  WHERE p.creator_id=%d", $userID);
    $plains = $wpdb->get_results($query);
    if($plains)
    {
        echo '<hr /><h3 style="margin-bottom: 5px;">Test Plans</h3>';
        echo '<table cellpadding="5" border="1">';
        echo '<thead><tr><th>Test Suite</th><th>Product</th><th>Level</th><th>Role</th><th>Date</th></tr></thead>';
        echo '<tbody>';
        foreach($plains as $row){
            echo '<tr>
                <td><a href="' . get_permalink($row->suite_id) . '" target="_blank">' . get_the_title($row->suite_id) . '</a></td>
                <td><a href="' . get_permalink($row->product_id) . '" target="_blank">' . get_the_title($row->product_id) . '</a></td>
                <td>' . trim(str_replace(';;', ', ', $row->level), ', ') . '</td>                
                <td>' . trim(str_replace(';;', ', ', $row->role), ', ') . '</td>                
                <td>' . $row->created_date . '</td>                
            </tr>
            ';
            
        }
        echo '</tbody></table>';
    }
    
    //Getting Compliance Claims
    $query = $wpdb->prepare("SELECT c.*, pm.meta_value as `issuer` FROM " . TABLE_CLAIM . " AS c LEFT JOIN " . $wpdb->postmeta . " as pm on pm.post_id=c.suite_id AND pm.meta_key='ts_issuer'  WHERE creator_id=%d", $userID);
    $claims = $wpdb->get_results($query);
    if($claims)
    {
        echo '<hr /><h3 style="margin-bottom: 5px;">Compliance Claims</h3>';
        echo '<table cellpadding="5" border="1">';
        echo '<thead><tr><th>Claim ID</th><th>Certificate</th><th>Test Suite</th><th>Product</th><th>Issuer</th><th>Level</th><th>Role</th><th>Status</th><th>Date</th></tr></thead>';
        echo '<tbody>';
        foreach($claims as $row){
            echo '<tr>
                <td>' . $row->claim_id .'</td>
                <td><a href="javascript: void;" onclick="window.open(\'' . get_site_url() . '/claims/' . $row->token . '\', \'\', \'height=600\')">View PDF</a</td>                
                <td><a href="' . get_permalink($row->suite_id) . '" target="_blank">' . get_the_title($row->suite_id) . '</a></td>
                <td><a href="' . get_permalink($row->product_id) . '" target="_blank">' . get_the_title($row->product_id) . '</a></td>
                <td>' . $row->issuer . '</td>
                <td>' . $row->conformance_level . '</td>                
                <td>' . $row->role . '</td>                
                <td>' . $row->status . '</td>                
                <td>' . $row->last_updated . '</td>                
            </tr>
            ';
            
        }
        echo '</tbody></table>';
    }
    
    //Getting Profile Instances
    $testData = getCustomerProfileInstances($userID);
    if($testData)
    {
        echo '<hr /><h3 style="margin-bottom: 5px;">Test Data</h3>';
        echo '<table cellpadding="5" border="1">';
        echo '<thead><tr><th>Profile Name</th><th>Purpose</th><th>Description</th><th>Type</th></thead>';
        echo '<tbody>';
        foreach($testData as $instance){
            $instanceObj = json_decode(base64_decode($instance->content));
            
                                    
                                
            echo '<tr>
                    <td>' . $instance->profile_name;
                if($instanceObj->Profile->Version)
                {
                    $version = array();
                    foreach(get_object_vars($instanceObj->Profile->Version) as $k=>$v)      
                    {
                        $version[] = $v;
                    }
                    echo " v" . implode(".", $version);
                }
            echo  '</td>
                   <td>' . $instanceObj->Profile->Purpose .'</td>
                   <td>' . $instanceObj->Profile->Description .'</td>
                   <td>' . $instance->profile_type_title;
                   $pJSON = json_decode(base64_decode($instance->schema));                            
                    if($pJSON->Version)
                    {
                        $version = array();
                        foreach(get_object_vars($pJSON->Version) as $k=>$v)      
                        {
                            $version[] = $v;
                        }
                        echo " v" . implode(".", $version);
                    }
            echo   '</td>                
            </tr>
            ';
            
        }
        echo '</tbody></table>';
    }
    
    
    echo '<br /></div>';
}