<?php
/*
 * Template Name: My Test Suites
 */


if(!is_user_logged_in()){
    wp_redirect(home_url());
    exit;
}
get_header();
?>
<div class="content" id="my_testsuites">
    <div class="dashboard-tabs">
        <?php get_sidebar('dashboard'); ?>
    </div>
    <div class="container">
        <div class="column">
            <div class="grid-box table-box" id="my_subscriptions">
                <div class="grid-box-body">
                    <div class="thead tr">
                        <div class="td td-community">Community</div>
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
                           <div class="td td-full">You are currently not subscribed to any test suites.</div>
                           <div class="clear"></div>
                       </div> 
                   <?php
                       }else{
                           $prev_purchase_id = 0;
                           
                           foreach($subscriptions as $row)
                           {
                           $community_id = get_post_meta($row->suite_id, 'community_id', true);
                           $group = groups_get_group(array('group_id' => $community_id));
                   ?>
                        <div class="tr">
                            <div class="td td-community">
                                <a href="<?php echo bp_get_group_permalink($group)?>"><?php echo bp_get_group_name($group) ?></a>
                            </div>
                            <div class="td td-suite">
                                <a href="<?php echo get_permalink($row->suite_id)?>"><?php echo $row->suite_title ?></a>
                            </div>
                            <div class="td td-fee tocenter">
                              <?php if($prev_purchase_id == $row->purchase_id): ?>
                                <span class="has-tooltip">*<span class="simple_tooltip">Use of all versions of a test suite is covered by a single subscription fee<span></span></span></span>
                              <?php else: ?>
                                $<?php 
                                $monthlyFee = getSubscriptionMonthlyFee($row->id);                                
                                echo $monthlyFee;
                                ?>/month
                              <?php endif; ?>                                                                
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
                                <a href="/?cp-action=<?php echo wp_create_nonce('get-harness')?>&id=<?php echo $row->id?>" class="action-btn harness-detail-btn harness-detail-link has-tooltip" data-id="<?php echo $row->id?>" rel="custom-popup" cp-type="ajax" cp-removeBoxAfterClose=1><span class="p"></span><span class="simple_tooltip">Harness Details<span></span></span></a>
                                <a href="javascript: void(0)" class="action-btn unsubscribe-btn icon-btn left10 unsubscribe-link has-tooltip" data-status="<?php echo $row->status?>" data-id="<?php echo $row->id?>"><span class="p"></span><span class="simple_tooltip">Unsubscribe<span></span></span></a><br />                        
                            </div>
                            <div class="clear"></div>
                        </div>
                   <?php
                                $prev_purchase_id = $row->purchase_id;
                           }
                       }
                   ?>
                   <div class="loading1"></div>
                   </div>
                   
                </div>                
            </div>
            <div class="space10"></div>
            <a href="<?php echo home_url(); ?>/test-suites" class="action-btn add-new-btn has-tooltip">
                <span class="p"></span>
                <span class="t">Add</span>
                <span class="simple_tooltip radius6">Add Test Suite<span></span></span>
            </a>
            <div class="space20"></div>
        </div>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div> <!--end content-->
<?php render_unsubscription_popup(); ?>

<script type="text/javascript">
jQuery(document).ready(function(){
    fixTdHeight(jQuery('#my_subscriptions'));
    //Fix Simple ToolTips
    jQuery('.td-status .simple_tooltip').each(function(){
        jQuery(this).css({'top': -1 * jQuery(this).outerHeight() - 6, 'margin-left': -1 * jQuery(this).outerWidth() / 2 + jQuery(this).parent().outerWidth() / 2});
    })
})
</script>
<?php
get_footer();
?>
