<?php defined('ABSPATH') OR exit; ?>
<div id="mc4wp-admin" class="wrap">

	<h2><img src="<?php echo plugins_url('mailchimp-for-wp-pro/assets/img/menu-icon.png'); ?>" /> MailChimp for WordPress: <?php _e("License & API Settings", "mailchimp-for-wp-pro"); ?></h2>

	<?php settings_errors(); ?>

	<form method="post" action="options.php">

		<?php settings_fields( 'mc4wp_settings' ); ?>

		<h3>
			<?php _e("License Settings", "mailchimp-for-wp-pro"); ?>
			<?php if($this->has_valid_license()) { ?>
				<span class="status positive"><?php echo ($this->get_license_status() == 'valid') ? 'ACTIVE' : 'LOCAL'; ?></span>
			<?php } else { ?>
				<span class="status negative">INACTIVE</span>
			<?php } ?>
		</h3>
		<table class="form-table">

			<tr valign="top">
				<th scope="row"><label for="mailchimp_license_key">MailChimp for WP Pro License Key</label></th>
				<td>
					<input type="text" class="widefat" placeholder="Your MailChimp for WP Pro license key" id="mailchimp_license_key" name="mc4wp[license_key]" value="<?php echo $opts['license_key']; ?>" <?php if($this->has_valid_license() && !empty($opts['license_key'])) { ?>readonly<?php } ?> />
					<?php if(empty($opts['license_key'])) { ?><small>Insert the license key you got when you bought the plugin and then save your settings.</small><?php } ?>
				</td>
				
			</tr>
			<?php if(!empty($opts['license_key'])) { ?>
			<tr valign="top">	
				<th scope="row" valign="top">
					<?php _e('Toggle license status'); ?>
				</th>
				<td>
					<?php if($this->has_valid_license()) { ?>
						<?php wp_nonce_field( 'mc4wp_toggle_license', '_mc4wp_nonce' ); ?>
						<input type="submit" class="button-secondary" name="mc4wp_toggle_license" value="<?php _e('Deactivate License'); ?>"/> &nbsp; 
						<small>(deactivate your license so you can activate it on another WP installation)</small>
					<?php } else {
						wp_nonce_field( 'mc4wp_toggle_license', '_mc4wp_nonce' ); ?>
						<input type="submit" class="button-secondary" name="mc4wp_toggle_license" value="<?php _e('Activate License'); ?>"/> &nbsp; 
						<small>(activate your license to gain access to all plugin functionality and to enable automatic updates)</small>
					<?php } ?>
					</td>
				</tr>
			<?php } ?>


			</table>

			<h3>API Settings <?php if($connected) { ?><span class="status positive">CONNECTED</span> <?php } else { ?><span class="status negative">NOT CONNECTED</span><?php } ?></h3>
			<table class="form-table">

				<tr valign="top">
					<th scope="row"><label for="mailchimp_api_key">MailChimp API Key</label></th>
					<td>
						<input type="text" class="widefat" placeholder="Your MailChimp API key" id="mailchimp_api_key" name="mc4wp[api_key]" value="<?php echo $opts['api_key']; ?>" />
						<small><a target="_blank" href="http://admin.mailchimp.com/account/api">Get your API key here.</a></small>
					</td>

				</tr>

			</table>

			<?php submit_button(); ?>
			
		</form>

		<?php if($connected) { ?>
		<h3>MailChimp data</h3>
		<p>The table below shows your cached MailChimp lists configuration. If you made any changes in your MailChimp configuration that is not yet represented in the table below, please renew the cache manually by hitting the "renew cached data" button.</p>

		<h4>Lists</h4>
		<table class="wp-list-table widefat">
			<thead>
				<tr>
					<th scope="col">List ID</th><th scope="col">Name</th><th scope="col">Merge fields</th><th scope="col">Groupings</th>
				</tr>
			</thead>
			<tbody>
				<?php if($lists) { ?>
				<?php foreach($lists as $list) { ?>
				<tr valign="top">
					<td><?php echo $list->id; ?></td>
					<td><?php echo $list->name; ?></td>
					<td><?php 
						$first = true;
						foreach($list->merge_vars as $merge_var) { 
							echo ($first) ? $merge_var->name : ', '. $merge_var->name;
							$first = false;
						} 
						?>
					</td>
					<td><?php 
						foreach($list->interest_groupings as $grouping) { 
							echo "<strong>{$grouping->name}:</strong> ";
							$first = true;
							foreach($grouping->groups as $group) {
								echo ($first) ? $group->name : ', '. $group->name;
								$first = false;
							}
							echo '<br />';
						} 
						?></td>
				</tr>
				<?php } ?>
				<?php } else { ?>
				<tr><td colspan="3"><p>No lists...</p></tr></td>
				<?php } ?>
			</tbody>
		</table>

		<p><form method="post"><input type="submit" name="renew-cached-data" value="Renew cached data" class="button" /></form></p>
		<?php } ?>

		<?php include 'parts/admin-footer.php'; ?>

	</div>