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
<?php if( ($newMessages = bp_get_total_unread_messages_count()) > 0) { ?>
<script type="text/javascript">

    jQuery(document).ready(function(){
        jQuery('#menu-dashboard_menu li.my_messages').append('<span class="new"><?php echo $newMessages ?></span>');
    })

</script>
<?php } ?>