<?php /*
Plugin Name: WP PIE
Plugin URI: http://themereactor.com/plugin/pie/
Description: Enables support of certain CSS3 styles in IE by automatically searching the primary stylesheet and applying the PIE (http://css3pie.com/) behaviour to the appropriate styles. This results in the enqueuing of a seperate stylesheet.
Author: ThemeREACTOR
Version: 1.2
Author URI: http://themereactor.com/
License: GPL2
*/
/*  Copyright 2012  ThemeREACTOR  (email : plugins@themereactor.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
-----------------------------------------------
The Stylesheets we want to which we want to 
apply PIE. To add an additional stylesheet add
an item to the array in the format below.
-----------------------------------------------
*/
$stylesheets = array (
	array(
		'name' => 'wppie', 
		'url' => get_stylesheet_uri(), //The current stylesheet. That of the child theme if such is the case.
	),
/*	array(
		'name' => 'parent',
		'url' => get_template_directory_uri().'/style.css', //The parent themes stylesheet, should one exist.
	),
	array(
		'name' => 'filename', //no spaces, url safe
		'url' => 'http://yoursite.com/style.css', //full url to stylesheet. 
	) */
);
/*
-----------------------------------------------
Enqueue Styles
-----------------------------------------------
*/
add_action('wp_enqueue_scripts', 'wpp_enqueue');
function wpp_enqueue() {
	foreach( $GLOBALS['stylesheets'] as $value) {
		if ($value['name'] == '') {
			$path_parts = pathinfo($value['url']);
			$filename = $path_parts['filename'];
		} elseif($value['name'] != '') {
			$filename = $value['name'];
		}
		wp_enqueue_style( $value['name'], get_bloginfo('wpurl').'/'.$filename.'.css', '1.0' );
	}
}
/*
-----------------------------------------------
The admin page for the backend.
-----------------------------------------------
*/
function wpp_menu_page() { ?>
	<div class="wrap">
		<h2 class="title">WP PIE</h2>
		<hr />
		<h3>An automatic application of CSS PIE (<a href="http://css3pie.com/">http://css3pie.com/</a>)</h3>
		<p>By loading this page, you've automatically searched the main stylesheet for any instances of styles using border-radius, box-shadow, and linear-gradient and created a new stylesheet that will now be enqueued on every page which applies the PIE behaviour file. And that's it, you're done!</p>
		<p>If you modify the stylesheet and want to update the PIE support simply reload this page.</p>
		<?php applypie(); ?>
	</div><!-- #wrap -->
<?php }
/*
-----------------------------------------------
add the admin page to the menu
-----------------------------------------------
*/
function wpp_create_menu(){
	add_menu_page ('WP PIE', 'WP PIE', "install_plugins", 'wppie', 'wpp_menu_page');
}

function applypie() {
	foreach( $GLOBALS['stylesheets'] as $value) {
		wppie($value['url'], $value['name']);
	}
}
function wppie($url, $name = '') {
	$contents = file_get_contents($url);
	preg_match_all( '#/\*(.+)\{#Us', $contents, $thefirst );
	preg_match_all( '/\}(.+)\{/Us', $contents, $styles, PREG_PATTERN_ORDER );
	preg_match_all( '/\{(.+)\}/Us', $contents, $attrib, PREG_PATTERN_ORDER );
	$match = array(
		'#\/\*(.+)\*\/#s',
		'/{/'
	);
	$first = preg_replace( $match, '', $thefirst[0][0]);
	$i = 0;
	while($i != count($attrib[1])) {
		if ( $i == 0 ) {
			$combined['styles'][0] = $first;
		} else {
			$combined['styles'][$i] = $styles[1][$i-1];
		}
		if (strlen(strstr($attrib[1][$i], 'border-radius' ) )>0) {
			$attrib[1][$i] = 'behavior: url('.site_url('/wp-content/plugins/wp-pie/pie/PIE.htc').');';
		} elseif (strlen(strstr($attrib[1][$i], 'box-shadow' ) )>0) {
			$attrib[1][$i] = 'behavior: url('.site_url('/wp-content/plugins/wp-pie/pie/PIE.htc').');';
		} elseif (strlen(strstr($attrib[1][$i], '-pie-background' ) )>0) {
			$attrib[1][$i] = 'behavior: url('.site_url('/wp-content/plugins/wp-pie/pie/PIE.htc').');';
		}
		$combined['values'][$i] = $attrib[1][$i];
		$i+=1;
	}
	$data = '';
	$x = 0;
	while ($x != count($combined['values']) ) {
		if ( $combined['values'][$x] == 'behavior: url('.site_url('/wp-content/plugins/wp-pie/pie/PIE.htc').');' ) {
			$data .= $combined['styles'][$x].' {'."\n".$combined['values'][$x]."\n".'}'."\n";
		}
		$x += 1;
	}
	if ($name == '') {
		$path_parts = pathinfo($url);
		$filename = $path_parts['filename'];
	} elseif($name != '') {
		$filename = $name;
	}
	file_put_contents('../'.$filename.'.css', $data);
}
if(is_admin()){
	add_action("admin_menu", "wpp_create_menu");
}