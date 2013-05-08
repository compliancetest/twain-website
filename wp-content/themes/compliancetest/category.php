<?php
/**
 * The template for Category page.
 */

get_header(); ?>
<div id="content-wrapper">
	<div class="container">
		<div class="content700">
			<?php if (have_posts()) { ?>
				<h2><?php single_cat_title(); ?></h2>
				<div class="content_inner">
					<?php while (have_posts()) : the_post(); ?>
						<div class="item_display">
							<?php if (has_post_thumbnail()) {
								echo '<a href="'.get_permalink().'">';
								the_post_thumbnail('post-thumb', array('class' => 'page_thumb'));
								echo '</a>';
							} ?>
							<h1><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1>
							<?php the_excerpt(); ?>
						</div>
					<?php endwhile; ?>
					<div class="pagination">
						<?php global $wp_query;
						$big = 999999999;
						echo paginate_links( array(
							'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
							'format' => '?paged=%#%',
							'current' => max( 1, get_query_var('paged') ),
							'total' => $wp_query->max_num_pages
						) ); ?>
					</div>
				</div>
			<?php } else { ?>
				<h1>Nothing found</h1>
				<div class="content_inner">
					<p>Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.</p>
					<?php get_search_form(); ?>
				</div>
			<?php } ?>
		</div>
		<?php get_sidebar(); ?>
	</div>
</div>
<div class="clear"></div>
<?php get_footer(); ?>