<?php 
// Set headers to serve CSS and encourage browser caching
$expires = 60 * 60 * 34 * 3; // cache time: 3 days
header('Content-Type: text/css; charset: UTF-8'); 
header("Cache-Control: public, max-age=" . $expires);
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');

// load checkbox reset
if(isset($_GET['checkbox'])) {
	readfile(dirname(__FILE__) . '/checkbox.css');
}

// load form reset
if(isset($_GET['form'])) {
	readfile(dirname(__FILE__) . '/form-reset.css');
}

// should we load a form theme?
if(isset($_GET['form-theme'])) {
	$form_theme = $_GET['form-theme'];

	// load theme base file
	readfile(dirname(__FILE__) . '/form-theme-base.css');

	// only load themes we actually have
	if(in_array($form_theme, array('blue', 'green', 'dark', 'light', 'red'))) {
		readfile('form-theme-'. $form_theme .'.css');
	} elseif(isset($_GET['custom-color'])) {
		include_once dirname(__FILE__) . '/form-theme-custom.php';
	}

}


exit;