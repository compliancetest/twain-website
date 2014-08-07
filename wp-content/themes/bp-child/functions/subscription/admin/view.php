<?php
/**
* Manage Subscriptions
*/
function ct_manage_organisation_subscriptions_list()
{
   $listTable = new CT_organisation_subscriptions_List_Table();
    $listTable->prepare_items();
    ?>
    <div class="wrap">
        <h2>
            Organisation Subscriptions
            <a href="<?php echo admin_url()?>admin.php?page=add-organisation-subscription" class="add-new-h2">Add New Subscription</a>
        </h2>        
        <?php
            flushMessages();
        ?>
        <form name="adminform" action="admin.php" method="get">
            <?php
                echo $listTable->display();
            ?>
            <input type="hidden" name="page" value="organisation-subscriptions" />
        </form>
    </div>
    <?php
}

function ct_add_organisation_subscription()
{
    global $wpdb;
    
    $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
    
    if ($id) {
       $data = ct_get_organisation_subscription_by_id($id);
    } 
    
    //Getting Organisations
    $query = "SELECT organisation_name, id from {$wpdb->prefix}organisations ORDER BY organisation_name";
    $organisations = $wpdb->get_results($query);
      
    $query = "SELECT distinct(family_mark), suite_title from {$wpdb->prefix}test_suites ORDER BY suite_title";
    $test_suites = $wpdb->get_results($query);
    
    //Getting Payment Methods
    $org_id = isset($data) ? $data->organisation_id : $organisations[0]->id;
    
    $orgClass = new CT_Organisation($org_id);
    $payment_methods = $orgClass->get_payment_methods();
    
    $users = $orgClass->get_organisation_members();
        
?>
    <div class="wrap">
        <h2>
            <?php echo !$id ? 'New' : 'Edit' ?> Subscription
        </h2>        
        <form name="adminform" id="organisationform" action="admin.php" method="post">
            <table class="widefat" style="width: auto;">
                <tr>
                    <th>Organisation</th>
                    <td>
                        <select name="organisation_id" id="organisation_id">                          
                          <?php foreach($organisations as $org): ?>
                          <option value="<?php echo $org->id?>" <?php echo isset($data) && $data->organisation_id == $org->id ? 'selected="selected"' : '' ?>><?php echo $org->organisation_name?></option>
                          <?php endforeach; ?>
                       </select>
                       <img src='/wp-admin/images/loading.gif' id='get-org-info' style="display: none;" />
                    </td>
                </tr>
                <tr>
                    <th>Test Suite</th>
                    <td>
                        <select name="suite_family_mark">
                          <?php foreach($test_suites as $s): ?>
                          <option value="<?php echo $s->family_mark?>" <?php echo isset($data) && $data->suite_family_mark == $s->family_mark ? 'selected="selected"' : '' ?>><?php echo $s->suite_title?></option>
                          <?php endforeach; ?>
                      </select>
                    </td>
                </tr>
                
                <tr>
                    <th>Nickname</th>
                    <td><input type="text" name="nickname" id="nickname" value="<?php echo isset($data) ? $data->nickname : ''?>" size="40"  /></td>
                </tr>  
                <tr>
                    <th>Status</th>
                    <td>
                        <?php echo _ct_manage_subscriptions_get_statuses_html() ?>
                    </td>
                </tr>  
                
                <tr>
                    <th>Payment Method</th>
                    <td>
                        <select name="payment_method" id="payment_method">  
                            <?php foreach($payment_methods as $p): ?>
                            <option value="<?php echo $p->id?>" <?php echo isset($data) && $data->payment_method == $p->id ? 'selected="selected"' : '' ?>><?php echo $p->nickname?>(<?php echo $p->invoice_me == 0 ? chunk_split($p->card_number, 4) : 'Invoice' ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th>Date</th>
                    <td>
                        <input type="text" name="purchased_date" id="purchased_date" value="<?php echo isset($data) ? $data->purchased_date : date("Y-m-d H:i:s")?>" size="40"  />
                    </td>
                </tr>
                
                <tr>
                    <th>Assignee</th>
                    <td>
                        <select name="user_id" id="user_id">
                            <?php foreach($users as $u): ?>
                            <option value="<?php echo $u->ID?>" <?php echo isset($data) && $data->user_id == $u->ID ? 'selected="selected"' : '' ?>><?php echo get_user_meta($u->ID, 'first_name', true) . " " . get_user_meta($u->ID, 'last_name', true)?>, <?php echo $u->user_email?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="submit" class="button-primary" value="Save" />
                    </td>
                </tr>
            </table>
            <?php
                wp_nonce_field('save-organisation-subscription', 'subscription_admin_action');
            ?>
            <input type="hidden" name="id" value="<?php echo $id?>" />
            <input type="hidden" name="page" value="add-organisation-subscriptions" />
            <input type="hidden" name="" />
        </form>
        <script type="text/javascript">
            (function($){
                $('#organisation_id').change(function(){
                    //Getting payment methods and assignee
                    var ajaxData = {
                        'action': 'get_organisation_info_on_admin',
                        'id': $(this).val()                        
                    }
                    $('#organisationform #get-org-info').show();
                    $.ajax({
                        url: ajaxurl,
                        data: ajaxData,
                        type: 'post',
                        dataType: 'xml',
                        success: function(rsp){
                            $('#organisationform #payment_method').html($(rsp).find('methods').text());
                            $('#organisationform #user_id').html($(rsp).find('users').text());
                        },
                        error: function(err){
                            alert(err.responseText);
                        },
                        complete: function(err){                            
                            $('#organisationform #get-org-info').hide();        
                        }
                    })
                })    
            })(jQuery)            
        </script>
    </div>
    <?php
}

function ct_manage_user_subscriptions_list()
{
   $listTable = new CT_User_Subscriptions_List_Table();
    $listTable->prepare_items();
    ?>
    <div class="wrap">
        <h2>
            User Subscriptions
            <a href="<?php echo admin_url()?>admin.php?page=add-user-subscription" class="add-new-h2">Add New Subscription</a>
        </h2>        
        <?php
            flushMessages();
        ?>
        <form name="adminform" action="admin.php" method="get">
            <?php
                echo $listTable->display();
            ?>
            <input type="hidden" name="page" value="user-subscriptions" />
        </form>
    </div>
    <?php
}

function ct_add_user_subscription()
{
    global $wpdb;
    
    $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
    
    if ($id) {
       $data = ct_get_user_subscription_by_id($id);
    } 
    
    //Getting Organisation Subscriptions
    $query = "SELECT nickname, id, suite_family_mark, organisation_id from {$wpdb->prefix}organisations_subscriptions ORDER BY organisation_id, nickname";
    $org_subscriptions = $wpdb->get_results($query);
    
    //Getting Payment Methods
    $family_mark = $org_subscriptions[0]->suite_family_mark;
    $org_id = $org_subscriptions[0]->organisation_id;
    if ($data) {
        foreach($org_subscriptions as $s) {
            if ($s->id == $data->parent_id) {
                $family_mark = $s->suite_family_mark;
                $org_id = $s->organisation_id;
            }
        }
        
    }

    $query = "SELECT p.ID, p.post_title from {$wpdb->prefix}test_suites as t LEFT JOIN {$wpdb->posts} AS p ON t.suite_id=p.ID WHERE t.family_mark=$family_mark ORDER BY p.post_title";
    $test_suites = $wpdb->get_results($query);
    
    
    $orgClass = new CT_Organisation($org_id);
    
    $users = $orgClass->get_organisation_members();
        
?>
    <div class="wrap">
        <h2>
            <?php echo !$id ? 'New' : 'Edit' ?> Subscription
        </h2>        
        <form name="adminform" id="organisationform" action="admin.php" method="post">
            <table class="widefat" style="width: auto;">
                <tr>
                    <th>Organisation Subscriptions</th>
                    <td>
                        <select name="organisation_subscription_id" id="organisation_subscription_id">                          
                          <?php foreach($org_subscriptions as $s): ?>
                          <option value="<?php echo $s->id?>" <?php echo isset($data) && $data->parent_id == $s->id ? 'selected="selected"' : '' ?>><?php echo $s->nickname?></option>
                          <?php endforeach; ?>
                       </select>
                       <img src='/wp-admin/images/loading.gif' id='get-org-info' style="display: none;" />
                    </td>
                </tr>
                <tr>
                    <th>Test Suite</th>
                    <td>
                        <select name="suite_id" id="suite_id">
                          <?php foreach($test_suites as $s): ?>
                          <option value="<?php echo $s->ID?>" <?php echo isset($data) && $data->suite_id == $s->ID ? 'selected="selected"' : '' ?>><?php echo $s->post_title?></option>
                          <?php endforeach; ?>
                      </select>
                    </td>
                </tr>
                
                <tr>
                    <th>Assignee</th>
                    <td>
                        <select name="user_id" id="user_id">
                            <?php foreach($users as $u): ?>
                            <option value="<?php echo $u->ID?>" <?php echo isset($data) && $data->user_id == $u->ID ? 'selected="selected"' : '' ?>><?php echo get_user_meta($u->ID, 'first_name', true) . " " . get_user_meta($u->ID, 'last_name', true)?>, <?php echo $u->user_email?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="submit" class="button-primary" value="Save" />
                    </td>
                </tr>
            </table>
            <?php
                wp_nonce_field('save-user-subscription', 'subscription_admin_action');
            ?>
            <input type="hidden" name="id" value="<?php echo $id?>" />
            <input type="hidden" name="page" value="add-user-subscriptions" />
            <input type="hidden" name="" />
        </form>
        <script type="text/javascript">
            (function($){
                $('#organisation_subscription_id').change(function(){
                    //Getting payment methods and assignee
                    var ajaxData = {
                        'action': 'get_organisation_subscription_info_on_admin',
                        'id': $(this).val()                        
                    }
                    $('#organisationform #get-org-info').show();
                    $.ajax({
                        url: ajaxurl,
                        data: ajaxData,
                        type: 'post',
                        dataType: 'xml',
                        success: function(rsp){
                            $('#organisationform #suite_id').html($(rsp).find('suites').text());
                            $('#organisationform #user_id').html($(rsp).find('users').text());
                        },
                        error: function(err){
                            alert(err.responseText);
                        },
                        complete: function(err){                            
                            $('#organisationform #get-org-info').hide();        
                        }
                    })
                })    
            })(jQuery)            
        </script>
    </div>
    <?php
}