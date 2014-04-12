<?php
/*
 * Template Name: Test Suite Coverage
 */
if(!is_user_logged_in()){
    wp_redirect(home_url());
    exit;
} 

get_header();

//Getting The Suites that belonged to the Community 
$mysuites = getUserSubscribedSuites();

$esb = new ManageESB();
?>
<div class="content" id="test_suite_coverage">
	<div class="dashboard-tabs">
        <?php get_sidebar('dashboard'); ?>
    </div>
	<div class="container">
        <div class="column">            
            <div class="page-description">
            <?php if (have_posts()) while (have_posts()) : the_post(); 
                the_content();
                endwhile;
            ?>
            </div>
           <?php foreach($mysuites as $suite){ ?>
           <?php
               $caseStatus = $esb->getCaseStatus($suite->id, $suite->suite_id);
               
           ?>
           <div class="grid-box table-box">
               <div class="grid-box-header">
                   <h5 class="left"><a href="<?php echo get_permalink($suite->suite_id)?>"><b><?php echo $suite->name?></b></a></h5>                   
                   <?php if(can_edit_suite($suite->suite_id)){ ?>
                   <a class="gbh-btn gbh-btn-edit right" href="/edit-test-suite?id=<?php echo $suite->suite_id?>">Edit<span class="simple_tooltip radius6">Edit<span></span></span></a>
                   <?php } ?>
                   <div class="clear"></div>
               </div>
               <?php
                   $plans = getTestPlansBySuiteId($suite->suite_id, get_current_user_id());
               ?>
               <div class="grid-box-body">
                   <div class="thead tr">
                       <div class="td td-product">Product</div>
                       <div class="td td-conflevel">Level</div>
                       <div class="td td-role">Role</div>
                       <div class="td td-coverage">Coverage</div>
                       <div class="td td-action">Action</div>
                       <div class="clear"></div>
                   </div>
                   <div class="tbody">
                   <?php if(!$plans) { ?>
                       <div class="tr">
                           <div class="td td-full">No test transactions recorded yet</div>
                           <div class="clear"></div>
                       </div>
                   <?php }else{ ?>
                       <?php foreach($plans as $crow){ ?>
                       <div class="tr">
                           <div class="td td-product"><?php echo $crow->product_name ?></div>
                           <div class="td td-conflevel"><?php echo implode(cp_explode($crow->level), ", ") ?></div>
                           <div class="td td-role"><?php echo implode(cp_explode($crow->role), ", ")?></div>
                           <div class="td td-coverage">
                               <?php
                                   $suiteObj = new TestSuite($suite->suite_id);
                                   $testCases = $suiteObj->loadTestCases(cp_explode($crow->level), cp_explode($crow->role));                                   
                                   
                               ?> 
                               <div class="coverage-progress">
                                   <?php
                                       $passedBlobs = "";
                                       $failedBlobs = "";
                                       $normalBlobs = "";
                                       
                                       foreach($testCases as $case)
                                       {
                                           $tooltip = '<span class="simple_tooltip radius6"><a href="' . get_permalink($case->ID) . '">' . $case->post_title . '</a> | <a href="' . get_site_url() . "/my-transaction-log?case=" . $case->ID .'">View Test Log</a><span></span></span>';
                                           if(isset($caseStatus[$suite->suite_id][$crow->product_id][$case->ID])) 
                                           {
                                               if($caseStatus[$suite->suite_id][$crow->product_id][$case->ID] == 'pass')
                                               {
                                                   $passedBlobs .= '<span class="bubble ' . $caseStatus[$suite->suite_id][$crow->product_id][$case->ID] . '">' . $tooltip . '</span>';
                                               }else{
                                                   $failedBlobs .= '<span class="bubble ' . $caseStatus[$suite->suite_id][$crow->product_id][$case->ID] . '">' . $tooltip . '</span>';
                                               }
                                               
                                           }else{
                                               $normalBlobs .= '<span class="bubble">' . $tooltip . '</span>';
                                           }
                                       }
                                       echo $passedBlobs . $failedBlobs . $normalBlobs;
                                   ?>
                               </div>    
                               <div class="clear"></div>
                           </div>
                           <div class="td td-action">
                              <a href="/my-transaction-log?suite=<?php echo $suite->suite_id?>&product=<?php echo $crow->product_id?>" class="action-btn view-log-btn icon-btn"><span class="p"></span>
                                  <span class="simple_tooltip radius6">View Log<span></span></span>
                              </a>
                              <a href="<?php echo get_permalink()?>?_plannonce=<?php echo wp_create_nonce('edit-plan')?>&suite_id=<?php echo $suite->suite_id?>&id=<?php echo $crow->id?>" data-product-id="<?php echo $product->ID?>" cp-type="ajax" cp-removeBoxAfterClose=1 class="action-btn edit-btn edit-plan-btn icon-btn"><span class="p"></span>
                                  <span class="simple_tooltip radius6">Edit Plan<span></span></span>
                              </a>
                              <a href="<?php echo get_permalink()?>?_plannonce=<?php echo wp_create_nonce('delete-plan')?>&id=<?php echo $crow->id?>" onclick="return confirm('Are you sure you want to delete this plan?')" class="action-btn delete-btn icon-btn left5"><span class="p"></span>
                                  <span class="simple_tooltip radius6">Delete Plan<span></span></span>
                              </a>                              
                              <a href="<?php echo get_permalink()?>?_plannonce=<?php echo wp_create_nonce('certify-plan')?>&id=<?php echo $crow->id?>" class="action-btn certify-grey-btn icon-btn left5">
                                  <span class="p"></span>
                                  <span class="simple_tooltip radius6">Certify<span></span></span>
                              </a>
                           </div>
                           <div class="clear"></div>
                       </div>
                       <?php } ?>
                   <?php } ?>
                   </div>
                   <?php if(is_customer($suite->suite_id)){ ?>
                   <a href="<?php echo get_permalink()?>?_plannonce=<?php echo wp_create_nonce('edit-plan')?>&suite_id=<?php echo $suite->suite_id?>" data-product-id="<?php echo $product->ID?>" cp-type="ajax" cp-removeBoxAfterClose=1 class="action-btn add-new-btn add-plan-btn"><span class="p"></span><span class="t">New Test Plan</span></a>
                   <?php } ?>
               </div>
           </div>           
           <div class="clear"></div>
           <div class="space20"></div>   
           <?php } ?>
        </div>           
    </div>
	<div class="clear"></div>
			
</div> <!--end content-->
<script type="text/javascript">
jQuery(document).ready(function(){
    jQuery('#test_suite_coverage .grid-box-body .tbody').each(function(){
        jQuery(this).find('.tr').each(function(){
            var h = Math.max(
                jQuery(this).find('.td:eq(0)').outerHeight(),
                jQuery(this).find('.td:eq(1)').outerHeight(),
                jQuery(this).find('.td:eq(2)').outerHeight(),
                jQuery(this).find('.td:eq(3)').outerHeight()
            );
            jQuery(this).find('.td:lt(4)').height(h - 16);
            jQuery(this).find('.td:eq(4)').height(h - 6);
        })
    });    
    
    jQuery('.add-plan-btn, .edit-plan-btn').cplightbox({
        onLoad: function(){
            jQuery('#make-plan-box .process-btn').click(function(){
                makePlan();
                return false;
            });
            
        }
    });
    
    //Make Plan
    function makePlan()
    {
        var form = jQuery('#makePlanForm');
        form.find('.message').remove();
        if(!form.find('#product_id').val() || form.find('.level:checked').length == 0 || form.find('.role:checked').length == 0)
        {
            form.find('.popup-box-content').append('<div class="message error">Please complete all fields in the form.</div>');    
            return false;
        }
        form.find('.loading1').show();
        
        form.submit();
    }
    
    //Show Bubble Tooltip
    jQuery('.coverage-progress .bubble').hover(function(){
        jQuery(this).find('.simple_tooltip').css('left', -jQuery(this).find('.simple_tooltip').outerWidth() / 2 + 11);
        jQuery(this).find('.simple_tooltip').show();
    }, function(){
        jQuery(this).find('.simple_tooltip').hide();
    })
})
</script>
<?php
get_footer();
?>
