<?php 
get_header();
?>

	<div class="content container">
		<!--page header-->
		
		<!--end page header-->
			<?php if (have_posts()) while (have_posts()) : the_post(); ?>
			<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<?php if ( in_category( 'docs' )) {
					echo 'doocs';
					}
					
				?>
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

	</div> <!--end content container-->

<?php
get_footer();
?>
