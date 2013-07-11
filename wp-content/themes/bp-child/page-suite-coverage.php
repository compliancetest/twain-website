<?php
/*
 * Template Name: Test Suite Coverage
 */
get_header();

//Getting The Suites that belonged to the Community 
$mysuites = getUserTestSuites();

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
                   <h5 class="left"><a href="<?php echo get_permalink($suite->ID)?>"><b><?php echo get_the_title($suite)?></b></a></h5>                   
                   <?php if(can_edit_suite($suite->ID)){ ?>
                   <a class="gbh-btn gbh-btn-edit right" href="/edit-test-suite?id=<?php echo $suite->ID?>">Edit<span class="simple_tooltip radius6">Edit<span></span></span></a>
                   <?php } ?>
                   <?php /*if(can_edit_suite($product->ID)){ ?>
                   <a class="gbh-btn gbh-btn-delete right" href="<?php get_permalink()?>?id=<?php echo $suite->ID?>&_wpnonce=<?php echo wp_create_nonce('delete-suite') ?>&return=<?php echo base64_encode(get_permalink()) ?>" onclick="return confirm('Are you sure that you want to delete this Test Suite?')">Edit<span class="simple_tooltip radius6">Delete<span></span></span></a>
                   <?php }*/ ?>
                   <div class="clear"></div>
               </div>
               <?php
                   $claims = getClaimsBySuiteId($suite->ID);
               ?>
               <div class="grid-box-body">
                   <div class="thead tr">
                       <div class="td td-product">Product</div>
                       <div class="td td-conflevel">Conf Level</div>
                       <div class="td td-coverage">Coverage</div>
                       <div class="td td-action">Action</div>
                       <div class="clear"></div>
                   </div>
                   <div class="tbody">
                   <?php if(!$claims) { ?>
                       <div class="tr">
                           <div class="td td-full">No test transactions recorded yet</div>
                           <div class="clear"></div>
                       </div>
                   <?php }else{ ?>
                       <?php foreach($claims as $crow){ ?>
                       <div class="tr">
                           <div class="td td-product"><?php echo $crow->product_name ?></div>
                           <div class="td td-conflevel"><?php echo $crow->conformance_level ?></div>
                           <div class="td td-coverage">Coverage</div>
                           <div class="td td-action">
                              <a href="/my-transaction-log" class="action-btn view-log-btn"><span class="p"></span><span class="t">View Log</span></a>
                              <a href="#" class="action-btn certify-grey-btn"><span class="p"></span><span class="t">Certify</span></a>
                           </div>
                           <div class="clear"></div>
                       </div>
                       <?php } ?>
                   <?php } ?>
                   </div>
               </div>
           </div>           
           <div class="space20"></div>   
           <?php } ?>
        </div>           
    </div>
	<div class="clear"></div>
			
</div> <!--end content-->

<?php
get_footer();
?>
