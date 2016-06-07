<?php
/**
 * The template for displaying 404 pages (Not Found)
 *
 */

get_header(); ?>

    <div class="content container"><!-- Start Content Container-->
        <div class="page-title-block column">
            <h2 class="nomarginbottom left">
                <?php echo of_get_option('404_title'); ?>
                <?php if(of_get_option('404_title')): ?>
                    <?php echo of_get_option('404_title'); ?>
                <?php else: ?>
                    Page not found
                <?php endif; ?>
            </h2>
            <div class="clear"></div>
        </div>
        <div class="content_inner column">
            <?php if(of_get_option('404_description')): ?>
                <?php echo of_get_option('404_description'); ?>
            <?php else: ?>
                <p class="tocenter">The page you were trying to view doesn't exist or has been removed</p>
            <?php endif; ?>
        </div>
        <div class="clear"></div>
    </div> <!--End content container-->

<?php get_footer(); ?>