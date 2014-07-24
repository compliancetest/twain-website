<?php
/*
 * Template Name: Organisation Test Suites
 */

if (!($organisation = ct_is_organisation_admin())) {    
    wp_redirect(home_url());
    exit;
}

$organisationClass = new CT_Organisation($organisation->id);

get_header();

?>
<div class="content" id="organisation-container">
    <div class="dashboard-tabs">
        <?php get_sidebar('dashboard'); ?>
    </div>
    <div class="container">
        <div class="column">
            <?php get_sidebar('organisation'); ?>
            <div id="item-body">
                <div id="organisation_test_suites" class="tab-content white_bcg column">
                    <div class="grid-box table-box" id="organisation_subscriptions">
                        <div class="grid-box-header">
                            <h5>The Organisation Subscriptions</h5>
                            <div class="clear"></div>
                        </div>
                        <div class="grid-box-body">
                            <div class="thead tr">
                               <div class="td td-suite">Test Suite</div>
                               <div class="td td-nickname">Nickname</div>
                               <div class="td td-assignee">Assignee</div>
                               <div class="td td-status tocenter">Status</div>
                               <div class="td td-action tocenter">Action</div>
                               <div class="clear"></div>
                           </div>
                           <div class="tbody">
                           <?php
                               $subscriptions =  $organisationClass->get_subscriptions();
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
                                        <?php echo $row->suite_title ?>
                                    </div>
                                    <div class="td td-nickname">
                                        <?php echo $row->nickname; ?>
                                    </div>
                                    <div class="td td-assignee">
                                        <?php echo $row->user_email; ?>
                                    </div>                                    
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
                                        <?php if($row->status != 'Unsubscribing'){ ?>
                                        <a href="javascript: void(0)" class="action-btn unsubscribe-btn icon-btn left10 unsubscribe-link has-tooltip" data-status="<?php echo $row->status?>" data-id="<?php echo $row->id?>"><span class="p"></span><span class="simple_tooltip">Delete<span></span></span></a><br />                        
                                        <?php } ?>
                                    </div>
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
                    <div class="clear"></div>
                </div>
            </div>
            <div class="clear"></div>            
        </div>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div> <!--end content-->

<div class="popup-box" id="subscribe-box" style="display: none;">
    <form name="paymentForm" id="paymentForm" action="">
        <div class="popup-box-header radius6 noradiusbottom">Purchase Subscription</div>        
        <div class="popup-box-content grid-box-body">    
            <div class="field-row">
                <h5>Confirm Existing Payment Method</h5>
                <span class="focus-tooltip"><span></span>You are about to purchase a monthly Subscription to: <a href="<?php echo get_permalink()?>"><?php echo $suite->name?></a> for $<?php echo $suite->monthlySubscriptionPriceValue?> per month (you can cancel anytime)</span>
            </div>
            <div class="field-row">
                <div class="grid-cell">
                    <label>Existing Card</label>
                    <select name="card_id" id="card_id" class="select">
                        <option value="">Select a Card</option>
                        <?php foreach($userCards as $row){ ?>
                        <option value="<?php echo $row->id?>">
                            <?php echo $row->nickname . " " . chunk_split(encrypt_card_number($row->card_number), 4)?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="clear"></div>
            </div>
            <div class="add-new-border"><span>or add new</span></div>
            <div class="field-row">
                <div class="grid-cell">
                    <label>Nickname</label>
                    <input type="text" name="nickname" id="nickname" value="" class="input" maxlength="50" />
                    <!--<img src="<?php echo CHILD_TEMPLATE_DIRECTORY?>/images/valid-icon.png" class="valid-icon" />-->
                </div>                
                <div class="clear"></div>
            </div>
            <div class="field-row">
                <div class="grid-cell">
                    <label>Email</label>
                    <input type="text" name="email" id="email" value="<?php echo $current_user->user_email ?>" class="input" maxlength="50" /> 
                    <br />
                    <span class="desc">(Invoices will be sent to this email.)</span>
                </div>                
                <div class="clear"></div>
            </div>
            
            <div class="field-row">
                <div class="grid-cell">
                    <label>Name on Card</label>
                    <input type="text" name="name_on_card" id="name_on_card" value="" class="input" />
                    <!--<img src="<?php echo CHILD_TEMPLATE_DIRECTORY?>/images/valid-icon.png" class="valid-icon" />-->
                </div>                
                <div class="clear"></div>
            </div>
            <div class="field-row">
                <div class="grid-cell">
                    <label>Card Number</label>
                    <input type="text" name="card_number" id="card_number" value="" class="input" />
                </div>                
                <div class="clear"></div>
            </div>
            <div class="field-row">
                <div class="grid-cell">
                    <label>Expiry Date</label>
                    <select name="exp_month" id="exp_month" class="select">
                        <option value="">Month</option>
                        <option value="1">Jan</option>
                        <option value="2">Feb</option>
                        <option value="3">Mar</option>
                        <option value="4">Apr</option>
                        <option value="5">May</option>
                        <option value="6">Jun</option>
                        <option value="7">Jul</option>
                        <option value="8">Aug</option>
                        <option value="9">Sep</option>
                        <option value="10">Oct</option>
                        <option value="11">Nov</option>
                        <option value="12">Dec</option>
                    </select>
                    <select name="exp_year" id="exp_year" class="select">
                        <option value="">Year</option>                        
                        <?php for($i=0; $i < 20; $i++){ ?>
                        <option value="<?php echo $i + date("y")?>"><?php echo $i + date("Y")?></option>
                        <?php } ?>
                    </select>                    
                </div>                
                <div class="clear"></div>
            </div>            
            <div class="field-row">
                <div class="grid-cell">
                    <label class="left">CVC</label>
                    <input type="text" name="card_cvc" id="card_cvc" placeholder="****" value="" class="input" />
                </div>                
                <div class="clear"></div>
            </div>
            <div class="field-row notice-txt">
                <div class="grid-cell">
                    <input type="checkbox" name="agree_terms" value="agree" id="agree_customer_terms"> I agree with the <a href="https://www.compliancetest.net/customer-tc/" target="_blank">Terms & Conditions</a>
                </div>
                <div class="clear"></div>
            </div>                
        </div>
        <?php
            wp_nonce_field('purchase_subscribe', '_organisation_nonce');
        ?>
        <div class="loading loading-with-text"><div><b>PROCESSING YOUR PAYMENT</b><span>Please wait...</span></div></div>
        <?php elseif($subscriptionType == 'free' || $subscriptionType == 'additional' || $subscriptionType == 'organisation'): ?>      
        <div class="popup-box-header radius6 noradiusbottom">Confirm Subscription</div>     
        <div class="popup-box-content grid-box-body">    
            <div class="field-row">
                <div class="grid-cell">
                    <input type="checkbox" name="agree_terms" value="agree" id="agree_customer_terms"> I agree with the <a href="https://www.compliancetest.net/customer-tc/" target="_blank">Terms & Conditions</a>
                </div>
                <div class="clear"></div>
            </div> 
        </div>     
        <?php if($subscriptionType == 'free'): ?>
            <input type="hidden" name="_paymentnonce" value="<?php echo wp_create_nonce('free_subscription'); ?>" />
        <?php elseif($subscriptionType == 'additional'): ?>
            <input type="hidden" name="_paymentnonce" value="<?php echo wp_create_nonce('additional_subscription'); ?>" />
        <?php elseif($subscriptionType == 'organisation'): ?>
            <input type="hidden" name="_paymentnonce" value="<?php echo wp_create_nonce('organisation_subscription'); ?>" />
        <?php endif; ?>
            
        <div class="loading loading-with-text"><div><b>PROCESSING SUBSCRIPTION</b><span>Please wait...</span></div></div>
        <?php endif; ?>
        
        
        <div class="popup-box-footer radius6 noradiustop">
            <a href="#" class="action-btn process-btn submit-btn"><span class="p"></span><span class="t">Submit</span></a>
            <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
            <div class="clear"></div>
        </div>
        <a class="close_btn"></a>                        
        <input type="hidden" name="suite_id" value="<?php echo $suite->id?>" />
    </form>
</div>
<script type="text/javascript">
jQuery(document).ready(function(){
    fixTdHeight(jQuery('#organisation_subscriptions'));
    //Fix Simple ToolTips
    jQuery('.td-status .simple_tooltip').each(function(){
        jQuery(this).css({'top': -1 * jQuery(this).outerHeight() - 6, 'margin-left': -1 * jQuery(this).outerWidth() / 2 + jQuery(this).parent().outerWidth() / 2});
    })
})
</script>
<?php
get_footer();
?>