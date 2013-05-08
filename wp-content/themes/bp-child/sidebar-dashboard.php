<div class="sidebar_widget">

      <?php
       wp_nav_menu( array(
							'theme_location' => 'dashboard-menu',
							'container' =>false,
							'echo' => true,
							'depth' => 0,
						//	'fallback_cb'=>'headermenu',
							'menu_id' => ''
						)
					);
       ?>
      <div class="clear"></div>

</div>

