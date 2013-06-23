<?php
/*
 * Template Name: No Header
 */
get_header();
?>

    <div class="content container"><!-- Start Content Container-->        
        
            <?php if (have_posts()) while (have_posts()) : the_post(); ?> 
                <div class="content_inner">
                    <?php if (has_post_thumbnail()) {
                        echo '<a href="'.get_permalink().'">';
                        the_post_thumbnail('post-thumb', array('class' => 'page_thumb'));
                        echo '</a>';
                    }
                    the_content(); ?>
                </div>
            <?php endwhile; ?>
            
        
        <div class="clear"></div>
        
    </div> <!--End content container-->    

<?php
get_footer();
?>
