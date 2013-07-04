<?php
/*
Template Name Posts: Product / Service
*/

$isAjax = isset($_REQUEST['is_ajax']) ? true : false;

global $post;

$product = new ProductAndService(get_the_ID());
$product->load();

if($isAjax)
{
?>
<div class="popup-box" id="product-popup-box" style="display: none">
    <div class="popup-box-header radius6 noradiusbottom">Product / Service Details</div>
    <div class="popup-box-content"> 
        <?php 
            include(dirname(__FILE__) . '/content/product-service.php');
        ?>
    </div>
    <a class="close_btn"></a>                
</div>
<?php   
}else{
        

    get_header();
?>
	    <div class="content container">
            <div class="grid  dark_gray_txt">
                <?php 
                    include(dirname(__FILE__) . '/content/product-service.php');
                ?>
	        </div> <!--end content container-->	
        </div>
<?php
    get_footer();
}