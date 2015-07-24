<?php
/**
 * The template for displaying 404 pages (Not Found)
 *
 */

get_header(); ?>

    <div class="content container"><!-- Start Content Container-->
        <div class="page-title-block column">
            <h2 class="nomarginbottom left"><?php echo of_get_option('404_title'); ?></h2>
            <div class="clear"></div>
        </div>
        <div class="content_inner column">
            <?php echo of_get_option('404_description'); ?>
        </div>
        <div class="clear"></div>
    </div> <!--End content container-->

<?php get_footer(); ?>