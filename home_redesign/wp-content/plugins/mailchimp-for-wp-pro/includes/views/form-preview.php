<?php
define('DONOTCACHEPAGE', true);
define('DONOTMINIFY', true);
define('DONOTCDN', true);
?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<link type="text/css" rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" />
	<?php wp_head(); ?>
	
	<style type="text/css">
		body{ padding:20px; background:white; }
		p.mc4wp-edit-link{ display:none; }
		#blackbox-web-debug, #wpadminbar{ display:none !important; }
	</style>

	<style type="text/css" id="custom-css"></style>
	<!--[if IE]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>
<body style="min-width: auto !important; max-width:1000px;">
	<?php mc4wp_form($_GET['form_id']); ?>
	<?php wp_footer(); ?>
</body>
</html>