<?php
/*
Template Name Posts: Service
*/
$isAjax = isset($_REQUEST['is_ajax']) ? true : false;

global $post;

$service = new Service( get_the_ID() );
$service->load();

if($isAjax)
{
    ?>
    <div class="popup-box" id="product-popup-box" style="display: none">
        <div class="popup-box-header radius6 noradiusbottom">Service Details</div>
        <div class="popup-box-content">
            <?php
            include(dirname(__FILE__) . '/content/service.php');
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
            include(dirname(__FILE__) . '/content/service.php');
            ?>
        </div> <!--end content container-->
    </div>
    <?php
    get_footer();
}