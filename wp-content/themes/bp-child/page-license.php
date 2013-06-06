<?php
/*
 * Template Name: License Agreement
 */
get_header();
?>

	<div class="content container">
		
		<div class="column">

			<?php 
			if (have_posts()) while (have_posts()) : the_post(); ?>
			<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				
				<div class="content_inner">
					<?php if (has_post_thumbnail()) {
						echo '<a href="'.get_permalink().'">';
						the_post_thumbnail('post-thumb', array('class' => 'page_thumb'));
						echo '</a>';
					}
					?>
				</div>
			<?php endwhile;
			session_start();
			if(isset($_SESSION['license'])) {
				$value = $_SESSION['license'];
			} else {
				$value = '';
			}
			print $value; 
			?>
		</div><!--end column-->
		<div class="clear"></div>

	</div> <!--end content container-->

<?php
get_footer();
?>
