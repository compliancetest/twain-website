<?php defined('ABSPATH') OR exit; ?>
<div class="mc4wp-column" style="width:55%">

	<p>Use the fields below to create custom styling rules for your forms. </p>

	<form action="options.php" method="post">
	<?php settings_fields( 'mc4wp_form_css_settings' ); ?>

	<noscript>You need to have JavaScript enabled to see a preview of your form.</noscript>

	<div class="mc4wp-accordion" id="mc4wp-css-form">
		<h4>Form container style</h4>
		<div>
			<table class="form-table">
				<tr valign="top">
					<th width="1">Background color</th>
					<td class="nowrap"><input id="form-background-color" name="mc4wp_form_css[form_background_color]" type="text" class="color-field" value="<?php echo esc_attr($css['form_background_color']); ?>" /></td>
					<th width="1">Padding</th>
					<td>
						<label>Horizontal <input id="form-horizontal-padding" name="mc4wp_form_css[form_horizontal_padding]" type="number" max="99" min="0" value="<?php echo esc_attr($css['form_horizontal_padding']); ?>"  /></label> &nbsp; 
						<label>Vertical <input id="form-vertical-padding" name="mc4wp_form_css[form_vertical_padding]" type="number" max="99" min="0" value="<?php echo esc_attr($css['form_vertical_padding']); ?>"  /></label>
					</td>
				</tr>
				<tr valign="top">
					<th width="1">Border color</th>
					<td class="nowrap"><input id="form-border-color" name="mc4wp_form_css[form_border_color]" type="text" class="color-field" value="<?php echo esc_attr($css['form_border_color']); ?>" /></td>
					<th width="1">Border width</th>
					<td><input id="form-border-width" name="mc4wp_form_css[form_border_width]" type="number" max="99" min="0" value="<?php echo esc_attr($css['form_border_width']); ?>" /></td>
				</tr>
				<tr valign="top">
					<th width="1">Font color</th>
					<td class="nowrap"><input id="form-font-color" name="mc4wp_form_css[form_font_color]" type="text" class="color-field" value="<?php echo esc_attr($css['form_font_color']); ?>" /></td>
					<th width="1">Text align</th>
					<td>
						<select name="mc4wp_form_css[form_text_align]" id="form-text-align">
							<option value="" <?php selected($css['form_text_align'], ''); ?>>Choose alignment</option>
							<option value="left" <?php selected($css['form_text_align'], 'left'); ?>>Left</option>
							<option value="center" <?php selected($css['form_text_align'], 'center'); ?>>Center</option>
							<option value="right" <?php selected($css['form_text_align'], 'right'); ?>>Right</option>
						</select>
					</td>
				</tr>
			</table>
		</div>

		<h4>Paragraph styles</h4>
		<div>
			<table class="form-table">
				<tr valign="top">
					<th>Font color</th>
					<td class="nowrap"><input id="paragraphs-font-color" name="mc4wp_form_css[paragraphs_font_color]" type="text" class="color-field" value="<?php echo esc_attr($css['paragraphs_font_color']); ?>" /></td>
					<th>Font size</th>
					<td><input id="paragraphs-font-size" name="mc4wp_form_css[paragraphs_font_size]" type="number" max="99" min="0" value="<?php echo esc_attr($css['paragraphs_font_size']); ?>"  /></td>
				</tr>
				<tr valign="top">
					<th>Vertical margin</th>
					<td><input id="paragraphs-vertical-margin" name="mc4wp_form_css[paragraphs_vertical_margin]" type="number" max="99" min="0" value="<?php echo esc_attr($css['paragraphs_vertical_margin']); ?>"  /></td>
					<th></th><td></td>
				</tr>
			</table>
		</div>

		<h4>Label styles</h4>
		<div>
			<table class="form-table">
				<tr valign="top">
					<th>Font color</th>
					<td class="nowrap"><input id="labels-font-color" name="mc4wp_form_css[labels_font_color]" type="text" class="color-field" /></td>
					<th>Font size</th>
					<td><input id="labels-font-size" name="mc4wp_form_css[labels_font_size]" type="number" max="99" min="0" value="<?php echo esc_attr($css['labels_font_size']); ?>"  /></td>
				</tr>
				<tr valign="top">
					<th>Font style?</th>
					<td>
						<select id="labels-font-style" name="mc4wp_form_css[labels_font_style]">
							<option value="" <?php selected($css['labels_font_style'], ''); ?>>Choose font style..</option>
							<option value="normal" <?php selected($css['labels_font_style'], 'normal'); ?>>Normal</option>
							<option value="bold" <?php selected($css['labels_font_style'], 'bold'); ?>>Bold</option>
							<option value="italic" <?php selected($css['labels_font_style'], 'italic'); ?>>Italic</option>
							<option value="bolditalic" <?php selected($css['labels_font_style'], 'bolditalic'); ?>>Bold & Italic</option>
						</select>
					</td>
					<th>Display inline or block?</th>
					<td id="labels-display">
						<label><input type="radio" name="mc4wp_form_css[labels_display]" value="block" <?php checked($css['labels_display'], 'block'); ?> /> Block</label>
						<label><input type="radio" name="mc4wp_form_css[labels_display]" value="inline-block" <?php checked($css['labels_display'], 'inline-block'); ?> /> Inline</label>
					</td>
				</tr>
			</table>
		</div>

		<h4>Field styles</h4>
		<div>
			<table class="form-table">
				<tr valign="top">
					<th>Field width<br /><span class="help">px or %</span></th>
					<td class="nowrap"><input id="fields-width" name="mc4wp_form_css[fields_width]" type="text" value="<?php echo esc_attr($css['fields_width']); ?>" /></td>
					<th>Field height</th>
					<td class="nowrap"><input id="fields-height" name="mc4wp_form_css[fields_height]" min="28" type="number" value="<?php echo esc_attr($css['fields_height']); ?>" /></td>
				</tr>
				<tr valign="top">
					<th>Border color</th>
					<td class="nowrap"><input id="fields-border-color" name="mc4wp_form_css[fields_border_color]" type="text" class="color-field" value="<?php echo esc_attr($css['fields_border_color']); ?>" /></td>
					<th>Border width</th>
					<td><input id="fields-border-width" name="mc4wp_form_css[fields_border_width]" type="number" max="99" min="0" value="<?php echo esc_attr($css['fields_border_width']); ?>" /></td>
				</tr>
				<tr>
					<th>Display inline or block?</th>
					<td id="fields-display">
						<label><input type="radio" name="mc4wp_form_css[fields_display]" value="block" <?php checked($css['fields_display'], 'block'); ?> /> Block</label>
						<label><input type="radio" name="mc4wp_form_css[fields_display]" value="inline-block" <?php checked($css['fields_display'], 'inline-block'); ?> /> Inline</label>
					</td>
				</tr>
			</table>
		</div>

		<h4>Button styles</h4>
		<div>
			<table class="form-table">
				<tr valign="top">
					<th>Button width<br /><span class="help">px or %</span></th>
					<td class="nowrap"><input id="buttons-width" name="mc4wp_form_css[buttons_width]" type="text" value="<?php echo esc_attr($css['buttons_width']); ?>" /></td>
					<th>Button height</th>
					<td class="nowrap"><input id="buttons-height" name="mc4wp_form_css[buttons_height]" min="28" type="number" value="<?php echo esc_attr($css['buttons_height']); ?>" /></td>
				</tr>
				<tr valign="top">
					<th width="1">Background color</th>
					<td class="nowrap"><input id="buttons-background-color" name="mc4wp_form_css[buttons_background_color]" type="text" class="color-field" value="<?php echo esc_attr($css['buttons_background_color']); ?>" /></td>
				</tr>
				<tr valign="top">
					<th>Border color</th>
					<td class="nowrap"><input id="buttons-border-color" name="mc4wp_form_css[buttons_border_color]" type="text" class="color-field" value="<?php echo esc_attr($css['buttons_border_color']); ?>" /></td>
					<th>Border width</th>
					<td><input id="buttons-border-width" name="mc4wp_form_css[buttons_border_width]" type="number" max="99" min="0" value="<?php echo esc_attr($css['buttons_border_width']); ?>" /></td>
				</tr>
				<tr valign="top">
					<th>Font color</th>
					<td class="nowrap"><input id="buttons-font-color" name="mc4wp_form_css[buttons_font_color]" type="text" class="color-field" value="<?php echo esc_attr($css['buttons_font_color']); ?>" /></td>
					<th>Font size</th>
					<td><input id="buttons-font-size" name="mc4wp_form_css[buttons_font_size]" type="number" max="99" min="0" value="<?php echo esc_attr($css['buttons_font_size']); ?>"  /></td>
				</tr>

				<tr>
					<th>Display inline or block?</th>
					<td id="buttons-display">
						<label><input type="radio" name="mc4wp_form_css[buttons_display]" value="block" <?php checked($css['buttons_display'], 'block'); ?> /> Block</label>
						<label><input type="radio" name="mc4wp_form_css[buttons_display]" value="inline-block" <?php checked($css['buttons_display'], 'inline-block'); ?> /> Inline</label>
					</td>
					<td colspan="2" class="desc">Don't wrap your submit button in paragraph tags if you want to display it inline.</td>
				</tr>
			</table>
		</div>

		<h4>Button (hover state) styles</h4>
		<div>
			<table class="form-table">
				<tr valign="top">
					<th width="1">Background color</th>
					<td class="nowrap"><input id="buttons-hover-background-color" name="mc4wp_form_css[buttons_hover_background_color]" type="text" class="color-field" value="<?php echo esc_attr($css['buttons_hover_background_color']); ?>" /></td>
					<th>Border color</th>
					<td class="nowrap"><input id="buttons-hover-border-color" name="mc4wp_form_css[buttons_hover_border_color]" type="text" class="color-field" value="<?php echo esc_attr($css['buttons_hover_border_color']); ?>" /></td>
				</tr>
				<tr valign="top">
					<th>Font color</th>
					<td class="nowrap"><input id="buttons-hover-font-color" name="mc4wp_form_css[buttons_hover_font_color]" type="text" class="color-field" value="<?php echo esc_attr($css['buttons_hover_font_color']); ?>" /></td>
					<th></th>
					<td></td>
				</tr>
			</table>
		</div>

		<h4>Error and success messages</h4>
		<div>
			<table class="form-table">
				<tr valign="top">
					<th>Success font color</th>
					<td class="nowrap"><input id="messages-font-color-success" name="mc4wp_form_css[messages_font_color_success]" type="text" class="color-field" value="<?php echo esc_attr($css['messages_font_color_success']); ?>" /></td>
					<th>Error font color</th>
					<td class="nowrap"><input id="messages-font-color-error" name="mc4wp_form_css[messages_font_color_error]" type="text" class="color-field" value="<?php echo esc_attr($css['messages_font_color_error']); ?>" /></td>
				</tr>
			</table>
		</div>

		<h4>Advanced</h4>
		<div>
			<table class="form-table">
				<tr valign="top">
					<th><label>CSS Selector Prefix</label></th>
					<td><input type="text" name="mc4wp_form_css[selector_prefix]" value="<?php echo esc_attr($css['selector_prefix']); ?>" placeholder="Example: #content" /></td>
					<td class="desc">Use this to create a more specific (and thus more "important") CSS selector.</td>
				</tr>
				<tr>
					<th colspan="1">Manual CSS</th><td colspan="2" class="desc">The CSS rules you enter here will be appended to the custom stylesheet.</td>
				</tr>
				<tr>
					<td colspan="3">
						<textarea class="widefat" rows="6" cols="50" name="mc4wp_form_css[manual]" id="mc4wp-css-textarea"><?php if(!empty($css['manual'])) { echo esc_textarea($css['manual']); } ?></textarea>
					</td>
				</tr>
	</table>
		
		</div>
	</div>

	<?php submit_button(__("Build CSS File")); ?>


		<?php 
			$tips = array(
				"Tip: use as few CSS settings as possible to reach the look you desire. In other words, leave as many options empty as possible.", 
				"Tip: make sure your form mark-up is compatible with the look you desire. Don't wrap your field in paragraph elements if you want a single-line form."
			);	
			echo '<p class="help">'. $tips[array_rand($tips)] . '</p>';
		?>
	</form>
</div>
<div class="mc4wp-column mc4wp-column-right" style="width:42.5%;">
	<h3>Form preview</h3>
	<p>
		<?php if(!$forms) { ?>
			Please <a href="<?php echo admin_url('post-new.php?post_type=mc4wp-form'); ?>">create at least 1 form</a> first.
		<?php } else { ?>
			<label for="mc4wp-css-preview-form">Select form for preview</label>
			<select id="mc4wp-css-preview-form">
				<?php foreach($forms as $form) { ?>
					<option value="<?php echo $form->ID; ?>"><?php echo (!empty($form->post_title)) ? $form->post_title : '(no title, ID '. $form->ID .')'; ?></option>
				<?php } ?>
			</select>
		<?php } ?>
	</p>
	<iframe id="mc4wp-css-preview" data-src-url="<?php echo site_url('?form_id={form_id}&_mc4wp_css_preview=1'); ?>" src="<?php echo site_url("?form_id={$form_id}&_mc4wp_css_preview=1"); ?>"></iframe>
</div>
<br class="clear" />