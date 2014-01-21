
      <?php
       wp_nav_menu( array(
							'theme_location' => 'dashboard-menu',
							'container' =>false,
							'echo' => true,
							'depth' => 0,
							'menu_id' => '',
                            'menu_class' => 'tabs no-ajax',
                            'link_before' => '<span class="menu-icon"></span><span>',
                            'link_after' => '</span>',
						)
					);
       ?>
      <div class="clear"></div>