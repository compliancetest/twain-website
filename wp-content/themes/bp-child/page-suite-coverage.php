<?php
/*
 * Template Name: Test Suite Coverage
 */
get_header();
?>

<div class="content" id="test_suite_coverage">
	<div class="space25"></div>
	<div class="column fifth left nopaddingleft nopaddingright sidebar">
		<?php get_sidebar('dashboard'); ?>
	</div>
		
	<div class="four_fifths right container">
        <div class="column">
           <div class="grid-box table-box">
               <div class="grid-box-header">
                   <h5><b>SuperStream MCS v1.1</b></h5>                   
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
                           <div class="td td-product">Product</div>
                           <div class="td td-conflevel">Conf Level</div>
                           <div class="td td-coverage">
                               <div class="coverage-progress"><span class="bar0"></span></div>    
                           </div>
                           <div class="td td-action">
                              <a href="#" class="action-btn view-log-btn"><span class="p"></span><span class="t">View Log</span></a>
                              <a href="#" class="action-btn certify-btn"><span class="p"></span><span class="t">Certify</span></a>
                           </div>
                           <div class="clear"></div>
                       </div>
                       <div class="tr">
                           <div class="td td-product">Product</div>
                           <div class="td td-conflevel">Conf Level</div>
                           <div class="td td-coverage">Coverage</div>
                           <div class="td td-action">
                              <a href="#" class="action-btn view-log-btn"><span class="p"></span><span class="t">View Log</span></a>
                              <a href="#" class="action-btn certify-grey-btn"><span class="p"></span><span class="t">Certify</span></a>
                           </div>
                           <div class="clear"></div>
                       </div>
                   </div>
               </div>
           </div>
           <div class="space20"></div>   
           <div class="grid-box table-box">
               <div class="grid-box-header">
                   <h5><b>SuperStream MCS v1.1</b></h5>                   
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
        </div>           
    </div>
	<div class="clear"></div>
			
</div> <!--end content-->

<?php
get_footer();
?>
