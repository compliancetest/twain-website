<?php
/**
* Template Name:Add/Edit Product&Service
*/


$psID = isset($_GET['id']) ? $_GET['id'] : null;

if( ($psID != null && !can_edit_product_and_service($psID)) || ($psID == null && !can_create_product_and_service()) )
{
    if(!$psID)
        addMessage('Sorry, you are not allowed to create a Product / Service. Only community admin can create new one.', 'error');
    else
        addMessage('Sorry, you are not allowed to edit the Product / Service', 'error');
        
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

$myProducts = getUserProductsAndServices(null, $isNew ? array() : array($psID));

?>
<div class="content edit-item-wrapper" id="edit_product_service_wrapper">
    <div class="space25"></div>
    <div class="column fifth left nopaddingleft nopaddingright sidebar">
        <?php get_sidebar('dashboard'); ?>
    </div>        
    <div class="column four_fifths right container"> 
      <form name="psForm" id="psForm" action="" method="post" enctype="multipart/form-data">
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
                       <div class="grid-cell has-focus-tooltip">
                           <label>Name:</label>         
                           <span class="has-tooltip">
                               <input type="text" class="input" name="product_name" id="product_name" value="<?php echo $product->name?>" />
                               <span class="focus-tooltip"><span></span>Some Explanatory Text Goes right here. For each focus click on the  input area the explanatory text will pop-up like this one!</span>
                           </span>
                       </div>                       
                       <div class="grid-cell">
                           <label>Release Date:</label>                    
                           <input type="text" class="input datepicker" name="product_release_date" id="product_release_date" value="<?php echo $product->release_date?>" />
                       </div>
                       <div class="grid-cell radio-cell" id="ps-type-cell">
                           <label>Type:</label>                    
                           <input type="radio" name="product_type" id="product_type_software" value="Software Product" <?php echo $product->type == 'Software Product' ? 'checked="checked"' : ''?> /> Software Product
                           <input type="radio" name="product_type" id="product_type_product" value="Web Service" <?php echo $product->type == 'Web Service' ? 'checked="checked"' : ''?> /> Web Service
                           
                       </div>
                       <div class="clear"></div>
                   </div>
                   <div class="field-row">
                       <div class="grid-cell">
                           <label>Version:</label>                    
                           <input type="text" class="input" name="product_version" id="product_version" value="<?php echo $product->version?>" />
                       </div>                   
                       <div class="grid-cell">
                           <label>Access URL:</label>                    
                           <input type="text" class="input medium-input" name="product_url" id="product_url" value="<?php echo $product->accessURL?>" />
                       </div>      
                       <div class="clear"></div>
                   </div>          
                   <div class="field-row">
                       <div class="grid-cell">
                            <label>Description:</label>
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
                 <?php if($myProducts){ ?>
                   <?php foreach($product->relatedProducts as $row){ ?>
                   <div class="field-row">
                       <div class="grid-cell radio-cell width55P">
                           <label>Related Product: </label>
                           <select class="combobox select" name="related-product[]">
                               <option value=""></option>
                               <?php foreach($myProducts as $p){ ?>
                               <option value="<?php echo $p->ID?>" <?php echo $p->ID == $row->related_product_id ? 'selected="selected"' : '' ?>><?php echo get_post_meta($p->ID, 'product_name', true)?></option>
                               <?php } ?>
                           </select>
                       </div>
                       <div class="grid-cell width30P">
                           <label>Relation Ship: </label>
                           <select class="select" name="related-product-relation[]">
                               <option value="Depends On" <?php echo $row->relationship == 'Depends On' ? 'selected="selected"' : '' ?>>Depends On</option>
                               <option value="Newer Version Of" <?php echo $row->relationship == 'Newer Version Of' ? 'selected="selected"' : '' ?>>Newer Version Of</option>
                           </select>
                       </div>
                       <div class="grid-cell right">
                           <label>&nbsp;</label>
                           <a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>
                       </div>
                       <div class="clear"></div>
                   </div>
                   <?php } ?>
                   <?php if($isNew){ ?>
                   <div class="field-row new-row">
                       <div class="grid-cell width55P">
                           <label>Related Product: </label>
                           <select class="combobox select" name="related-product[]">
                               <option value=""></option>
                               <?php foreach($myProducts as $p){ ?>
                               <option value="<?php echo $p->ID?>"><?php echo get_post_meta($p->ID, 'product_name', true)?></option>
                               <?php } ?>
                           </select>
                       </div>
                       <div class="grid-cell width30P">
                           <label>Relation Ship: </label>
                           <select class="select" name="related-product-relation[]">
                               <option value="Depends On">Depends On</option>
                               <option value="Newer Version Of">Newer Version Of</option>
                           </select>
                       </div>
                       <div class="grid-cell right">
                           <label>&nbsp;</label>
                           <a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>
                       </div>
                       <div class="clear"></div>
                   </div>
                   <?php } ?>
                   <div class="btn-row">
                       <a href="#" class="action-btn add-new-btn" id="add-related-product"><span class="p"></span><span class="t">Add Related Product</span></a>
                       <div class="clear"></div>
                   </div>
                 <?php }else{ ?>
                   <div class="field-row noborderbottom">
                       <div class="grid-cell width100P">
                           No Product/Service Found!
                       </div>
                   </div>
                 <?php }   ?>
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
       <?php
           wp_nonce_field('save-product-service');
       ?>
      </form>
    </div>
    <div class="clear"></div>
</div>
<script type="text/javascript">
jQuery(document).ready(function(){
    jQuery('#add-related-product').click(function(){
        jQuery('#ps-related-box .btn-row').before('<div class="field-row new-row">' + 
                       '<div class="grid-cell width55P">' +
                           '<label>Related Product: </label>' +
                           '<select class="combobox select" name="related-product[]">' +
                               '<option value=""></option>' +
                               <?php foreach($myProducts as $p){ ?>
                               '<option value="<?php echo $p->ID?>"><?php echo get_post_meta($p->ID, 'product_name', true)?></option>' +
                               <?php } ?>
                           '</select>' + 
                       '</div>' + 
                       '<div class="grid-cell width30P">' +
                           '<label>Relation Ship: </label>' +
                           '<select class="select" name="related-product-relation[]">' +
                               '<option value="Depends On">Depends On</option>' +
                               '<option value="Newer Version Of">Newer Version Of</option>' +
                           '</select>' +
                       '</div>' +
                       '<div class="grid-cell right">' +
                           '<label>&nbsp;</label>' +
                           '<a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>' +
                       '</div>' +
                       '<div class="clear"></div>' +
                   '</div>');
        jQuery('#ps-related-box .combobox:last').combobox();
        return false;
    });
    jQuery('#ps-related-box').on('click', '.blue-delete-btn', function(){
        //if(jQuery(this).parents('.field-row').hasClass('new-row'))
//        {
            jQuery(this).parents('.field-row').fadeOut('fast', function(){
                jQuery(this).remove();                
            })
//        }
        
        return false;
    })

    jQuery('#ps-related-box .combobox').combobox();
    
    jQuery('#psForm').submit(function(){
        if(jQuery('#psForm #product_name').val() == '')    
        {
            jQuery('#ps-info-box').find('.message').remove();
            jQuery('#ps-info-box .column').prepend('<div class="message error">Product/Service name should not be empty!</div>');
            jQuery('#product_name').focus();
            return false;
        }
    });
})

</script>
<?php

get_footer();