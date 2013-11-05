<?php
/**
* Profile - My Test Suite Subscriptions
*/
if(!defined('ABSPATH'))
    die('Invalid Request!');
    
?>
<div class="column left three_fifths nopadding">
    <div class="grid-box table-box" id="my_subscriptions">
        <div class="grid-box-header">
            <h5>My Test Suite Subscriptions</h5>
            <div class="clear"></div>
        </div>
        <div class="grid-box-body">
            <div class="thead tr">
               <div class="td td-suite">Test Suite</div>
               <div class="td td-fee">Fee</div>
               <div class="td td-action tocenter">Action</div>
               <div class="clear"></div>
           </div>
           <div class="tbody">
           <?php
               $subscriptions =  getUserSubscriptions();
               if(count($subscriptions) < 1)
               {
           ?>
               <div class="tr">
                   <div class="td td-full">No subscription recorded yet.</div>
                   <div class="clear"></div>
               </div> 
           <?php
               }else{
                   foreach($subscriptions as $row)
                   {
                       
           ?>
                <div class="tr">
                    <div class="td td-suite">
                        <a href="<?php echo get_permalink($row->suite_id)?>"><?php echo get_post_meta($row->suite_id, 'ts_name',  true) ?></a>
                    </div>
                    <div class="td td-fee">$<?php echo get_post_meta($row->suite_id, 'monthly_subscription_price', true); ?>/m</div>
                    <div class="td td-action tocenter">
                        <a href="?_paymentnonce=<?php echo wp_create_nonce('unsubscribe') ?>&id=<?php echo $row->id ?>" class="action-btn harness-detail-btn harness-detail-link has-tooltip" data-id="<?php echo $row->id?>"><span class="p"></span><span class="simple_tooltip">Harness Details<span></span></span></a>
                        <a href="?_paymentnonce=<?php echo wp_create_nonce('unsubscribe') ?>&id=<?php echo $row->id ?>" class="action-btn unsubscribe-btn icon-btn left10 unsubscribe-link has-tooltip"><span class="p"></span><span class="simple_tooltip">Unsubscribe<span></span></span></a><br />                        
                    </div>
                    <input type="hidden" id="p_mode_agreement<?php echo $row->id?>" value="<?php echo $row->p_mode_agreement?>" />
                    <input type="hidden" id="harness_endpoint_url<?php echo $row->id?>" value="<?php echo $row->harness_endpoint_url?>" />
                    <input type="hidden" id="harness_username<?php echo $row->id?>" value="<?php echo $row->harness_username?>" />
                    <input type="hidden" id="harness_password<?php echo $row->id?>" value="<?php echo $row->harness_password?>" />
                    <input type="hidden" id="tester_endpoint_url<?php echo $row->id?>" value="<?php echo $row->tester_endpoint_url?>" />
                    <input type="hidden" id="tester_username<?php echo $row->id?>" value="<?php echo $row->tester_username?>" />
                    <input type="hidden" id="tester_password<?php echo $row->id?>" value="<?php echo $row->tester_password?>" />
                    <div class="clear"></div>
                </div>
           <?php
                   }
               }
           ?>
           <div class="loading1"></div>
           </div>
           
        </div>                    
    </div>
</div>
<?php $my_subscriptions_desc = get_post_meta($post->ID, 'my_subscriptions_desc', true);?>
<?php if($my_subscriptions_desc): ?>
<div class="right two_fifths">
    <div class="gray_message_box radius9 light_gray_txt">
        <div class="indicator"></div>
        <?php echo $my_subscriptions_desc; ?>
    </div>
</div>
<?php endif; ?>