<?php
$postType = get_post_type();
if(in_array($postType, $postTypes) )
{
    include(STYLESHEETPATH . "/single-blog.php");
    exit;
}
get_header();
?>


    <div class="content container"><!-- Start Content Container-->        
        
            <?php if (have_posts()) while (have_posts()) : the_post(); ?>
            <div class="page-title-block column">
                <h2 class="nomarginbottom left width95P"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <a href="<?php echo addPrintParams(get_permalink(), 'static')?>" class="action-btn print-btn icon-btn print-page-btn" id="print-static-page-btn"><span class="p"></span></a>
                <div class="clear"></div>
            </div>        
                <div class="content_inner column">
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
