<?php defined('ABSPATH') OR exit; ?>

<div id="mc4wp-admin" class="wrap checkbox-settings">

	<h2><img src="<?php echo plugins_url('mailchimp-for-wp-pro/assets/img/menu-icon.png'); ?>" /> MailChimp for WordPress: Checkbox Settings</h2>	
	<?php settings_errors(); ?>

	<p>To use the MailChimp for WP sign-up checkboxes, select at least one list and one form to add the checkbox to.</p>

	<form class="clear" method="post" action="options.php">

		<?php settings_fields( 'mc4wp_checkbox_settings' ); ?>

		<h3>List Settings</h3>	

		<table class="form-table">
			<tr valign="top">
				<th scope="row">Lists</th>
				
					<?php // loop through lists
					if(!$lists || empty($lists)) { 
						?><td colspan="2">No lists found, are you connected to MailChimp?</td><?php
					} else { ?>
					<td class="nowrap">
						<?php foreach($lists as $list) {
							?><input type="checkbox" id="mc4wp_checkbox_list_<?php echo $list->id ?>_cb" name="mc4wp_checkbox[lists][<?php echo $list->id; ?>]" value="<?php echo $list->id; ?>" <?php if(array_key_exists($list->id, $opts['lists'])) echo 'checked="checked"'; ?>> <label for="mc4wp_checkbox_list_<?php echo $list->id; ?>_cb"><?php echo $list->name; ?></label><br /><?php
						} ?>
					</td>
					<td class="desc" <?php if(empty($opts['lists'])) { echo 'style="color:red;"'; } ?>>Select at least one MailChimp list to subscribe to</td>
					<?php
				} ?>
				
			</tr>
			<tr valign="top">
				<th scope="row">Double opt-in?</th>
				<td class="nowrap"><input type="radio" id="mc4wp_checkbox_double_optin_1" name="mc4wp_checkbox[double_optin]" value="1" <?php if($opts['double_optin'] == 1) echo 'checked="checked"'; ?> /> <label for="mc4wp_checkbox_double_optin_1">Yes</label> &nbsp; <input type="radio" id="mc4wp_checkbox_double_optin_0" name="mc4wp_checkbox[double_optin]" value="0" <?php if($opts['double_optin'] == 0) echo 'checked="checked"'; ?> /> <label for="mc4wp_checkbox_double_optin_0">No</label></td>
				<td class="desc">Tick "yes" if you want subscribers to have to confirm their email address (recommended)</td>
			</tr>
			<?php $enabled = !$opts['double_optin']; ?>
			<tr id="mc4wp-send-welcome"  valign="top" <?php if(!$enabled) { ?>class="hidden"<?php } ?>>
				<th scope="row">Send Welcome Email?</th>
				<td class="nowrap">
					<input type="radio" id="mc4wp_checkbox_send_welcome_1" name="mc4wp_checkbox[send_welcome]" value="1" <?php if($enabled) { checked($opts['send_welcome'], 1); } else { echo 'disabled'; } ?> />
					<label for="mc4wp_checkbox_send_welcome_1">Yes</label> &nbsp; 
					<input type="radio" id="mc4wp_checkbox_send_welcome_0" name="mc4wp_checkbox[send_welcome]" value="0" <?php if($enabled) { checked($opts['send_welcome'], 0); } else { echo 'disabled'; } ?> />
					<label for="mc4wp_checkbox_send_welcome_0">No</label> &nbsp; 
				</td>
				<td class="desc">Tick "yes" if you want to send your lists Welcome Email if a subscribe succeeds (only when double opt-in is disabled).</td>
			</tr>
		</table>
		
		<h3>Checkbox settings</h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">Add the checkbox to these forms</th>
				<td class="nowrap">
					<?php foreach($checkbox_plugins as $code => $name) { ?>
						<label><input name="mc4wp_checkbox[show_at_<?php echo $code; ?>]" value="1" type="checkbox" <?php checked($opts['show_at_'.$code], 1); ?>> <?php echo $name; ?></label> <br />
					<?php } ?>
					<label><input name="mc4wp_checkbox[show_at_other_forms]" value="1" type="checkbox" <?php if($opts['show_at_other_forms']) echo 'checked '; ?>> Other forms (manual)</label>
				</td>
				<td class="desc">
					Select to which forms a sign-up checkbox should be added.<br />
					Check "other forms" if you're adding the checkbox to a third-party or custom form.
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="mc4wp_checkbox_label">Checkbox label text</label></th>
				<td colspan="2">
					<input type="text" class="widefat" id="mc4wp_checkbox_label" name="mc4wp_checkbox[label]" value="<?php echo esc_attr($opts['label']); ?>" required />
					<p class="help">HTML tags like <code>&lt;strong&gt;</code> and <code>&lt;em&gt;</code> are allowed in the label text.</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Pre-check the checkbox?</th>
				<td class="nowrap"><input type="radio" id="mc4wp_checkbox_precheck_1" name="mc4wp_checkbox[precheck]" value="1" <?php if($opts['precheck'] == 1) echo 'checked="checked"'; ?> /> <label for="mc4wp_checkbox_precheck_1">Yes</label> &nbsp; <input type="radio" id="mc4wp_checkbox_precheck_0" name="mc4wp_checkbox[precheck]" value="0" <?php if($opts['precheck'] == 0) echo 'checked="checked"'; ?> /> <label for="mc4wp_checkbox_precheck_0">No</label></td>
				<td class="desc"></td>
			</tr>
			<tr valign="top">
				<th scope="row">Load some default CSS?</th>
				<td class="nowrap"><input type="radio" id="mc4wp_checbox_css_1" name="mc4wp_checkbox[css]" value="1" <?php if($opts['css'] == 1) echo 'checked="checked"'; ?> /> <label for="mc4wp_checbox_css_1">Yes</label> &nbsp; <input type="radio" id="mc4wp_checbox_css_0" name="mc4wp_checkbox[css]" value="0" <?php if($opts['css'] == 0) echo 'checked="checked"'; ?> /> <label for="mc4wp_checbox_css_0">No</label></td>
				<td class="desc">Tick "yes" if the checkbox appears in a weird place.</td>
			</tr>
		</table>

		<?php submit_button(__("Save all changes", "mailchimp-for-wp-pro")); ?>

		<?php if($selected_checkbox_hooks) { ?>
		<h3>Custom label texts</h3>
		<p>Override the default checkbox label text for any given checkbox using the fields below.</p>
		<table class="form-table">
			<?php foreach($selected_checkbox_hooks as $code => $name) { ?>
			<tr valign="top">
				<th scope="row"><?php echo $name; ?></th>
				<td><input type="text" name="mc4wp_checkbox[text_<?php echo $code; ?>_label]" placeholder="<?php echo esc_attr($opts['label']); ?>" class="widefat" value="<?php if(isset($opts['text_'.$code.'_label'])) echo esc_attr($opts['text_'.$code.'_label']); ?>" /></td>
			</tr>
			<?php } ?>
			<tr>
				<th></th>
				<td><p class="help">HTML tags like <code>&lt;strong&gt;</code> and <code>&lt;em&gt;</code> are allowed in the label text.</p></td>
			</tr>
		</table>

		<?php submit_button(__("Save all changes", "mailchimp-for-wp-pro")); ?>

		<?php } ?>

	</form>

	<?php include 'parts/admin-footer.php'; ?>

</div>