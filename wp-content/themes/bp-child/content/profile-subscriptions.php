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
                        if($currPrice < $row->price)
                            echo ceil($currPrice);
                        else 
                            echo ceil($row->price);                        
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
<div class="popup-box" id="unsubscription-confirm-box" style="display: none; width: 450px;">
    <form name="unsubscribe-form" action="" method="post">
        <div class="popup-box-header radius6 noradiusbottom">Confirm unsubscribing</div>        
        <div class="popup-box-content grid-box-body">    
            <p>Are you sure that you want to unsubscribe the subscription?<br > will remain active until then, and you can continue to test as normal by the end of this month.</p>        
            <p>If you check the below checkbox, the subscription will be cancelled immediately.</p>
        </div>
        <div class="popup-box-footer radius6 noradiustop">              
            <label class="left"><input type="checkbox" id="delete-now" name="delete-now" /> Delete immediately</label>
            <div class="right">
                <a href="#" class="action-btn process-btn submit-btn"><span class="p"></span><span class="t">OK</span></a>            
                <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
            </div>
            <div class="clear"></div>
        </div>
        <div class="loading loading-with-text radius6"><div><b>UNSUBSCRIBING</b><span>Please wait...</span></div></div>
        <a class="close_btn"></a>
        <input type="hidden" name="id" id="subscription-id" value="" />    
        <?php wp_nonce_field('unsubscribe', '_paymentnonce'); ?>
    </form>
</div>