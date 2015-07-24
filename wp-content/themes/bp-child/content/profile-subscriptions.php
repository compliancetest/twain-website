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
               <div class="td td-status tocenter">Status</div>
               <div class="td td-action tocenter">Action</div>
               <div class="clear"></div>
           </div>
           <div class="tbody">
           <?php
               $subscriptions =  getUserSubscriptions(null, true);
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
                        <a href="<?php echo get_permalink($row->suite_id)?>"><?php echo $row->suite_title ?></a>
                    </div>
                    <div class="td td-fee">$<?php 
                        $currPrice = get_post_meta($row->suite_id, 'monthly_subscription_price', true); 
                        if($currPrice < $row->monthly_fee)
                            echo ceil($currPrice);
                        else 
                            echo ceil($row->monthly_fee);                        
                    ?>/month</div>
                    <div class="td td-status">
                        <span class="status_btn status_<?php echo strtolower($row->status)?> has-tooltip">
                            <?php echo $row->status?>
                            <span class="simple_tooltip radius6">
                                <?php
                                    switch($row->status)
                                    {
                                        case 'Active';
                                            echo 'You have an active subscription to this test suite.';
                                            break;
                                        case 'InArrears';
                                            echo 'There is a problem with the payment method associated with your subscription to this test suite.';
                                            break;
                                        case 'Frozen';
                                            echo 'Testing is frozen until the problem with the payment method associated with this subscription is resolved.';
                                            break;
                                        case 'Unsubscribing';
                                            echo 'You have requested to be unsubscribed from this test suite. This will occur at the end of the month.';
                                            break;
                                    }
                                ?>
                            <span></span></span>
                        </span>
                    </div>
                    <div class="td td-action tocenter">
                        <a href="javascript: void(0)" class="action-btn harness-detail-btn harness-detail-link has-tooltip" data-id="<?php echo $row->id?>"><span class="p"></span><span class="simple_tooltip">Harness Details<span></span></span></a>
                        <?php if($row->status != 'Unsubscribing'){ ?>
                        <a href="javascript: void(0)" class="action-btn unsubscribe-btn icon-btn left10 unsubscribe-link has-tooltip" data-status="<?php echo $row->status?>" data-id="<?php echo $row->id?>"><span class="p"></span><span class="simple_tooltip">Unsubscribe<span></span></span></a><br />
                        <?php } ?>
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
