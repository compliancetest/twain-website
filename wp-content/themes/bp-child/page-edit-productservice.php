<?php
/**
* Template Name:Add/Edit Product&Service
*/


$psID = isset($_GET['id']) ? $_GET['id'] : null;

if( ($psID != null && !can_edit_product_and_service($psID)) || ($psID == null && !can_create_product_and_service()) )
{
    wp_redirect("/");
    exit;
}

$isNew = true;

$product = new ProductAndService($psID);
$product ->load();
if(!$product ->id)
    $isNew = true;
else
    $isNew = false;

get_header();

?>
<div class="content edit-item-wrapper" id="edit_product_service_wrapper">
    <div class="space25"></div>
    <div class="column fifth left nopaddingleft nopaddingright sidebar">
        <?php get_sidebar('dashboard'); ?>
    </div>        
    <div class="column four_fifths right container"> 
      <form name="suiteForm" id="suiteForm" action="" method="post" enctype="multipart/form-data">
        <?php if($isNew){ ?>
        <h2>Add New Product and Service</h2>
        <?php }else{ ?>
        <h2>Edit Product and Service: <?php $product->name ?></h2>
        <?php } ?> 
        <div class="grid-box grid-box-expandable grid-box-opened" id="ps-info-box">
           <div class="grid-box-header">
               <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
               <h5 class="left">Information</h5>
               <div class="clear"></div>
           </div>
           <div class="grid-box-body">
               <div class="column">
                   <div class="field-row">
                       <div class="grid-cell">
                           <label>Name</label>                    
                           <input type="text" class="input" name="product_name" id="product_name" value="<?php echo $product->name?>" />
                       </div>                       
                       <div class="grid-cell">
                           <label>Date</label>                    
                           <input type="text" class="input datepicker" name="product_date" id="product_date" value="<?php echo $product->date?>" />
                       </div>
                       <div class="grid-cell radio-cell" id="ps-type-cell">
                           <label>Type</label>                    
                           <input type="radio" name="product_type" id="product_type_software" value="Software" <?php echo $product->type == 'Software' ? 'checked="checked"' : ''?> /> Software
                           <input type="radio" name="product_type" id="product_type_product" value="Product" <?php echo $product->type == 'Product' ? 'checked="checked"' : ''?> /> Product
                           <input type="radio" name="product_type" id="product_type_process" value="Process" <?php echo $product->type == 'Process' ? 'checked="checked"' : ''?> /> Process
                           <input type="radio" name="product_type" id="product_type_service" value="Service" <?php echo $product->type == 'Service' ? 'checked="checked"' : ''?> /> Service
                           
                       </div>
                       <div class="clear"></div>
                   </div>
                   <div class="field-row">
                       <div class="grid-cell">
                           <label>Owner</label>                    
                           <input type="text" class="input" name="product_owner" id="product_owner" value="<?php echo $product->owner?>" />
                       </div>                       
                       <div class="grid-cell">
                           <label>Access URL</label>                    
                           <input type="text" class="input" name="product_url" id="product_url" value="<?php echo $product->accessURL?>" />
                       </div>
                       <div class="grid-cell radio-cell">
                           <label>Status</label>                    
                           <input type="radio" name="product_status" id="product_staus_active" value="Active" <?php echo $product->status == 'Active' ? 'checked="checked"' : ''?> /> Active
                           <input type="radio" name="product_status" id="product_staus_on_hold" value="On Hold"  <?php echo $product->status == 'On Hold' ? 'checked="checked"' : ''?> /> On Hold                           
                       </div>
                       <div class="clear"></div>
                   </div>
                   <div class="field-row">
                       <div class="grid-cell">
                            <label>Description</label>
                            <textarea cols="" rows="" class="textarea" name="product_description"><?php echo $product->descrition?></textarea>
                       </div>
                       <div class="clear"></div>
                   </div>
               </div>
           </div>
        </div>     
        <div class="space20"></div>
        <div class="grid-box grid-box-expandable grid-box-opened" id="ps-related-box">
           <div class="grid-box-header">
               <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
               <h5 class="left">Select Related Products / Services</h5>
               <div class="clear"></div>
           </div>
           <div class="grid-box-body">
               <div class="column">                   
                   <div class="field-row">
                       <div class="grid-cell checkbox-cell">
                       <?php $availableProducts = $product->getAvailableProducts(); ?>
                       <?php foreach($availableProducts as $row){ ?>
                            <label><input type="checkbox" name="related_products[]" value="<?php echo $row->ID?>"  <?php echo in_array($row->ID, $product->relatedProducts) ? 'checked="checked"' : ''?> /> <?php echo get_the_title($row)?></label>
                       <?php } ?>
                       </div>
                       <div class="clear"></div>
                   </div>
               </div>
           </div>
        </div>
        <div class="space20"></div>
        <div class="grid-box grid-box-expandable grid-box-opened" id="ps-certifications-box">
           <div class="grid-box-header">
               <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
               <h5 class="left">Select Certifications(Test Suites)</h5>
               <div class="clear"></div>
           </div>
           <div class="grid-box-body">
               <div class="column">                   
                   <div class="field-row">
                       <div class="grid-cell checkbox-cell">
                       <?php $availableSuites = getUserTestSuites(); ?>
                       <?php foreach($availableSuites as $row){ ?>
                            <label><input type="checkbox" name="test_suites[]" value="<?php echo $row->ID?>"  <?php echo in_array($row->ID, $product->certifications) ? 'checked="checked"' : ''?> /> <?php echo get_the_title($row)?></label>
                       <?php } ?>   
                       </div>
                       <div class="clear"></div>
                   </div>
               </div>
           </div>
        </div>    
        <div class="grid-box">
           <div class="grid-box-footer nobackground noshadow">
               <div class="btn-row nopaddingright">
                   <a href="#" class="action-btn process-btn submit-btn left15"><span class="p"></span><span class="t">SAVE PRODUCT/SERVICE</span></a>
                   <a href="javascript: history.go(-1)" class="action-btn cancel-btn"><span class="p"></span><span class="t">Cancel</span></a>
                   <div class="clear"></div>
               </div>
           </div>
       </div>
       <input type="hidden" name="id" value="<?php echo $product->id?>" />
       <input type="hidden" name="action" value="cp-suite-save" />
       <?php
           wp_nonce_field('save-product-service');
       ?>
      </form>
    </div>
    <div class="clear"></div>
</div>
<?php

get_footer();