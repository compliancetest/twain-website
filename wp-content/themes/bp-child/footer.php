		<div id="footer-wrapper">
			<div class="footer">
				<div id="menu-footer">
					<h5>MENU</h5>
					<?php
					wp_nav_menu( array(
							'theme_location' => 'footer-menu',
							'container' =>false,
							'echo' => true,
							'depth' => 0,
							'fallback_cb'=>'footermenu',
							'walker' => new footer_walker()
						)
					);
					?>
				</div>
				<div id="useful-links">
					<h5>USEFUL LINKS</h5>
					<?php
					wp_nav_menu( array(
							'theme_location' => 'footer-menu2',
							'container' =>false,
							'echo' => true,
							'depth' => 0,
							'fallback_cb'=>'footermenu',
							'walker' => new footer_walker()
						)
					);
					?>
				</div>
				<div id="partners">

					<h5>PARTNERS</h5>
					<img src="<?php echo of_get_option('plogo1'); ?>" class="left">
					<img src="<?php echo of_get_option('plogo2'); ?>" class="left">
					<img src="<?php echo of_get_option('plogo3'); ?>" class="left">
					<img src="<?php echo of_get_option('plogo4'); ?>" class="left">
					<img src="<?php echo of_get_option('plogo5'); ?>" class="left">
					<div class="clear"></div>
					<div class="space60"></div>
					<h6 class="right"><?php echo of_get_option('copyright'); ?></h6>				

				</div>
				<div class="clear"></div>
				<div class="space15"></div>
			</div>
		</div>
<!-- **************** FOOTER *************** -->
	</div>
	
	<?php wp_footer(); ?>

</body>
</html>
