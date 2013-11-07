<?php
$base_color = $_GET['custom-color'];

$hex = str_replace( '#', '', $base_color );
if ( strlen( $hex ) == 3 ) {
	$hex = str_repeat( substr( $hex, 0, 1 ), 2 ).str_repeat( substr( $hex, 1, 1 ), 2 ).str_repeat( substr( $hex, 2, 1 ), 2 );
}

// Get decimal values
$r = hexdec( substr( $hex, 0, 2 ) );
$g = hexdec( substr( $hex, 2, 2 ) );
$b = hexdec( substr( $hex, 4, 2 ) );

// calculate font color
$avg = (($r + $g + $b) / 3);
$font_color = ($avg > 170) ? 'black' : 'white';

// calculate darker color
$r = max( 0, min( 255, $r - 20 ) );
$g = max( 0, min( 255, $g - 20 ) );
$b = max( 0, min( 255, $b - 20 ) );

$r_hex = str_pad( dechex( $r ), 2, '0', STR_PAD_LEFT );
$g_hex = str_pad( dechex( $g ), 2, '0', STR_PAD_LEFT );
$b_hex = str_pad( dechex( $b ), 2, '0', STR_PAD_LEFT );

$darker_color = '#'.$r_hex.$g_hex.$b_hex;

// calculate darkest color
$r = max( 0, min( 255, $r - 20 ) );
$g = max( 0, min( 255, $g - 20 ) );
$b = max( 0, min( 255, $b - 20 ) );

$r_hex = str_pad( dechex( $r ), 2, '0', STR_PAD_LEFT );
$g_hex = str_pad( dechex( $g ), 2, '0', STR_PAD_LEFT );
$b_hex = str_pad( dechex( $b ), 2, '0', STR_PAD_LEFT );

$darkest_color = '#'.$r_hex.$g_hex.$b_hex;
?>

.mc4wp-form input[type="submit"], .mc4wp-form button {
	color: <?php echo $font_color; ?>;
  	background-color: <?php echo $base_color; ?>;
  	border-color: <?php echo $darker_color; ?>;
}

.mc4wp-form input[type="submit"]:hover, .mc4wp-form button:hover,
.mc4wp-form input[type="submit"]:active, .mc4wp-form button:active,
.mc4wp-form input[type="submit"]:focus, .mc4wp-form button:focus {
	color: <?php echo $font_color; ?>;
  	background-color: <?php echo $darker_color; ?>;
  	border-color: <?php echo $darkest_color; ?>;
}

.mc4wp-form input[type="text"]:focus,
.mc4wp-form input[type="email"]:focus,
.mc4wp-form input[type="tel"]:focus,
.mc4wp-form input[type="url"]:focus,
.mc4wp-form textarea:focus,
.mc4wp-form select:focus {
	border-color: <?php echo $base_color; ?>;
}
