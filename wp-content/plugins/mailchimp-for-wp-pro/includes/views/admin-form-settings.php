<?php defined('ABSPATH') OR exit; ?>
<div id="mc4wp-admin" class="wrap form-settings">

	<h2><img src="<?php echo plugins_url('mailchimp-for-wp-pro/assets/img/menu-icon.png'); ?>" /> MailChimp for WordPress: <?php _e("Forms", "mailchimp-for-wp-pro"); ?></h2>

	<h2 class="nav-tab-wrapper">  
		<a href="?page=mc4wp-pro-form-settings&tab=forms-settings" class="nav-tab <?php echo ($tab == 'forms-settings') ? 'nav-tab-active' : ''; ?>"><?php _e("Forms & Settings", "mailchimp-for-wp-pro"); ?></a>
		<a href="?page=mc4wp-pro-form-settings&tab=form-css" class="nav-tab <?php echo ($tab == 'form-css') ? 'nav-tab-active' : ''; ?>"><?php _e("Custom Form Styling", "mailchimp-for-wp-pro"); ?></a>    
	</h2> 

	<?php settings_errors(); ?>
	
	<br class="clear" />

		<?php require "parts/admin-{$tab}.php"; ?>

		<?php include 'parts/admin-footer.php'; ?>

</div>