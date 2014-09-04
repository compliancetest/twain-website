<p>Any settings you specify here will override the <a href="<?php echo get_admin_url(null, 'admin.php?page=mc4wp-pro-form-settings'); ?>">general form settings</a>. If no setting is specified, the corresponding general setting value will be used.</p>

<h4 class="mc4wp-title">MailChimp settings</h4>
<table class="form-table">
	<tr valign="top">
		<th scope="row"><label for="mc4wp_form_hide_after_success">Double opt-in?</label></th>
		<td class="nowrap">
			<input type="radio" id="mc4wp_form_double_optin_1" name="mc4wp_form[double_optin]" value="1" <?php if($form_settings['double_optin'] == 1) echo 'checked="checked"'; ?> /> 
			<label for="mc4wp_form_double_optin_1">Yes</label> &nbsp; 
			<input type="radio" id="mc4wp_form_double_optin_0" name="mc4wp_form[double_optin]" value="0" <?php if($form_settings['double_optin'] == 0) echo 'checked="checked"'; ?> /> 
			<label for="mc4wp_form_double_optin_0">No</label> &nbsp; 
			<input type="radio" id="mc4wp_form_double_optin_inherit" name="mc4wp_form[double_optin]" value="" data-inherited-value="<?php echo $inherited_settings['double_optin']; ?>" <?php if($form_settings['double_optin'] == '') echo 'checked="checked"'; ?> /> 
			<label for="mc4wp_form_double_optin_inherit">Inherit</label>
		</td>
		<td class="desc">Tick "yes" if you want users to confirm their email address (recommended)</td>
	</tr>
	<tr valign="top">
		<th scope="row">Update existing subscribers?</th>
		<td class="nowrap">
			<input type="radio" id="mc4wp_form_update_existing_1" name="mc4wp_form[update_existing]" value="1" <?php checked($form_settings['update_existing'], 1); ?> /> 
			<label for="mc4wp_form_update_existing_1">Yes</label> &nbsp; 
			<input type="radio" id="mc4wp_form_update_existing_0" name="mc4wp_form[update_existing]" value="0" <?php checked($form_settings['update_existing'], 0); ?> /> 
			<label for="mc4wp_form_update_existing_0">No</label> &nbsp; 
			<input type="radio" id="mc4wp_form_update_existing_inherit" name="mc4wp_form[update_existing]" value="" data-inherited-value="<?php echo $inherited_settings['update_existing']; ?>" <?php checked($form_settings['update_existing'], ''); ?> />
			<label for="mc4wp_form_update_existing_inherit">Inherit</label>
		</td>
		<td class="desc">Tick "yes" if you want to update existing subscribers instead of showing the "already subscribed" message.</td>
	</tr>
	<?php $enabled = $final_settings['update_existing']; ?>
	<tr id="mc4wp-replace-interests" valign="top" class="<?php if(!$enabled) echo 'hidden'; ?>">
		<th scope="row">Replace interest groups?</th>
		<td class="nowrap">
			<input type="radio" id="mc4wp_form_replace_interests_1" name="mc4wp_form[replace_interests]" value="1" <?php if($enabled) { checked($form_settings['replace_interests'], 1); } else { echo 'disabled'; } ?> />
			<label for="mc4wp_form_replace_interests_1">Yes</label> &nbsp; 
			<input type="radio" id="mc4wp_form_replace_interests_0" name="mc4wp_form[replace_interests]" value="0" <?php if($enabled) { checked($form_settings['replace_interests'], 0); } else { echo 'disabled'; } ?> />
			<label for="mc4wp_form_replace_interests_0">No</label> &nbsp; 
			<input type="radio" id="mc4wp_form_replace_interests_inherit" name="mc4wp_form[replace_interests]" value="" <?php if($enabled) { checked($form_settings['replace_interests'], ''); } else { echo 'disabled'; } ?> />
			<label for="mc4wp_form_replace_interests_inherit">Inherit</label>
		</td>
		<td class="desc">Tick "yes" if you want to replace the interest groups with the groups provided instead of adding the provided groups to the member's interest groups (when updating a subscriber).</td>
	</tr>
	<?php $enabled = !$final_settings['double_optin']; ?>
	<tr id="mc4wp-send-welcome"  valign="top" class="<?php if(!$enabled) echo 'hidden'; ?>">
		<th scope="row">Send Welcome Email?</th>
		<td class="nowrap">
			<input type="radio" id="mc4wp_form_send_welcome_1" name="mc4wp_form[send_welcome]" value="1" <?php if($enabled) { checked($form_settings['send_welcome'], 1); } else { echo 'disabled'; } ?> />
			<label for="mc4wp_form_send_welcome_1">Yes</label> &nbsp; 
			<input type="radio" id="mc4wp_form_send_welcome_0" name="mc4wp_form[send_welcome]" value="0" <?php if($enabled) { checked($form_settings['send_welcome'], 0); } else { echo 'disabled'; } ?> />
			<label for="mc4wp_form_send_welcome_0">No</label> &nbsp; 
			<input type="radio" id="mc4wp_form_send_welcome_inherit" name="mc4wp_form[send_welcome]" value="" <?php if($enabled) { checked($form_settings['send_welcome'], ''); } else { echo 'disabled'; } ?> />
			<label for="mc4wp_form_send_welcome_inherit">Inherit</label>
		</td>
		<td class="desc">Tick "yes" if you want to send your lists Welcome Email if a subscribe succeeds.</td>
	</tr>
</table>

<h4 class="mc4wp-title">Form settings & messages</h4>
<table class="form-table">
	<tr valign="top">
		<th scope="row">Enable AJAX form submission?</th>
		<td class="nowrap">
			<input type="radio" id="mc4wp_form_ajax_1" name="mc4wp_form[ajax]" value="1" <?php if($form_settings['ajax'] == 1) echo 'checked="checked"'; ?> /> 
			<label for="mc4wp_form_ajax_1">Yes</label> &nbsp; 
			<input type="radio" id="mc4wp_form_ajax_0" name="mc4wp_form[ajax]" value="0" <?php if($form_settings['ajax'] == 0) echo 'checked="checked"'; ?> />
			<label for="mc4wp_form_ajax_0">No</label> &nbsp; 
			<input type="radio" id="mc4wp_form_ajax_inherit" name="mc4wp_form[ajax]" value="" <?php if($form_settings['ajax'] == '') echo 'checked="checked"'; ?> /> 
			<label for="mc4wp_form_ajax_inherit">Inherit</label>
		</td>
		<td class="desc">Tick "yes" if you want the form to submit without causing the page to reload.</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="mc4wp_form_hide_after_success">Hide form after a successful sign-up?</label></th>
		<td class="nowrap">
			<input type="radio" id="mc4wp_form_hide_after_success_1" name="mc4wp_form[hide_after_success]" value="1" <?php if($form_settings['hide_after_success'] == 1) echo 'checked="checked"'; ?> /> 
			<label for="mc4wp_form_hide_after_success_1">Yes</label> &nbsp; 
			<input type="radio" id="mc4wp_form_hide_after_success_0" name="mc4wp_form[hide_after_success]" value="0" <?php if($form_settings['hide_after_success'] == 0) echo 'checked="checked"'; ?> />
			<label for="mc4wp_form_hide_after_success_0">No</label> &nbsp; 
			<input type="radio" id="mc4wp_form_hide_after_success_inherit" name="mc4wp_form[hide_after_success]" value="" <?php if($form_settings['hide_after_success'] == '') echo 'checked="checked"'; ?> /> 
			<label for="mc4wp_form_hide_after_success_inherit">Inherit</label>
		</td>
		<td class="desc">Tick "yes" to hide the form fields after a successful sign-up.</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="mc4wp_form_redirect">Redirect to this URL after a successful sign-up</label></th>
		<td colspan="2">
			<input type="text" class="widefat" name="mc4wp_form[redirect]" id="mc4wp_form_redirect" placeholder="<?php echo $inherited_settings['redirect']; ?>" value="<?php echo $form_settings['redirect']; ?>" />
			<small>Leave empty or enter <strong>0</strong> (zero) if you don't want to redirect.</small>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="mc4wp_form_text_success">Success message</label></th>
		<td colspan="2" ><input type="text" class="widefat" id="mc4wp_form_text_success" name="mc4wp_form[text_success]" placeholder="<?php echo $inherited_settings['text_success']; ?>" value="<?php echo esc_attr($form_settings['text_success']); ?>" /></td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="mc4wp_form_text_error">General error message</label></th>
		<td colspan="2" ><input type="text" class="widefat" id="mc4wp_form_text_error" name="mc4wp_form[text_error]" placeholder="<?php echo $inherited_settings['text_error']; ?>"  value="<?php echo esc_attr($form_settings['text_error']); ?>" /></td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="mc4wp_form_text_invalid_email">Invalid email address message</label></th>
		<td colspan="2" ><input type="text" class="widefat" id="mc4wp_form_text_invalid_email" name="mc4wp_form[text_invalid_email]" placeholder="<?php echo $inherited_settings['text_invalid_email']; ?>" value="<?php echo esc_attr($form_settings['text_invalid_email']); ?>" /></td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="mc4wp_form_text_already_subscribed">Email address is already on list message</label></th>
		<td colspan="2" ><input type="text" class="widefat" id="mc4wp_form_text_already_subscribed" name="mc4wp_form[text_already_subscribed]" placeholder="<?php echo $inherited_settings['text_already_subscribed']; ?>" value="<?php echo esc_attr($form_settings['text_already_subscribed']); ?>" /></td>
	</tr>
	<tr>
		<th></th>
		<td colspan="2"><p><small>HTML tags like &lt;a&gt; and &lt;strong&gt; etc. are allowed in the message fields.</small></p></td>
	</tr>


</table>
