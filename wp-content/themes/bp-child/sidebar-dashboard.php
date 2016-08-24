
    <?php
    wp_nav_menu( array(
					    'theme_location' => 'dashboard-menu',
					    'container' =>false,
					    'echo' => true,
					    'depth' => 0,
					    'menu_id' => '',
                        'menu_class' => 'tabs no-ajax',
                        'link_before' => '<span class="menu-icon"></span><span class="menu-title">',
                        'link_after' => '</span>',
				    )
			    );
    ?>
    <?php if (!is_organisation_admin()): ?>
        <script type="text/javascript">
            jQuery('.dashboard-tabs .menu-organisation').remove();
        </script>
    <?php endif; ?>
    <?php if ( ! is_super_admin() ): ?>
        <script type="text/javascript">
            jQuery('.dashboard-tabs .menu-apilogs').remove();
        </script>
    <?php endif; ?>
    
    <div class="clear"></div>