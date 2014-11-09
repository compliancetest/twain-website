<?php
/*
 * Template Name: Organisation Test Suites
 */

if (!($organisation_id = ct_is_organisation_admin())) {    
    wp_redirect(home_url());
    exit;
}

$organisationClass = new CT_Organisation($organisation_id);
wp_enqueue_script( 'plans-moving', get_stylesheet_directory_uri() . '/js/pricing-plans-moving.js', array('jquery'), '0.0.1');
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
                            <h5>Subscriptions</h5>
                            <div class="clear"></div>
                        </div>
                        <div class="grid-box-body">
                            <div class="thead tr">
                               <div class="td td-community">Community</div>
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
                                    <div class="td td-community">
                                        <?php echo $row->community_name ?>
                                    </div>
                                    <div class="td td-suite">
                                        <?php echo $row->suite_title ?>
                                    </div>
                                    <div class="td td-nickname">
                                        <?php echo stripslashes( $row->nickname ); ?>
                                    </div>
                                    <div class="td td-assignee">
                                        <?php echo $row->full_name; ?>
                                        <?php echo ($row->user_email)?('<br/>('.$row->user_email.')'):(''); ?>
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
                                        <a href="<?php echo get_permalink($row->suite_id)?>?_organisation_nonce=<?php echo wp_create_nonce('organisation-unsubscribe')?>&id=<?php echo $row->id?>&return=<?php echo base64_encode(get_permalink()) ?>" class="action-btn unsubscribe-btn icon-btn left10 has-tooltip" rel="custom-popup" cp-type="ajax" cp-removeBoxAfterClose=1><span class="p"></span><span class="simple_tooltip">Cancel Subscription<span></span></span></a>
                                        <a href="<?php echo the_permalink() ?>?_organisation_nonce=<?php echo wp_create_nonce('get_price_plan') ?>&suite_id=<?php echo $row->suite_family_mark;?>&plan_id=<?php echo $row->pricing_plan_id;?><?php if( $row->voucher ):?>&voucher=<?php echo $row->voucher;?><?php endif;?>" class="action-btn harness-detail-btn harness-detail-link has-tooltip left10" data-id="26" rel="custom-popup" cp-type="ajax" cp-removeboxafterclose="1" cp-closewhenclickoveraly="0">
                                            <span class="p"></span>
                                            <span class="simple_tooltip" style="top: -27px;">Pricing Plan Details<span></span></span>
                                        </a>
                                        <a href="#_organisation_nonce=<?php echo wp_create_nonce('edit-subscription')?>&id=<?php echo $row->id?>" class="action-btn edit-btn icon-btn left10 edit-link has-tooltip edit_sub_<?php echo $row->id?>" cp-type="ajax" cp-closeWhenClickOveraly=0 rel="custom-popup" cp-removeBoxAfterClose=1><span class="p"></span><span class="simple_tooltip">Edit Subscription<span></span></span></a>
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
                    <a href="#subscribe-box" cp-closeWhenClickOveraly=0 rel="custom-popup" cp-type="inline"  class="action-btn process-btn add-new-btn top10 has-tooltip" id="purchase-subscribe"><span class="p"></span><span class="t">Add</span><span class="simple_tooltip"><span></span>Purchase A Subscription</span></a>
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
    <form name="paymentForm" id="paymentForm" action="" method="post">
        <div class="popup-box-header radius6 noradiusbottom">Purchase A Subscription</div>        
        <div class="popup-box-content grid-box-body">    
            <div class="field-row">
                <div class="grid-cell">
                    <label>Community</label>
                    <select name="community_id" id="community_id" class="select">                        
                        <?php $communities = groups_get_groups(); ?>
                        <?php foreach($communities['groups'] as $group){ ?>
                        <option value="<?php echo $group->id?>">
                            <?php echo bp_get_group_name($group) ?>
                        </option>
                        <?php } ?>
                    </select>                    
                </div>
                <div class="clear"></div>
            </div>            
            <div class="field-row">
                <div class="grid-cell">
                    <label>Test Suite</label>
                    <select name="suite_family_mark" id="suite_family_mark" class="select">
                        <option value="">Select a Test Suite</option>                        
                    </select>                    
                </div>
                <div class="clear"></div>
            </div>            
            <div class="field-row">
                <div class="grid-cell">
                    <label>Payment Method</label>
                    <select name="payment_method" id="payment_method" class="select">
                        <option value="">Select a Method</option>
                        <?php foreach($organisationClass->get_payment_methods() as $row){ ?>
                        <option value="<?php echo $row->id?>" <?php echo ($row->is_default=='1')?('selected="selected"'):(''); ?>>
                            <?php 
                                echo $row->nickname;
                                if (!$row->invoice_me)
                                    echo " " . chunk_split(encrypt_card_number($row->card_number), 4)?>
                        </option>
                        <?php } ?>
                    </select>
                    <a href="/my-organisation" class="left15">Add payment method</a>
                </div>
                <div class="clear"></div>
            </div>
            <div class="field-row">
                <div class="grid-cell">
                    <label>Subscription Nickname</label>
                    <input type="text" name="nickname" id="nickname" value="" class="input" maxlength="50" />                    
                </div>                
                <div class="clear"></div>
            </div>
            <div class="field-row">
                <div class="grid-cell">
                    <label>Pricing Plan</label>
                    <select id="pricing_plan_id_span"></select>
                    <input type="hidden" id="pricing_plan_id" name="pricing_plan_id" value="">
                    <a href="<?php echo the_permalink() ?>?_organisation_nonce=<?php echo wp_create_nonce('get_price_plan') ?>&suite_id=1" class="submit_all" rel="custom-popup" cp-type="ajax" cp-closeWhenClickOveraly=0 cp-removeBoxAfterClose=1><span class="p"></span><span class="t">Select Pricing Plan</span></a>
                    <script>
                        jQuery(document).ready(function($){
                            $('#suite_family_mark').on('change', function(e){
                                if( jQuery('.submit_all').attr('href').indexOf( 'suite_id' ) == -1 ){
                                    var new_url = jQuery('.submit_all').attr('href') + '&suite_id='+ $('#suite_family_mark').val();
                                } else {
                                    var new_url = jQuery('.submit_all').attr('href').split('&suite_id');
                                    new_url = new_url[0] + '&suite_id='+ $('#suite_family_mark').val();

                                }
                                addSelectValues( new_url );
                                jQuery('.submit_all').attr( 'href', new_url );
                                jQuery(".submit_all").off("click").cplightbox( {'href': new_url});
                                $('#pricing_plan_id_span').on('change', function(){
                                    $('#pricing_plan_id').val( $(this).val());

                                    if( jQuery('.submit_all').attr('href').indexOf( 'suite_id' ) == -1 ){
                                        var new_url = jQuery('.submit_all').attr('href') + '&suite_id='+ $('#suite_family_mark').val();
                                    } else {
                                        var new_url = jQuery('.submit_all').attr('href').split('&suite_id');
                                        new_url = new_url[0] + '&suite_id='+ $('#suite_family_mark').val();
                                    }
                                    new_url = new_url+'&pricing_plan_id='+jQuery('#pricing_plan_id').val();
                                    jQuery('.submit_all').attr( 'href', new_url );
                                    jQuery(".submit_all").off("click").cplightbox( {'href': new_url});
                                });
                                function addSelectValues( url ){
                                    $.ajax({
                                        url: new_url+'&get_all=1',
                                        type: 'get',
                                        dataType: 'json',
                                        success: function(data){
                                            $("#pricing_plan_id_span").prepend("<option value='' selected='selected'>Select pricing plan</option>");
                                            $.each(data, function(i, value) {
                                                $('#pricing_plan_id_span').append($('<option>').text(value).attr('value', i));
                                            });
                                        }
                                    })
                                }
                            });
                        });
                    </script>
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
        <div class="loading loading-with-text"><div><b>PROCESSING YOUR SUBSCRIPTION</b><span>Please wait...</span></div></div>

        <div class="popup-box-footer radius6 noradiustop">
            <a href="#" class="action-btn process-btn submit-btn"><span class="p"></span><span class="t">Submit</span></a>
            <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
            <div class="clear"></div>
        </div>
        <a class="close_btn"></a>                        
        <input type="hidden" name="suite_id" value="<?php echo $suite->id?>" />
        <select id="all_family_mark" style="display: none;">
            <?php $test_suites = ct_get_test_suites_without_version(); ?>
            <?php foreach($test_suites as $row){ ?>
            <?php
                $s = new TestSuite( $row->family_mark );
                $s->load();
            ?>
            <?php if(! empty( $s->test_suite_plans ) ):?>
                <option value="<?php echo $row->family_mark?>" community-id="<?php echo get_post_meta($row->suite_id, 'community_id', true) ?>">
                    <?php echo $row->suite_title ?>
                </option>
            <?php endif;?>
            <?php } ?>
        </select>
    </form>
</div>
<script type="text/javascript">
jQuery(document).ready(function(){
    fixTdHeight(jQuery('#organisation_subscriptions'));
    //Fix Simple ToolTips
    jQuery('.td-status .simple_tooltip').each(function(){
        jQuery(this).css({'top': -1 * jQuery(this).outerHeight() - 6, 'margin-left': -1 * jQuery(this).outerWidth() / 2 + jQuery(this).parent().outerWidth() / 2});
    })
    
    jQuery('#paymentForm').submit(function(){
        jQuery('#subscribe-box .message').remove();
        jQuery('#subscribe-box .input-error').removeClass('input-error');
        jQuery('#subscribe-box .select-error').removeClass('select-error');
        
        var isValid = true;
        
        if(jQuery('#paymentForm #payment_method').val() == '')
        {
            jQuery('#paymentForm #payment_method').addClass('select-error');
            isValid = false;            
        }
        
        if(jQuery('#paymentForm #suite_family_mark').val() == '')
        {
            jQuery('#paymentForm #suite_family_mark').addClass('select-error');
            isValid = false;            
        }
        
        if(jQuery('#paymentForm #nickname').val() == '')
        {
            jQuery('#paymentForm #nickname').addClass('input-error');
            isValid = false;            
        }
        if(jQuery('#pricing_plan_id_span').val() == '')
        {
            jQuery('#pricing_plan_id_span').addClass('input-error');
            isValid = false;
        }
        
        if(!isValid)
        {
            jQuery('#subscribe-box .popup-box-footer').prepend('<div class="message error">Please complete fields in red.</div>');
            return false;
        }
        
        //Check Terms and condition
        if(!jQuery('#subscribe-box #agree_customer_terms').prop('checked'))
        {
            jQuery('#subscribe-box .popup-box-footer').prepend('<div class="message error">You must agree to our Terms & Conditions.</div>');
            return false;
        }
        
        jQuery('#subscribe-box .loading').show();
        
        return isValid;
    })
    
    //Edit Subscription
    jQuery('body').on('submit', '#updateSubscriptionForm', function(){
        var isValid = true;
        jQuery('#update-subscription-box .message').remove();
        jQuery('#update-subscription-box .input-error').removeClass('input-error');
        
        if (jQuery('#updateSubscriptionForm #nickname').val() == '') {
            jQuery('#updateSubscriptionForm #nickname').addClass('input-error');            
            isValid = false;            
        }
        
        if (!isValid) {
            jQuery('#update-subscription-box .popup-box-footer').prepend('<div class="message error">Please complete fields in red.</div>');
            return false;
        }
        
        jQuery('#update-subscription-box .loading').show();
        
        return isValid;
    })
    
    function filter_test_suites()
    {
        jQuery('#subscribe-box #suite_family_mark').val('');
        
        jQuery('#subscribe-box #suite_family_mark option:gt(0)').remove();
        
        jQuery('#subscribe-box #all_family_mark option').each(function(){
            if(jQuery(this).attr('community-id') == jQuery('#subscribe-box #community_id').val())
                jQuery('#subscribe-box #suite_family_mark').append(jQuery(this).clone());
        })
        
    }
    filter_test_suites();
    jQuery('#subscribe-box #community_id').change(function(){
        filter_test_suites();
    })
})
</script>
<?php
get_footer();
?>