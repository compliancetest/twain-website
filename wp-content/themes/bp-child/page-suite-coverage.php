<?php
/*
 * Template Name: Test Suite Coverage
 */
get_header();

//Getting The Suites that belonged to the Community 
$mysuites = getUserTestSuites();

?>
<div class="content" id="test_suite_coverage">
	<div class="space25"></div>
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
                   <h5><a href="<?php echo get_permalink($suite->ID)?>"><b><?php echo get_the_title($suite)?></b></a></h5>                   
                   <div class="clear"></div>
               </div>
               <div class="grid-box-body">
                   <div class="thead tr">
                       <div class="td td-product">Product</div>
                       <div class="td td-conflevel">Conf Level</div>
                       <div class="td td-coverage">Coverage</div>
                       <div class="td td-action">Action</div>
                       <div class="clear"></div>
                   </div>
                   <div class="tbody">
                       <div class="tr">
                           <div class="td td-full">No test transactions recorded yet</div>
                           <div class="clear"></div>
                       </div>
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
