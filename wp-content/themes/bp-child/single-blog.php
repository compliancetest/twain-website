<?php
  /**
  * Blog Template
  */

get_header();
?>


    <div class="content container news-events-wrapper"><!-- Start Content Container-->        
        
            <?php if (have_posts()) while (have_posts()) : the_post(); ?>
            <div class="content_inner">
                <div class="four_fifths right">
                    <div class="column">
                        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>                        
                        <div class="clear"></div>
                        <?php if (has_post_thumbnail()) {
                            echo '<a href="'.get_permalink().'">';
                            the_post_thumbnail('post-thumb', array('class' => 'page_thumb'));
                            echo '</a>';
                        }
                        the_content(); ?>
                    </div>
                </div>
                <div class="fifth left">
                    <div class="column">                    
                        <?php get_sidebar('newsevents')?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
            
        
        <div class="clear"></div>
        
    </div> <!--End content container-->    



<?php
get_footer();
?>
