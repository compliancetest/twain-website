<?php
/**
* Manage the Signup-Fee per a users per suite
*/

add_action("admin_menu", "ct_users_signup_fee_menu");

function ct_users_signup_fee_menu()
{
    add_users_page("Manage User Signup Fee", "Signup Fee", "manage_options", "subscription_signup_fee", "ct_manage_users_signup_fee");
}

function ct_manage_users_signup_fee()
{
    global $wpdb;
    
    require_once('user-suite-list-table.php');
    
    if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'save')
    {
        $userID = $_REQUEST['id'];
        $suites = $_POST['suite_id'];
        $result1 = array();
        $result2 = array();
        foreach($suites as $sid)
        {
            $sid = intval($sid);
            if(isset($_POST['set_value' . $sid]) && $_POST['signup_fee' . $sid])
            {
               $result1[$sid] = $_POST['signup_fee' . $sid];               
            }
            if(isset($_POST['set_value' . $sid]) && $_POST['monthly_fee' . $sid])
            {
               $result2[$sid] = $_POST['monthly_fee' . $sid];               
            }
            
        }
        update_user_meta($userID, 'signup_fee', $result1);
        update_user_meta($userID, 'monthly_fee', $result2);
        $msg = 'Successfully Saved!';
    }
    
    if(isset($_REQUEST['id']) && $_REQUEST['id'])
    {
        $userID = $_REQUEST['id'];
        $userData = get_userdata($userID);
        $signup_fee = get_user_meta($userID, 'signup_fee', true);
        $monthly_fee = get_user_meta($userID, 'monthly_fee', true);
        if(!$signup_fee)
            $signup_fee = array();
        if(!$monthly_fee)
            $monthly_fee = array();
        
        
        $args = array(
                'post_type' => 'test-suite',         
                'posts_per_page' => -1,
                'tax_query' => array('relation' => 'and'),
                'orderby' => 'title',
                'order' => 'asc'
        );
        $all_posts = new WP_Query($args);
        
        $all_posts->set('suppress_filters', false);
        add_filter('posts_join_paged', 'add_community_join_query', 100, 2);
        add_filter('posts_orderby', 'add_community_orderby_query', 100, 2);
        add_filter('posts_fields_request', 'add_community_fields_query', 100, 2);

        $testsuites = $all_posts->get_posts();

        //Remove Filters
        remove_filter('posts_join_paged', 'add_community_join_query');
        remove_filter('posts_orderby', 'add_community_orderby_query');
        remove_filter('posts_fields_request', 'add_community_fields_query');
        
        //Getting User Purchases
        $query = "Select s.*, p.paid_amount FROM {$wpdb->prefix}users_subscriptions s LEFT JOIN {$wpdb->prefix}users_purchases as p ON p.id=s.purchase_id WHERE s.user_id=" . $userID;
        $results = $wpdb->get_results($query);
        
        $purchases = array();
        foreach($results as $r)
        {
            $purchases[$r->suite_id] = $r->paid_amount;
        }
        
        ?>
        <div class="wrap">
            <h2>Edit Fee for <?php echo $userData->first_name . " " . $userData->last_name ?></h2>
            <?php if(isset($msg)){ ?>
            <div id="message" class="updated below-h2"><p><?php echo $msg?></p></div>
            <?php } ?>
            <a href="users.php?page=subscription_signup_fee">Back to the list</a>
            <br />
            <form name="adminform" action="users.php?page=subscription_signup_fee" method="post">
                <input type="hidden" name="page" value="subscription_signup_fee" />
                <input type="hidden" name="action" value="save" />
                <input type="hidden" name="id" value="<?php echo $userID?>" />
                <p><input type="submit" value="Save" class="button button-primary button-large" /></p>
                <table border="1" style="" cellpadding="5" id="editFeeTable">
                    <thead>
                        <tr>
                            <th rowspan="2">Community</th>
                            <th colspan="3">Suite</th>
                            <th colspan="4">User</th>
                            <th rowspan="2">Set Value</th>
                        </tr>
                        <tr>
                            <th>Title</th>
                            <th>Sign-up Fee</th>
                            <th>Monthly Fee</th>
                            <th>Sign-up Fee</th>
                            <th>Monthly Fee</th>
                            <th>Purchased<br />Subscription</th>
                            <th>First Payment Price</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        
                        
                        foreach($testsuites as $suite)
                        {
                            $group = groups_get_group(array('group_id' => get_post_meta($suite->ID, 'community_id', true)));
                            ?>
                            <tr>
                                <td><?php echo bp_get_group_name($group) ?></td>
                                <td><?php echo $suite->post_title?></td>
                                <td>
                                    <?php 
                                        $price = get_post_meta($suite->ID,'signup_price', true);
                                        if($price > 0)
                                            echo '$' . $price;
                                        else 
                                            echo $price;
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                        $price = get_post_meta($suite->ID,'monthly_subscription_price', true);
                                        if($price > 0)
                                            echo '$' . $price;
                                        else 
                                            $price;
                                    ?>
                                </td>
                                <td><input type="text" name="signup_fee<?php echo $suite->ID?>" value="<?php echo isset($signup_fee[$suite->ID]) ? $signup_fee[$suite->ID] : '' ?>"
                                     <?php echo isset($signup_fee[$suite->ID]) || isset($monthly_fee[$suite->ID]) ? '' : 'disabled="disabled"' ?> /></td>
                                <td><input type="text" name="monthly_fee<?php echo $suite->ID?>" value="<?php echo isset($monthly_fee[$suite->ID]) ? $monthly_fee[$suite->ID] : '' ?>"
                                     <?php echo isset($signup_fee[$suite->ID]) || isset($monthly_fee[$suite->ID]) ? '' : 'disabled="disabled"' ?> /></td>
                                <td align="center"><?php echo isset($purchases[$suite->ID]) ? 'Yes' : 'No'?></td>
                                <td align="center"><?php echo isset($purchases[$suite->ID]) ? '$' . $purchases[$suite->ID] : '-'?></td>
                                <td>
                                    <input type="checkbox" name="set_value<?php echo $suite->ID?>" value="1" <?php echo isset($signup_fee[$suite->ID]) || isset($monthly_fee[$suite->ID]) ? 'checked="checked"' : '' ?>   />
                                    <input type="hidden" name="suite_id[]" value="<?php echo $suite->ID?>" />
                                </td>                                
                            </tr>
                            <?php
                        }
                    ?>
                    </tbody>
                </table>
                <p><input type="submit" value="Save" class="button button-primary button-large" /></p>
            </form>
            <script type="text/javascript">
                jQuery(document).ready(function($){
                    $('#editFeeTable tbody tr').each(function(){
                        var parent = $(this);
                        parent.find('input[type="checkbox"]').click(function(){
                            if(this.checked)
                                parent.find('input[type="text"]').prop('disabled', false);
                            else
                                parent.find('input[type="text"]').prop('disabled', true);
                        })
                    })
                })
            </script>
        </div>
        <?php
    }else{
        $listTable = new CT_User_Suite_List_Table();
        $listTable->prepare_items();
        ?>
        <div class="wrap">
            <h2>Users</h2>
            <form name="adminform" action="users.php?page=subscription_signup_fee" method="post">
            <?php
                echo $listTable->display();
            ?>
            </form>
        </div>
        <?php    
    }
}

function ct_edit_users_signup_fee()
{
    ?>
    <div class="wrap">
        <h2>Users</h2>
        <?php
            
        ?>
    </div>
    <?php
}