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
                        <h2 style="line-height: 1.2em"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>                        
                        <div class="clear"></div>
                        <?php if (has_post_thumbnail()) {
                            echo '<a href="'.get_permalink().'">';
                            the_post_thumbnail('post-thumb', array('class' => 'page_thumb'));
                            echo '</a>';
                        }                        
                        the_content(); 
                        if($post->post_type == 'event')
                        {
                            ?>
                            <p><?php echo get_post_meta($post->ID, 'event_date', true)?></p>
                            <p><?php echo get_post_meta($post->ID, 'event_location', true)?></p>
                            <?php    
                        }
                        ?>
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
