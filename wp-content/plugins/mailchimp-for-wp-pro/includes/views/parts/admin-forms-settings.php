<?php defined('ABSPATH') OR exit; ?>
<h2>Sign-Up Forms <a href="<?php echo admin_url('post-new.php?post_type=mc4wp-form'); ?>" class="add-new-h2">Create New Form</a></h2>

	<?php $table->display(); ?>

	<form method="post" action="options.php">

		<?php settings_fields( 'mc4wp_form_settings' ); ?>

		<h3>General form settings</h3>				
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="mc4wp_load_stylesheet_select">Load styles or theme?</label></th>
				<td class="nowrap">
					<select name="mc4wp_form[css]" id="mc4wp_load_stylesheet_select">
						<option value="0" <?php selected($opts['css'], 0); ?>>No</option>
						<option value="default" <?php selected($opts['css'], 'default'); ?><?php selected($opts['css'], 1); ?>>Yes, load basic formatting styles</option>
						<option value="custom" <?php selected($opts['css'], 'custom'); ?>>Yes, load my custom form styles</option>
						<optgroup label="Load a default form theme">
							<option value="light" <?php selected($opts['css'], 'light'); ?>>Light theme</option>
							<option value="red" <?php selected($opts['css'], 'red'); ?>>Red theme</option>
							<option value="green" <?php selected($opts['css'], 'green'); ?>>Green theme</option>
							<option value="blue" <?php selected($opts['css'], 'blue'); ?>>Blue theme</option>
							<option value="dark" <?php selected($opts['css'], 'dark'); ?>>Dark theme</option>
							<option value="custom-color" <?php selected($opts['css'], 'custom-color'); ?>>Custom color theme</option>
						</optgroup>
					</select>
				</td>
				<td class="desc">
					If you <a href="?page=mc4wp-pro-form-settings&tab=form-css">created a custom stylesheet</a> and want it to be loaded, select "custom form styles". Otherwise, choose the basic formatting styles or one of the default themes.
				</td>
			</tr>
			<tr id="mc4wp-custom-color" <?php if($opts['css'] != 'custom-color') { echo 'style="display: none;"'; } ?>>
				<th><label for="mc4wp-custom-color-input">Select Color</label></th>
				<td>
					<input id="mc4wp-custom-color-input" name="mc4wp_form[custom_theme_color]" type="text" class="color-field" value="<?php echo esc_attr($opts['custom_theme_color']); ?>" />
				</td>
			</tr>

		</table>

		<?php submit_button(__("Save all changes")); ?>

		<h3>Default MailChimp settings</h3>
		<p>The following settings apply to <strong>all</strong> forms but can be overridden on a per-form basis.</p>

		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="mc4wp_form_hide_after_success">Double opt-in?</label></th>
				<td class="nowrap">
					<input type="radio" id="mc4wp_form_double_optin_1" name="mc4wp_form[double_optin]" value="1" <?php if($opts['double_optin'] == 1) echo 'checked="checked"'; ?> /> 
					<label for="mc4wp_form_double_optin_1">Yes</label> &nbsp; 
					<input type="radio" id="mc4wp_form_double_optin_0" name="mc4wp_form[double_optin]" value="0" <?php if($opts['double_optin'] == 0) echo 'checked="checked"'; ?> /> 
					<label for="mc4wp_form_double_optin_0">No</label>
				</td>
				<td class="desc">Tick "yes" if you want users to confirm their email address (recommended).</td>
			</tr>
			<?php $enabled = !$opts['double_optin']; ?>
			<tr id="mc4wp-send-welcome"  valign="top" <?php if(!$enabled) { ?>class="hidden"<?php } ?>>
				<th scope="row">Send Welcome Email?</th>
				<td class="nowrap">
					<input type="radio" id="mc4wp_form_send_welcome_1" name="mc4wp_form[send_welcome]" value="1" <?php if($enabled) { checked($opts['send_welcome'], 1); } else { echo 'disabled'; } ?> />
					<label for="mc4wp_form_send_welcome_1">Yes</label> &nbsp; 
					<input type="radio" id="mc4wp_form_send_welcome_0" name="mc4wp_form[send_welcome]" value="0" <?php if($enabled) { checked($opts['send_welcome'], 0); } else { echo 'disabled'; } ?> />
					<label for="mc4wp_form_send_welcome_0">No</label> &nbsp; 
				</td>
				<td class="desc">Tick "yes" if you want to send your lists Welcome Email if a subscribe succeeds (only when double opt-in is disabled).</td>
			</tr>
			<tr valign="top">
				<th scope="row">Update existing subscribers?</th>
				<td class="nowrap">
					<input type="radio" id="mc4wp_form_update_existing_1" name="mc4wp_form[update_existing]" value="1" <?php checked($opts['update_existing'], 1); ?> /> 
					<label for="mc4wp_form_update_existing_1">Yes</label> &nbsp; 
					<input type="radio" id="mc4wp_form_update_existing_0" name="mc4wp_form[update_existing]" value="0" <?php checked($opts['update_existing'], 0); ?> /> 
					<label for="mc4wp_form_update_existing_0">No</label> &nbsp; 
				</td>
				<td class="desc">Tick "yes" if you want to update existing subscribers instead of showing the "already subscribed" message.</td>
			</tr>
			<?php $enabled = $opts['update_existing']; ?>
			<tr id="mc4wp-replace-interests" valign="top" <?php if(!$enabled) { ?>class="hidden"<?php } ?>>
				<th scope="row">Replace interest groups?</th>
				<td class="nowrap">
					<input type="radio" id="mc4wp_form_replace_interests_1" name="mc4wp_form[replace_interests]" value="1" <?php if($enabled) { checked($opts['replace_interests'], 1); } else { echo 'disabled'; } ?> /> 
					<label for="mc4wp_form_replace_interests_1">Yes</label> &nbsp; 
					<input type="radio" id="mc4wp_form_replace_interests_0" name="mc4wp_form[replace_interests]" value="0" <?php if($enabled) { checked($opts['replace_interests'], 0); } else { echo 'disabled'; } ?> /> 
					<label for="mc4wp_form_replace_interests_0">No</label> 
				</td>
				<td class="desc">Tick "yes" if you want to replace the interest groups with the groups provided instead of adding the provided groups to the member's interest groups (only when updating a subscriber).</td>
			</tr>
		</table>

		<h3>Default form settings</h3>
		<p>The following settings apply to <strong>all</strong> forms but can be overridden on a per-form basis.</p>

		<table class="form-table">
			<tr valign="top">
				<th scope="row">Enable AJAX form submission?</th>
				<td class="nowrap"><input type="radio" id="mc4wp_form_ajax_1" name="mc4wp_form[ajax]" value="1" <?php if($opts['ajax'] == 1) echo 'checked="checked"'; ?> /> <label for="mc4wp_form_ajax_1">Yes</label> &nbsp; <input type="radio" id="mc4wp_form_ajax_0" name="mc4wp_form[ajax]" value="0" <?php if($opts['ajax'] == 0) echo 'checked="checked"'; ?> /> <label for="mc4wp_form_ajax_0">No</label></td>
				<td class="desc">Tick "yes" if you want the form to submit without causing the page to reload.</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="mc4wp_form_hide_after_success">Hide form after a successful sign-up?</label></th>
				<td class="nowrap"><input type="radio" id="mc4wp_form_hide_after_success_1" name="mc4wp_form[hide_after_success]" value="1" <?php if($opts['hide_after_success'] == 1) echo 'checked="checked"'; ?> /> <label for="mc4wp_form_hide_after_success_1">Yes</label> &nbsp; <input type="radio" id="mc4wp_form_hide_after_success_0" name="mc4wp_form[hide_after_success]" value="0" <?php if($opts['hide_after_success'] == 0) echo 'checked="checked"'; ?> /> <label for="mc4wp_form_hide_after_success_0">No</label></td>
				<td class="desc">Tick "yes" to only show the success message after a successful sign-up.</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="mc4wp_form_redirect">Redirect to this URL after a successful sign-up</label></th>
				<td colspan="2">
					<input type="text" class="widefat" name="mc4wp_form[redirect]" id="mc4wp_form_redirect" value="<?php echo $opts['redirect']; ?>" />
					<small>Leave empty or enter <strong>0</strong> (zero) if you don't want to redirect.</small>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="mc4wp_form_text_success">Success message</label></th>
					<td colspan="2" ><input type="text" class="widefat" id="mc4wp_form_text_success" name="mc4wp_form[text_success]" value="<?php echo esc_attr($opts['text_success']); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="mc4wp_form_text_error">General error message</label></th>
					<td colspan="2" ><input type="text" class="widefat" id="mc4wp_form_text_error" name="mc4wp_form[text_error]" value="<?php echo esc_attr($opts['text_error']); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="mc4wp_form_text_invalid_email">Invalid email address message</label></th>
					<td colspan="2" ><input type="text" class="widefat" id="mc4wp_form_text_invalid_email" name="mc4wp_form[text_invalid_email]" value="<?php echo esc_attr($opts['text_invalid_email']); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="mc4wp_form_text_already_subscribed">Email address is already on list message</label></th>
					<td colspan="2" ><input type="text" class="widefat" id="mc4wp_form_text_already_subscribed" name="mc4wp_form[text_already_subscribed]" value="<?php echo esc_attr($opts['text_already_subscribed']); ?>" /></td>
				</tr>
				<tr>
					<th></th>
					<td colspan="2"><p class="help">HTML tags like &lt;a&gt; and &lt;strong&gt; etc. are allowed in the message fields.</p></td>
				</tr>
			</table>

			<?php submit_button(__("Save all changes")); ?>

		</form>