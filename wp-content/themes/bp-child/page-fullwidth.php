<?php
/*
 * Template Name: Full page
 */
get_header();
?>

	<div class="content container"><!-- Start Content Container-->
		
		<div class="column">            
			<?php if (have_posts()) while (have_posts()) : the_post(); ?>
			<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				
				<div class="content_inner">
                <a href="#" class="action-btn print-btn"><span class="p"></span><span class="t">PRINT</span></a>                    
					<?php if (has_post_thumbnail()) {
						echo '<a href="'.get_permalink().'">';
						the_post_thumbnail('post-thumb', array('class' => 'page_thumb'));
						echo '</a>';
					}
					the_content(); ?>
				</div>
			<?php endwhile; ?>
		</div><!--end column-->
		
		<div class="clear"></div>

	</div> <!--End content container-->	

<?php
get_footer();
?>
