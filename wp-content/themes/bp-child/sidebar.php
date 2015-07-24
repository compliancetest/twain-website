<div class="sidebar">
	<?php if ( ! dynamic_sidebar( 'Sidebar' )) : ?>

		<div id="sidebar-search" class="sidebar_widget">
			<h2>Search</h2>
			<?php get_search_form(); ?> <!-- outputs the default Wordpress search form-->
		</div>
		
		<div id="sidebar-archives" class="sidebar_widget">
			<h2>Archives</h2>
			<ul>
				<?php wp_get_archives( 'type=monthly' ); ?>
			</ul>
		</div>

		<div id="sidebar-meta" class="sidebar_widget">
			<h2>Meta</h2>
			<ul>
				<?php wp_register(); ?>
				<li><?php wp_loginout(); ?></li>
				<?php wp_meta(); ?>
			</ul>
		</div>

	<?php endif; ?>
</div><!--sidebar-->