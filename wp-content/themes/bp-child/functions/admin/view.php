<?php
function ct_sync_users_to_mailchimp_page()
{
    global $wpdb;
    
    //Getting Total Users
    $total_users = $wpdb->get_var("SELECT count(*) FROM {$wpdb->users} WHERE user_status=0");
    
    ?>    
    <div class="wrap">    
        <div class="icon32" id="icon-tools"> <br /> </div>    
        <h2>Synchronise all users with Mailchimp</h2>   
        <p><b>Total Users:</b> <?php echo $total_users?></p>
        <div id="actions-wrap">
            <p class="current-action-progress" id="removing-subscribers"></p>
            <p class="current-action-progress" id="add-users"></p>
        </div>        
    </div>
    <script type="text/javascript">
        //Remove Current Subscribers
        function removeCurrentSubscribers(page)
        {
            jQuery.ajax({
                url : '<?php echo admin_url()?>',
                type : 'post',
                data : {'admin-action': '<?php echo wp_create_nonce('remove-current-subscribers')?>', 'page': page},
                dataType: 'json',
                success: function(jData){
                    if(jData.total)
                    {
                        jQuery('#removing-subscribers').html('Removing Subscribers: ' + jData.total + ' users removed');
                    }
                    if (jData.status == 'continue') {
                        removeCurrentSubscribers(jData.page)
                    } else {
                        jQuery('#removing-subscribers').append(' &mdash; <b>Completed!</b>');
                        jQuery('#removing-subscribers').addClass('action-completed');
                        jQuery('#add-users').html('Adding Users');                        
                        addUsers(1);
                    }
                },
                error: function(err){
                    alert(err.responseText);
                }
            });
        }
        jQuery('#removing-subscribers').append('Removing Subscribers');
        removeCurrentSubscribers(1);
        
        //Adding Users
        function addUsers(page)
        {
            jQuery.ajax({
                url : '<?php echo admin_url()?>',
                type : 'post',
                data : {'admin-action': '<?php echo wp_create_nonce('add-users-to-mailchimp')?>', 'page': page},
                dataType: 'json',
                success: function(jData){
                    if(jData.total)
                    {
                        jQuery('#add-users').html('Add Users: ' + jData.total + ' users added');
                    }
                    if (jData.status == 'continue') {
                        addUsers(jData.page)
                    } else {
                        jQuery('#add-users').append(' &mdash; <b>Completed!</b>');
                        jQuery('#add-users').addClass('action-completed');
                        jQuery('#actions-wrap').append('<h4>Completed!</h4>');
                    }
                },
                error: function(err){
                    alert(err.responseText);
                }
            });
        }
        
    </script>
    <?php
}

function ct_sync_users_to_mailchimp_page2()
{
    global $wpdb;
    
    $group_id = $_REQUEST['id'];
    
    //Getting Total Users
    $total_users = $wpdb->get_var("SELECT count(*) FROM {$wpdb->prefix}bp_groups_members WHERE group_id=" . $group_id . " AND is_confirmed=1");
    
    $group = groups_get_group(array('group_id' => $group_id));
    
    ?>    
    <div class="wrap">    
        <div class="icon32" id="icon-tools"> <br /> </div>    
        <h2>Synchronise all members of the community "<?php echo bp_get_group_name($group)?> with Mailchimp</h2>   
        <p><b>Total Members:</b> <?php echo $total_users?></p>
        <div id="actions-wrap">
            <p class="current-action-progress" id="removing-subscribers"></p>
            <p class="current-action-progress" id="add-users"></p>
        </div>        
    </div>
    <script type="text/javascript">
        //Remove Current Subscribers
        function removeCurrentSubscribers(page)
        {
            jQuery.ajax({
                url : '<?php echo admin_url()?>',
                type : 'post',
                data : {'admin-action': '<?php echo wp_create_nonce('remove-current-subscribers2')?>', 'page': page, 'id' : '<?php echo $group_id?>'},
                dataType: 'json',
                success: function(jData){
                    if(jData.total)
                    {
                        jQuery('#removing-subscribers').html('Removing Subscribers: ' + jData.total + ' users removed');
                    }
                    if (jData.status == 'continue') {
                        removeCurrentSubscribers(jData.page)
                    } else {
                        jQuery('#removing-subscribers').append(' &mdash; <b>Completed!</b>');
                        jQuery('#removing-subscribers').addClass('action-completed');
                        jQuery('#add-users').html('Adding Members');                        
                        addUsers(1);
                    }
                },
                error: function(err){
                    alert(err.responseText);
                }
            });
        }
        jQuery('#removing-subscribers').append('Removing Subscribers');
        removeCurrentSubscribers(1);
        
        //Adding Users
        function addUsers(page)
        {
            jQuery.ajax({
                url : '<?php echo admin_url()?>',
                type : 'post',
                data : {'admin-action': '<?php echo wp_create_nonce('add-users-to-mailchimp2')?>', 'page': page, 'id' : '<?php echo $group_id?>'},
                dataType: 'json',
                success: function(jData){
                    if(jData.total)
                    {
                        jQuery('#add-users').html('Add Members: ' + jData.total + ' users added');
                    }
                    if (jData.status == 'continue') {
                        addUsers(jData.page)
                    } else {
                        jQuery('#add-users').append(' &mdash; <b>Completed!</b>');
                        jQuery('#add-users').addClass('action-completed');
                        jQuery('#actions-wrap').append('<h4>Completed!</h4>');
                    }
                },
                error: function(err){
                    alert(err.responseText);
                }
            });
        }
        
    </script>
    <?php
}

function ct_process_cc_invoice_via_eway()
{
    global $wpdb;
    
    //Getting Pending Payments
    $payments = $wpdb->get_results("SELECT
                                        p.*,
                                        o.organisation_name,
                                        pm.nickname
                                   FROM {$wpdb->prefix}organisations_payments AS p
                                   LEFT JOIN {$wpdb->prefix}organisations AS o ON o.id=p.organisation_id
                                   LEFT JOIN {$wpdb->prefix}organisations_payment_methods AS pm ON pm.id=p.payment_method_id
                                   WHERE p.is_paid=0 ORDER BY p.date_added");
    
    ?>    
    <div class="wrap">    
        <div class="icon32" id="icon-tools"> <br /> </div>    
        <h2>Pay CC Invoices via eWay</h2>   
        <p><b>Total Pending Payments:</b> <?php echo count($payments)?></p>
        <table cellpadding="5" border="1" id="organisations-payments-table">
          <?php if(empty($payments)): ?>
            <tr><td>No data found.</td></tr>
          <?php else: ?>
            <tr>
                <th>ID</th>
                <th>Organisation</th>
                <th>Amount</th>
                <th>Payment Method</th>
                <th>Created Date</th>
                <th>Status</th>
            </tr>
            <?php foreach($payments as $p): ?>            
            <tr class="xero-payment-tr" data-id="<?php echo $p->id?>">
                <td><?php echo $p->id?></td>
                <td><?php echo $p->organisation_name?></td>
                <td>$<?php echo $p->amount?></td>
                <td>
                    <?php echo $p->nickname?> (ID: #<?php echo $p->payment_method_id?>)
                </td>
                <td><?php echo $p->date_added?></td>
                <td>
                    <p class="current-action-progress">Pending</p>
                </td>
            </tr>
            <?php endforeach; ?>            
          <?php endif; ?>            
        </table>
    </div>
    <script type="text/javascript">
        //Remove Current Subscribers
        function ct_processing_payment(idx)
        {
            if(idx >= jQuery('.xero-payment-tr').length)
            {
                return jQuery('#organisations-payments-table').after('<br /><b>Completed!</b>');
            }
            
            var payment_id = jQuery('.xero-payment-tr').eq(idx - 1).attr('data-id');
            
            jQuery.ajax({
                url : '<?php echo admin_url()?>',
                type : 'post',
                data : {'admin-action': '<?php echo wp_create_nonce('process-cc-payment')?>', 'id': payment_id},
                dataType: 'json',
                success: function(jData){
                    if(jData.total)
                    {
                        jQuery('#removing-subscribers').html('Removing Subscribers: ' + jData.total + ' users removed');
                    }
                    if (jData.status == 'continue') {
                        removeCurrentSubscribers(jData.page)
                    } else {
                        jQuery('#removing-subscribers').append(' &mdash; <b>Completed!</b>');
                        jQuery('#removing-subscribers').addClass('action-completed');
                        jQuery('#add-users').html('Adding Members');                        
                        addUsers(1);
                    }
                },
                error: function(err){
//                    alert(err.responseText);
                }
            });
        }
        
        ct_processing_payment(1);

        
    </script>
    <?php
}