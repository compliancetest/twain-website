<?php
/*
 * Template Name: Test Suite Coverage
 */
get_header();

//Getting The Suites that belonged to the Community 
$mysuites = getUserSubscribedSuites();

?>
<div class="content" id="test_suite_coverage">
	<div class="column fifth left nopaddingleft nopaddingright sidebar">
		<?php get_sidebar('dashboard'); ?>
	</div>
	<div class="four_fifths right container">
        <div class="column">
            <?php if(can_create_suite()){ ?>
            <a href="/add-new-test-suite" class="action-btn add-new-btn"><span class="p"></span><span class="t">Add New Test Suite</span></a>
            <div class="clear space15"></div>
            <?php } ?>
           <?php foreach($mysuites as $suite){ ?>
           <div class="grid-box table-box">
               <div class="grid-box-header">
                   <h5 class="left"><a href="<?php echo get_permalink($suite->suite_id)?>"><b><?php echo $suite->name?></b></a></h5>                   
                   <?php if(can_edit_suite($suite->suite_id)){ ?>
                   <a class="gbh-btn gbh-btn-edit right" href="/edit-test-suite?id=<?php echo $suite->suite_id?>">Edit<span class="simple_tooltip radius6">Edit<span></span></span></a>
                   <?php } ?>
                   <?php /*if(can_edit_suite($product->ID)){ ?>
                   <a class="gbh-btn gbh-btn-delete right" href="<?php get_permalink()?>?id=<?php echo $suite->suite_id?>&_wpnonce=<?php echo wp_create_nonce('delete-suite') ?>&return=<?php echo base64_encode(get_permalink()) ?>" onclick="return confirm('Are you sure that you want to delete this Test Suite?')">Edit<span class="simple_tooltip radius6">Delete<span></span></span></a>
                   <?php }*/ ?>
                   <div class="clear"></div>
               </div>
               <?php
                   $plans = getTestPlansBySuiteId($suite->suite_id);
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
                               <div class="coverage-progress">
                                   <span class=""></span>
                                   <span class=""></span>
                                   <span class=""></span>
                                   <span class=""></span>
                                   <span class=""></span>
                                   <span class=""></span>
                                   <span class=""></span>                                   
                                   <span class=""></span>                                   
                                   <span class=""></span>                                   
                                   <span class=""></span>                                   
                               </div>    
                               <div class="clear"></div>
                           </div>
                           <div class="td td-action">
                              <a href="/my-transaction-log" class="action-btn view-log-btn icon-btn"><span class="p"></span>
                                  <span class="simple_tooltip radius6">View Log<span></span></span>
                              </a>
                              <a href="<?php echo get_permalink()?>?_plannonce=<?php echo wp_create_nonce('edit-plan')?>&suite_id=<?php echo $suite->suite_id?>&id=<?php echo $crow->id?>" data-product-id="<?php echo $product->ID?>" cp-type="ajax" cp-removeBoxAfterClose=1 class="action-btn grey-edit-btn edit-plan-btn icon-btn"><span class="p"></span>
                                  <span class="simple_tooltip radius6">Edit Plan<span></span></span>
                              </a>
                              <a href="<?php echo get_permalink()?>?_plannonce=<?php echo wp_create_nonce('delete-plan')?>&id=<?php echo $crow->id?>" onclick="return confirm('Are you sure you want to delete this plan?')" class="action-btn grey-delete-btn icon-btn"><span class="p"></span>
                                  <span class="simple_tooltip radius6">Delete Plan<span></span></span>
                              </a>                              
                              <a href="<?php echo get_permalink()?>?_plannonce=<?php echo wp_create_nonce('certify-plan')?>&id=<?php echo $crow->id?>" class="action-btn certify-grey-btn icon-btn">
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
                   <a href="<?php echo get_permalink()?>?_plannonce=<?php echo wp_create_nonce('edit-plan')?>&suite_id=<?php echo $suite->suite_id?>" data-product-id="<?php echo $product->ID?>" cp-type="ajax" cp-removeBoxAfterClose=1 class="action-btn process-btn add-plan-btn"><span class="p"></span><span class="t">New Test Plan</span></a>
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
})
</script>
<?php
get_footer();
?>
