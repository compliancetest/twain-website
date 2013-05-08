<?php
get_header();
?>

<div id="content-pattern">
<div id="content-wrapper">
	<div class="space25"></div>
	<div class="content container">
		 <!--end infos-->
		
		<div class="clear"></div>
		
		<div class="column">
			
			
		<div class="content700">
			<?php if (have_posts()) while (have_posts()) : the_post(); ?>
			<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				
				<div class="content_inner">
					<?php if (has_post_thumbnail()) {
						echo '<a href="'.get_permalink().'">';
						the_post_thumbnail('post-thumb', array('class' => 'page_thumb'));
						echo '</a>';
					}
					the_content(); ?>
				</div>
			<?php endwhile; ?>
		</div>
		<?php get_sidebar(); ?>
				
			
			
			
			
			
			
			
		</div><!--end column-->
		
		<div class="clear"></div>
		<div class="space15"></div>
	</div> <!--end content container-->
	
</div>
<div class="space45"></div>
<div class="clear"></div>
</div>


<div class="clear"></div>
<?php
get_footer();
?>
