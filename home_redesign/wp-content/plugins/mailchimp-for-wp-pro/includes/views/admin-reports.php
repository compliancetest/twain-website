<?php defined('ABSPATH') OR exit; ?>
<div id="mc4wp-admin" class="wrap reports">

	<h2 class="nav-tab-wrapper">  
		<a href="?page=mc4wp-pro-reports&tab=statistics" class="nav-tab <?php echo ($tab == 'statistics') ? 'nav-tab-active' : ''; ?>"><?php _e("Statistics", "mailchimp-for-wp-pro"); ?></a>
		<a href="?page=mc4wp-pro-reports&tab=log" class="nav-tab <?php echo ($tab == 'log') ? 'nav-tab-active' : ''; ?>">Log</a>    
	</h2> 

	<br class="clear" />

	<?php include "parts/admin-{$tab}.php"; ?> 


</div>