<?php defined('ABSPATH') OR exit; ?>

/* form container */
<?php echo $selector_prefix; ?>.mc4wp-form {
	display: block; border-color: transparent;
	<?php
	if(!empty($form_border_color)) { echo "border-color: {$form_border_color} !important;"; } else { echo 'border-color: transparent;'; }
	if(!empty($form_border_width)) { echo "border-width: {$form_border_width}px; border-style: solid;";  }
	if(!empty($form_horizontal_padding)) { echo "padding-left: {$form_horizontal_padding}px; padding-right: {$form_horizontal_padding}px;"; }
	if(!empty($form_vertical_padding)) {  echo "padding-top: {$form_vertical_padding}px; padding-bottom: {$form_vertical_padding}px;"; }
	if(!empty($form_background_color)) { echo "background: {$form_background_color} !important;"; }
	if(!empty($form_font_color)) { echo "color: {$form_font_color} !important; "; }
	if(!empty($form_text_align)) { echo "text-align: {$form_text_align};"; }
	?>
}

/* paragraphs */
<?php echo $selector_prefix; ?>.mc4wp-form p {
	<?php
	if(!empty($paragraphs_font_size)) { echo "font-size: {$paragraphs_font_size}px;"; }
	if(!empty($paragraphs_font_color)) { echo "color: {$paragraphs_font_color} !important;"; }
	if(!empty($paragraphs_vertical_margin)) { echo "margin-top: {$paragraphs_vertical_margin}px; margin-bottom: {$paragraphs_vertical_margin}px;"; }
	?>
}

/* labels */
<?php echo $selector_prefix; ?>.mc4wp-form label { 
	margin-bottom: 6px;
<?php
	if(!empty($labels_font_color)) { echo "color: {$labels_font_color};"; }
	if(!empty($labels_font_style)) { 
		if($labels_font_style == 'italic' || $labels_font_style == 'bolditalic') {
			echo 'font-style: italic;';
		} else {
			echo 'font-style: normal;';
		}

		if($labels_font_style == 'bold' || $labels_font_style == 'bolditalic') {
			echo 'font-weight: bold;';
		} else {
			echo 'font-weight: normal;';
		}
	}
	if(!empty($labels_font_size)) { echo "font-size: {$labels_font_size}px;"; }
	if(!empty($labels_display)) { echo "display: {$labels_display};"; }
	if(!empty($labels_vertical_margin)) { echo "margin-top: {$labels_vertical_margin}px; margin-bottom: {$labels_vertical_margin}px;"; }
	if(!empty($labels_horizontal_margin)) { echo "margin-left: {$labels_horizontal_margin}px; margin-right: {$labels_horizontal_margin}px;"; }
?>
}

/* fields */
<?php echo $selector_prefix; ?>.mc4wp-form input[type="text"], <?php echo $selector_prefix; ?>.mc4wp-form input[type="email"], <?php echo $selector_prefix; ?>.mc4wp-form input[type="url"], 
<?php echo $selector_prefix; ?>.mc4wp-form input[type="tel"], <?php echo $selector_prefix; ?>.mc4wp-form input[type="number"], <?php echo $selector_prefix; ?>.mc4wp-form input[type="date"], <?php echo $selector_prefix; ?>.mc4wp-form select, <?php echo $selector_prefix; ?>.mc4wp-form textarea { 
	box-sizing:border-box; box-sizing: border-box; -moz-box-sizing: border-box;
	padding:6px 12px; margin-bottom: 6px;
<?php
	if(!empty($fields_width)) { echo "width: {$fields_width};"; }
	if(!empty($fields_height)) { echo "line-height: ". ($fields_height - 12) . "px; height: {$fields_height}px;"; }
	if(!empty($fields_border_color)) { echo "border-color: {$fields_border_color} !important; "; }
	if(!empty($fields_border_width)) { echo "border-width: {$fields_border_width}px; border-style:solid;"; }
	if(!empty($fields_display)) { echo "display: {$fields_display};"; }
?>
}

/* buttons */
<?php echo $selector_prefix; ?>.mc4wp-form input[type="submit"], <?php echo $selector_prefix; ?>.mc4wp-form button, <?php echo $selector_prefix; ?>.mc4wp-form input[type="button"], <?php echo $selector_prefix; ?>.mc4wp-form input[type="reset"] {
	text-shadow:none; box-sizing:border-box; box-sizing: border-box; -moz-box-sizing: border-box;
	padding:6px 12px; cursor: pointer; text-align:center; 
<?php
	if(!empty($buttons_background_color)) { echo "background:none; filter: none; background: {$buttons_background_color} !important;"; }
	if(!empty($buttons_font_color)) { echo "color: {$buttons_font_color} !important;"; }
	if(!empty($buttons_font_size)) { echo "font-size: {$buttons_font_size}px;"; }
	if(!empty($buttons_border_color)) { echo "border-color: {$buttons_border_color} !important;"; }
	if(!empty($buttons_border_width)) { echo "border-width: {$buttons_border_width}px; border-style: solid;"; }
	if(!empty($buttons_width)) { echo "width: {$buttons_width};"; }
	if(!empty($buttons_height)) { echo "height: {$buttons_height}px;"; }
	if(!empty($buttons_display)) { echo "display: {$buttons_display};"; }
?>
}

<?php echo $selector_prefix; ?>.mc4wp-form input[type="submit"]:hover, <?php echo $selector_prefix; ?>.mc4wp-form button:hover, <?php echo $selector_prefix; ?>.mc4wp-form input[type="button"]:hover, <?php echo $selector_prefix; ?>.mc4wp-form input[type="reset"]:hover {
<?php
	if(!empty($buttons_hover_background_color)) { echo "background:none; filter: none; background: {$buttons_hover_background_color} !important;"; }
	if(!empty($buttons_hover_font_color)) { echo "color: {$buttons_hover_font_color} !important;"; }
	if(!empty($buttons_hover_border_color)) { echo "border-color: {$buttons_hover_border_color} !important;"; }
?>
}

/* messages */
<?php echo $selector_prefix; ?>.mc4wp-form .mc4wp-alert{ }
<?php echo $selector_prefix; ?>.mc4wp-form .mc4wp-success{ 
	<?php
	if(!empty($messages_font_color_success)) { echo "color: $messages_font_color_success;"; }
	?>
}
<?php echo $selector_prefix; ?>.mc4wp-form .mc4wp-error{
	<?php
	if(!empty($messages_font_color_error)) { echo "color: $messages_font_color_error;"; }
	?>
}

<?php echo $manual; ?>