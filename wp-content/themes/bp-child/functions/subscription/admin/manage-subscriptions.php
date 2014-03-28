<?php
/**
* Manage Subscriptions
*/

require_once(THE_FUNCTION . '/subscription/admin/users-subscriptions-list-table.php');
require_once(THE_FUNCTION . '/subscription/admin/users-payments-logs-list-table.php');

add_action('admin_menu', 'ct_add_manage_subscriptions_menu');

function ct_add_manage_subscriptions_menu()
{
    add_menu_page("Payments", "Payments", "manage_options", "manage-payments", "ct_manage_subscriptions_users_list");
    add_submenu_page("manage-payments", "Users", "Users", "manage_options", "users", "ct_manage_subscriptions_users_list");
    add_submenu_page("manage-payments", "Processing", "Processing", "manage_options", "processing", "ct_manage_subscriptions_trigger_processing");    
    
    wp_enqueue_style("manage-payments", get_stylesheet_directory_uri() . "/functions/subscription/admin/manage-payments.css");
}

function ct_manage_subscriptions_users_list()
{
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    if($action == 'detail')
    {
        ct_manage_subscriptions_show_user_detail();
    }else{
        ct_manage_subscriptions_show_users_list();
    }
}


function ct_manage_subscriptions_trigger_processing()
{
    $action = isset($_GET['action']) ? $_GET['action'] : 'list';    
    if($action == 'list')
        ct_manage_subscriptions_payments_logs_list();
    else if($action == 'preview')
        ct_manage_subscriptions_trigger_processing_run();
}

function ct_manage_subscriptions_payments_logs_list()
{
    $listTable = new CT_Users_Payments_Logs_List_Table();
    $listTable->prepare_items();
    ?>
    <div class="wrap">
        <h2>Payments</h2>
        <a href="<?php echo admin_url()?>admin.php?page=processing&action=preview" class="button button-primary">Processing Payments</a>
        <form name="adminform" action="users.php?page=processing" method="post">
        <?php
            echo $listTable->display();
        ?>
        </form>
    </div>
    <?php       
}

function ct_manage_subscriptions_trigger_processing_run()
{
    global $wpdb;
    
    $step = isset($_GET['step']) ? $_GET['step'] : 'preview';    
    
    ?>
    <div class="wrap">
        <h2><?php echo $step == 'confirm' ? 'Processing Run' : 'Preview Processing' ?></h2>        
        <?php
            if($step == 'confirm'){
                
            }else{
                ?>
                <h4 style="float: left">Date: <?php echo date("Y-m-d"); ?></h4>                
                <a href="<?php echo admin_url()?>admin.php?page=processing&action=confirm" class="button button-primary left" style="float: right">Continue Processing</a>        
                <br clear="all" />
                <table class="widefat processing-payments-results">
                    <thead>
                        <tr>
                            <th rowspan="2">No</th>                                                        
                            <th rowspan="2">Purchaser</th>
                            <th rowspan="2">Subscriptions</th>
                            <th rowspan="2">Monthly Fee</th>
                            <th rowspan="2">Total Amount</th>
                            <th colspan="3" style="text-align: center" class="current-stats">Before Processing</th>
                            <th colspan="3" style="text-align: center" class="new-stats">After Processing</th>
                        </tr>
                        <tr>
                            <th style="text-align: center" class="current-stats">Payment Method Status</th>
                            <th style="text-align: center" class="current-stats">Subscription Status</th>
                            <th style="text-align: center" class="current-stats">Due Date</th>
                            <th style="text-align: center" class="new-stats">Payment Method Status</th>
                            <th style="text-align: center" class="new-stats">Subscription Status</th>
                            <th style="text-align: center" class="new-stats">Due Date</th>
                            <th rowspan="2">ID</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $query = $wpdb->prepare("SELECT up.*, count(us.id) AS subscriptions, us.suite_id, uc.status AS card_status FROM {$wpdb->prefix}users_purchases AS up 
                            LEFT JOIN {$wpdb->prefix}users_subscriptions AS us ON up.id=us.purchase_id                       
                            LEFT JOIN {$wpdb->prefix}users_cards AS uc ON up.card_id=uc.id                       
                            WHERE up.expiry_date <= %s AND uc.status='Active'
                            GROUP BY up.id
                            ", date("Y-m-d"));
                        $payments = $wpdb->get_results($query);
                        
                        if(!$payments):
                        ?>
                        <tr>
                            <td colspan="11" class="alt">No Due Payments Found!</td>
                        </tr>
                    <?php
                        else:
                        
                        foreach($payments as $i=>$row){
                    ?>
                        <tr  <?php echo $i % 2 == 0 ? 'class="alt"' : '' ?>>
                            <td><?php echo $i + 1?></td>                            
                            <td><a href="<?php echo admin_url()?>admin.php?page=users&action=detail&id=<?php echo $row->user_id?>" target="_blank"><?php echo cp_get_user_fullname($row->user_id)?></a></td>
                            <td>
                                <?php
//                                    echo $row->subscriptions;
                                    $query = "SELECT us.*, p.post_title AS suite_title FROM {$wpdb->prefix}users_subscriptions AS us
                                              LEFT JOIN {$wpdb->posts} AS p ON p.ID=us.suite_id  
                                              WHERE purchase_id={$row->id}
                                              ORDER BY us.user_id, post_title";
                                              
                                    $srows = $wpdb->get_results($query);
                                    foreach($srows as $srow){
                                        echo $srow->suite_title . " by <a href='" . admin_url() ."admin.php?page=users&action=detail&id=" . $srow->user_id . "'>" . cp_get_user_fullname($srow->user_id) . "</a><br />";
                                    }
                                ?>
                            </td>
                            <td>$<?php $monthlyFee = getSubscriptionMonthlyFee2($row->suite_id, $row->monthly_fee, $row->user_id); echo $monthlyFee;?>/month</td>
                            <td>
                                <?php $months = ct_get_months($row->expiry_date, date("Y-m-d")) + 1; ?>
                                $<?php echo intval($monthlyFee) * $months ?> ($<?php echo $monthlyFee?> * <?php echo $months ?> months)
                            </td>
                            
                            <td style="text-align: center" class="current-stats">
                                <span class="ct-status <?php echo strtolower($row->card_status)?>">
                                    <?php echo $row->card_status?>
                                </span>
                            </td>
                            <td style="text-align: center" class="current-stats">
                                <span class="ct-status <?php echo strtolower($row->status)?>">
                                    <?php echo $row->status?>
                                </span>
                            </td>                            
                            <td style="text-align: center" class="current-stats"><?php echo $row->expiry_date?></td>
                            
                            <td style="text-align: center" class="new-stats">
                                <span class="ct-status <?php echo strtolower($row->card_status)?>">
                                    <?php echo $row->card_status?>
                                </span>
                            </td>
                            <td style="text-align: center" class="new-stats">
                                <span class="ct-status <?php echo strtolower($row->status)?>">
                                    <?php echo $row->status?>
                                </span>
                            </td>                            
                            <td style="text-align: center"  class="new-stats">
                                <?php
                                    echo date("Y-m-01", strtotime("+1 MONTH", time()));
                                ?>
                            </td>
                            <td><?php echo $row->id?></td>
                        </tr>
                        <?php   
                        }
                        endif;
                    ?>
                    </tbody>
                </table>
                <br clear="all" />
                <a href="<?php echo admin_url()?>admin.php?page=processing&action=confirm" class="button button-primary left" style="float: right">Continue Processing</a>        
                <br clear="all" />
                <?php
                
                
            }
        ?>    
    </div>
    <?php       
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
            <div id="message" class="error below-h2"><p>Invalid Request!</p></div>
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
        <table class="widefat" id="user-payment-methods">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Card Number</th>
                    <th>Nickname</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($cards as $i => $row): ?>
                <tr <?php echo $i % 2 == 0 ? 'class="alt"' : '' ?>>
                    <td><?php echo $row->id?></td>
                    <td><?php echo $row->card_number?></td>
                    <td><?php echo $row->nickname?></td>
                    <td><?php echo $row->email?></td>
                    <td><?php echo _ct_manage_subscriptions_get_statuses_html2('status', $row->status)?></td>
                    <td class="td-action"><a href="#" class="button button-primary" data-id="<?php echo $row->id?>">Update</a><img src="<?php echo admin_url()?>/images/wpspin_light.gif" class="loading" style="display: none;" /></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <script type="text/javascript">
            jQuery(document).ready(function($){
                $('#user-payment-methods tbody tr').each(function(){
                    $(this).find('a.button').click(function(){
                        var link = $(this);
                        $(this).hide();
                        $(this).next().show();
                        var ajaxData = {
                            'action': 'update_card_by_admin',
                            'id': $(this).attr('data-id'),
                            'status': $(this).parent().parent().find('select').val()
                        }

                        $.ajax({
                            url: ajaxurl,
                            data: ajaxData,
                            type: 'post',
                            success: function(rsp){
                                alert(rsp);
                                link.show();
                                link.next().hide();
                            }
                        })
                        return false;
                    })
                })
            })
        </script>
        <br />
        <h3>Subscriptions</h3>
        <table class="widefat" id="user-subscriptions-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Test Suite</th>
                    <th>Current Payment Amount</th>
                    <th>Next Due Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($subscriptions as $i => $row): ?>
                <tr <?php echo $i % 2 == 0 ? 'class="alt"' : '' ?>>
                    <td><?php echo $row->id?></td>
                    <td><?php echo get_the_title($row->suite_id)?></td>
                    
                    <td>
                        <?php 
                            $fee = getSubscriptionMonthlyFee2($row->suite_id, $row->monthly_fee, $row->user_id);
                            echo !$fee ? 'Free' : ('$' . $fee);
                        ?>
                    </td>
                    <?php 
                        $familyCounts = 0;
                        
                        if($row->user_id != $row->purchaser_id)
                            $readonly = true;
                        else
                            $readonly = false;
                            
                        if($i == 0 || $subscriptions[$i-1]->family_mark != $row->family_mark)
                        {
                            for($j = $i; $j < count($subscriptions); $j++)
                            {
                                if($subscriptions[$j]->family_mark == $row->family_mark)
                                    $familyCounts++;
                                else
                                    break;
                            }
                            ?>
                        <td rowspan="<?php echo $familyCounts?>" style="vertical-align: middle"><input type="date" name="expiry_date" value="<?php echo $row->expiry_date?>" <?php echo $readonly ? "readonly='readonly'" : ''?> /></td>
                        <td rowspan="<?php echo $familyCounts?>" style="vertical-align: middle"><?php echo _ct_manage_subscriptions_get_statuses_html('status', $row->status, $readonly ? ' disabled="disabled" ' : '')?></td>
                        <td rowspan="<?php echo $familyCounts?>" style="vertical-align: middle">
                            <?php if(!$readonly) : ?>
                            <a href="#" class="button button-primary" data-id="<?php echo $row->id?>">Update</a><img src="<?php echo admin_url()?>/images/wpspin_light.gif" class="loading" style="display: none;" />
                            <?php else: ?>
                            This is organisation subscription. Organisation user is <a target="_blank" href="<?php echo admin_url()?>/admin.php?page=users&action=detail&id=<?php echo $row->purchaser_id?>"><?php echo cp_get_user_fullname($row->purchaser_id)?></a>
                            <?php endif; ?>
                        </td>
                            <?php
                        }
                    ?>                    
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <script type="text/javascript">
            jQuery(document).ready(function($){
                $('#user-subscriptions-table tbody tr').each(function(){
                    $(this).find('a.button').click(function(){
                        var link = $(this);
                        $(this).hide();
                        $(this).next().show();
                        var ajaxData = {
                            'action': 'update_subscription_by_admin',
                            'id': $(this).attr('data-id'),
                            'status': $(this).parent().parent().find('select').val(),
                            'expiry': $(this).parent().parent().find('input[name="expiry_date"]').val()
                        }

                        $.ajax({
                            url: ajaxurl,
                            data: ajaxData,
                            type: 'post',
                            success: function(rsp){
                                alert(rsp);
                                link.show();
                                link.next().hide();
                            }
                        })
                        return false;
                    })
                })
            })
        </script>
    </div>
    <?php
    
}

function _ct_manage_subscriptions_get_statuses_html($name = 'status', $default = '', $attr = '')
{
    $statuses = array('Active', 'InArrears', 'Frozen', 'Unsubscribing');
    $html = '<select name="' . $name . '" ' .  $attr . '>';
    foreach($statuses as $s)
    {
        $html .= '<option value="' . $s . '" ' . ($default == $s ? 'selected="selected"' : '') . '>' . $s . '</option>';
    }
    $html .= '</select>';
    
    return $html;
}

function _ct_manage_subscriptions_get_statuses_html2($name = 'status', $default = '', $attr = '')
{
    $statuses = array('Active', 'Suspended');
    $html = '<select name="' . $name . '" ' .  $attr . '>';
    foreach($statuses as $s)
    {
        $html .= '<option value="' . $s . '" ' . ($default == $s ? 'selected="selected"' : '') . '>' . $s . '</option>';
    }
    $html .= '</select>';
    
    return $html;
}

/**
* Backend Actions
*/
add_action('wp_ajax_update_subscription_by_admin', 'ct_manage_subscriptions_update_subscriptions_by_ajax');
function ct_manage_subscriptions_update_subscriptions_by_ajax()
{
    global $wpdb;
    
    if(!is_admin() && !is_super_admin())    
    {
        die("Invalid Request");
    }
    
    $sid = $_POST['id'];
    $status = $_POST['status'];
    $expiry_date = date('Y-m-d', strtotime($_POST['expiry']));
    
    //Getting Purchase ID
    $query = $wpdb->prepare("SELECT purchase_id FROM {$wpdb->prefix}users_subscriptions WHERE id=%d", $sid);
    $purchase_id = $wpdb->get_var($query);
    
    $purchase = new CT_Purchase($purchase_id);
    $purchase->load();
    
    if(!$purchase)
    {
        die("Invalid Request");
    }
    
    //Update Expiry Date
    $wpdb->update($wpdb->prefix . "users_purchases", array('expiry_date' => $expiry_date), array('id' => $purchase_id));
    
    if($purchase->status != $status)
    {
        if($status == 'InArrears')
        {
            $purchase->inArrears();                
        }else if($status == 'Frozen'){
            $purchase->frozen();                
        }else if($status == 'Active'){
            $purchase->active();
        }else if($status == 'Unsubscribing'){
            $query = "SELECT * FROM {$wpdb->prefix}users_subscriptions WHERE purchase_id=" . $purchase_id;
            $srows = $wpdb->get_results($query);
            foreach($srows as $srow)
            {
                $subObj = new CT_Subscription($srow->id);
                $subObj->load();
                $subObj->unsubscribing();
            }
        }
    }
    
    echo 'Successfully updated!';
    
    exit;
}

add_action('wp_ajax_update_card_by_admin', 'ct_manage_subscriptions_update_card_by_ajax');
function ct_manage_subscriptions_update_card_by_ajax()
{
    global $wpdb;
    
    if(!is_admin() && !is_super_admin())    
    {
        die("Invalid Request!");
    }
    
    $cid = $_POST['id'];
    $status = $_POST['status'];
    
    
    //Getting Purchase ID
    $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}users_cards WHERE id=%d", $cid);
    $card = $wpdb->get_row($query);
    
    if(!$card)
    {
        die("Invalid Request!");
    }
    
    if($status != $card->status)
    {
        $wpdb->update($wpdb->prefix . "users_cards", array('status' => $status), array('id' => $card->id));
        /*if($status == 'Suspended')    
        {
            $wpdb->update($wpdb->prefix . "users_cards", array('status' => 'Suspended'), array('id' => $card->id));
        }else if($status == 'Active'){            
            $wpdb->update($wpdb->prefix . "users_cards", array('status' => 'Active'), array('id' => $card->id));
            
        }*/
    }
    
    echo 'Successfully updated!';
    
    exit;
}

