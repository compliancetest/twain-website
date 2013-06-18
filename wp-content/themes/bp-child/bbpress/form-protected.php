<?php

/**
 * Password Protected
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<div id="bbpress-forums" class="tab-content white_bcg padding10">
	<fieldset class="bbp-form" id="bbp-protected">
		<Legend><?php _e( 'Protected', 'bbpress' ); ?></legend>

		<?php echo get_the_password_form(); ?>

	</fieldset>
</div>