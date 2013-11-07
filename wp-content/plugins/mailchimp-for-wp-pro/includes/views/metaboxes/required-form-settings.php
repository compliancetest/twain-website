<?php  // Use nonce for verification
  wp_nonce_field( 'mc4wp_save_form', '_mc4wp_nonce' );
?>

<p class="mc4wp-notice" style="display: none;"></p>

<h4 class="mc4wp-title">Lists this form subscribes to</h4>
<p>
<?php if(!$lists) { 
				?>No lists found, are you connected to MailChimp?<?php
		} else { ?>
			<ul id="mc4wp-lists">
				<?php foreach($lists as $list) {
					?><li><label><input type="checkbox" name="mc4wp_form[lists][<?php echo $list->id; ?>]" value="<?php echo $list->id; ?>" data-groupings="<?php echo esc_attr(json_encode($list->interest_groupings)); ?>" data-fields="<?php echo esc_attr(json_encode($list->merge_vars)); ?>" <?php if(array_key_exists($list->id, $form_settings['lists'])) echo 'checked="checked"'; ?>> <?php echo $list->name; ?></label></li><?php
				} ?>
			</ul>
		<?php
		} ?>
</p>

<hr />

<div id="mc4wp-fw">

	<h4 class="mc4wp-title">Add a new field</h4>
	<p>
		<select class="widefat" id="mc4wp-fw-mailchimp-fields">
			<option class="default" value="" disabled selected>Select MailChimp field to add..</option>
			<optgroup label="MailChimp merge fields" class="merge-fields"></optgroup>
			<optgroup label="Interest groupings" class="groupings"></optgroup>
			<optgroup label="Other" class="other">
				<option class="default" value="submit">Submit button</option>				
			</optgroup>
		</select>
	</p>

	<div id="mc4wp-fw-fields">

		<p class="row label">
			<label for="mc4wp-fw-label">Label <small>(optional)</small></label>
			<input class="widefat" type="text" id="mc4wp-fw-label" />
		</p>

		<p class="row placeholder">
			<label for="mc4wp-fw-placeholder">Placeholder <small>(optional, HTML5)</small></label>
			<input class="widefat" type="text" id="mc4wp-fw-placeholder" />
		</p>

		<p class="row value">
			<label for="mc4wp-fw-value"><span id="mc4wp-fw-value-label">Initial value <small>(optional)</small></span></label>
			<input class="widefat" type="text" id="mc4wp-fw-value" />
		</p>

		<p class="row values" id="mc4wp-fw-values">
			<label for="mc4wp-fw-values">Value labels <small>(leave empty to hide)</small></label>
		</p>
		

		<p class="row wrap-p">
			<label for="mc4wp-fw-wrap-p"><input type="checkbox" id="mc4wp-fw-wrap-p" value="1" checked /> Wrap in paragraph (<code>&lt;p&gt;</code>) tags?</label>
		</p>

		<p class="row required">
			<label for="mc4wp-fw-required"><input type="checkbox" id="mc4wp-fw-required" value="1" /> Required field? <small>(HTML5)</small></label>
		</p>

		<p>
			<input class="button button-large" type="button" id="mc4wp-fw-add-to-form" value="&laquo; add HTML to form" />
		</p>

		<p>
			<label for="mc4wp-fw-preview">Generated HTML</label>
			<textarea class="widefat" id="mc4wp-fw-preview" rows="5"></textarea>
		</p>

		

	</div>
</div>
<hr />

<h4 class="mc4wp-title">Form usage</h4>
<p class="mc4wp-form-usage">Use the shortcode <input type="text" onfocus="this.select();" readonly="readonly" value="[mc4wp_form id=&quot;<?php echo $post->ID; ?>&quot;]" class="mc4wp-shortcode-example"> to display this form inside a post, page or text widget.</p>