<?php defined('ABSPATH') OR exit; ?>

	<?php $table->views(); ?>
	<form method="get" action="<?php echo admin_url('admin.php'); ?>">
		<input type="hidden" name="page" value="mc4wp-pro-reports" />
		<input type="hidden" name="tab" value="log" />
		<?php $table->search_box('search', 'mc4wp-log-search'); ?>
		<?php $table->display(); ?>
	</form>
	