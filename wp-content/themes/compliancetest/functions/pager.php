<?php

$javascript = <<<EOT

<script type="text/javascript">
function hexset(id, hexvalue)
{
	document.getElementById(id).value = hexvalue;
}

function checkstyle()
{
	if (document.getElementById('style1').value == 'none' ) {
		document.getElementById('borderdiv1').style.display = 'none';
		document.getElementById('bgdiv1').style.display = 'none';
		document.getElementById('bgdiv2').style.display = 'none';
		document.getElementById('trdiv2').style.display = '';
		document.getElementById('trdiv1').style.display = '';		
	} else if(document.getElementById('style1').value == 'custom' ) {
		document.getElementById('borderdiv1').style.display = 'none';
		document.getElementById('bgdiv1').style.display = 'none';
		document.getElementById('bgdiv2').style.display = 'none';
		document.getElementById('trdiv1').style.display = 'none';
		document.getElementById('trdiv2').style.display = 'none';		
	} else {
		document.getElementById('borderdiv1').style.display = 'block';
		document.getElementById('bgdiv1').style.display = 'block';
		document.getElementById('bgdiv2').style.display = 'block';
		document.getElementById('trdiv2').style.display = '';
		document.getElementById('trdiv1').style.display = '';
		}
}

function checktitle(is_true){
	if(is_true){
		document.getElementById('Title_Template').style.visibility = 'visible';
	} else{
		document.getElementById('Title_Template').style.visibility = 'hidden';
	}
}

function checkexcerpt(is_true) {
	if(is_true){
		document.getElementById('Excerpt_Option').style.visibility = 'visible';
	} else {
		document.getElementById('Excerpt_Option').style.visibility = 'hidden';
	}
}

function checkinstructions(is_one) {
	if(is_one) {
		document.getElementById('instruction1').style.display = 'block';
		document.getElementById('instruction2').style.display = 'none';
	} else {
		document.getElementById('instruction1').style.display = 'none';
		document.getElementById('instruction2').style.display = 'block';	
	}
}
</script>

EOT;
//Add Pager options to database
add_option('SEOPager_styles', 'boxed');
add_option('SEOPager_prelabel', '&laquo; Previous ');
add_option('SEOPager_nextlabel', 'Next &raquo;');
add_option('SEOPager_preallow', 'disallow');
add_option('SEOPager_nextallow', 'disallow');
add_option('SEOPager_linkallow', 'allow');
add_option('SEOPager_bordercolor', 'black');
add_option('SEOPager_bgcolor1', 'white');
add_option('SEOPager_bgcolor2', 'white');
add_option('SEOPager_fgcolor1', '#67A7C4');
add_option('SEOPager_fgcolor2', '#7F7F7F');
add_option('SEOPager_auto', 'disallow');
add_option('SEOPager_title', 'disallow');
add_option('SEOPager_titletext', '%wp_title% &mdash; Page %page_no%');
add_option('SEOPager_excerpt', 'disallow');
add_option('SEOPager_excerpt_pre', 'Archived; click post to view.<br />
<b>Excerpt:</b>');
add_option('SEOPager_excerpt_post', '...');
add_option('SEOPager_excerpt_length', '25%');
add_option('SEOPager_excerpt_first_page', 'disallow');
add_option('SEOPager_ellipsis', '...');
add_option('SEOPager_num_of_pages', '10');



// Redirect out of bounds page to max page
add_action('template_redirect', 'redirect_NE_pages');
function redirect_NE_pages () {
	if(is_404() && get_option('permalink_structure')){
		$current_page = get_query_var('paged');
		$page_num = get_query_var('page_id');
		$post_id = get_query_var('p');
		$limit = get_option('posts_per_page');
		global $wp_query;
		$numposts = $wp_query->found_posts;
		$numpages = ceil($numposts/$limit);

		if ($current_page > $numpages && !$page_num && !$post_id){
			$url = $_SERVER['REQUEST_URI'] ;
			$url = preg_replace('#/page/([0-9]+)/?$#',"/page/$numpages", $url);			
			header ("Location: $url");
			exit();
		}
	}
}

// Ellipsize posts on pages for excerpt posts option
if(get_option('SEOPager_excerpt') == 'allow') {
	add_filter('the_posts', 'post_excerpt', 10);
	
	function post_excerpt($posts){			
		$current_page = get_query_var('paged');
		if(get_option('SEOPager_excerpt_first_page') == 'allow'){
			$catid = is_category();
			$month = is_date() ;
			$istag  =  is_tag();			
			
		} else{
			$catid = false;
			$month = false;
			$istag = false;
		}

		if (($current_page == '' || $current_page == '1') && !$catid && !$month & !$istag){

			return $posts;			
		} else{
			$pre = get_option('SEOPager_excerpt_pre') . ' ';
			$posttext = get_option('SEOPager_excerpt_post');
			
			
			foreach( $posts as $key => $post ){
				$content = strip_tags($post->post_content);
					if(substr(get_option('SEOPager_excerpt_length'),0,1) == '.' ){
						$content = $pre . 
						substr($content, 0, strlen($content) * (float)(get_option('SEOPager_excerpt_length'))) 
						.$posttext;
					}
					else{							
						if(sizeof(explode(' ', $content)) > get_option('SEOPager_excerpt_length')){
							$content = $pre . implode(' ',array_slice(explode(' ', $content),0,get_option('SEOPager_excerpt_length'))) . $posttext;
 						}
 						else{
 							$content = $post->post_content;
 						}
					}
				$posts[$key]->post_content = strtr($content, "\r\n", "  ");
				}
			return $posts;
		}
	}
}

// Add pager to loop_end for automatic pager option
if(get_option('SEOPager_auto') == 'allow'){
	add_action('loop_end', 'Pager_hook');
	function Pager_hook(){
		if (!is_feed() && !is_admin()) SEO_pager();
	}
}

// Add title information for title template option
if(get_option('SEOPager_title') == 'allow'){
	add_filter('wp_title', 'set_SEO_title');
	
	function set_SEO_title($title) {
		global $paged;
		if($paged){
			$titleText = get_option('SEOPager_titletext');
			if($titleText ){
				$titleString = preg_replace('#%wp_title%#', $title, $titleText);
				$titleString = preg_replace('#%page_no%#', $paged, $titleString);
				return $titleString;
			} else {
				return $title . ' : Page ' . $paged;
			}
		} else{
			return $title;
		}
	}
}

// Used to generate colors for Pager Appearence
function SEOPager_generateColor($selected)
{
	$colorsarray = array(
		'aqua' => '00ffff',
		'black' => '000000',
		'blue' => '0000ff',
		'fuchsia' => 'ff00ff',
		'green' => '008000',
		'gray' => '808080',
		'lime' => '00ff00',
		'maroon' => '800000',
		'navy' => '000080',
		'olive' => '808000',
		'purple' => '800080',
		'red' => 'ff0000',
		'silver' => 'c0c0c0',
		'teal' => '008080',
		'white' => 'ffffff',
		'yellow' => 'ffff00',
	);	
	
	$selected = strtolower(trim($selected));
	
	if(substr($selected,0,1) == '#') {
		$selected = substr($selected,1);
	}
	 if(in_array($selected, array_keys($colorsarray))){

		foreach ($colorsarray as $name => $hex){
			$hex = '#' . $hex;
			if($selected == $name){
				echo '<option selected="selected" value="' . $hex . '" >' 
				. ucfirst($name) . '</option>'; 
			} else{
				echo '<option value="' . $hex . '" >' . ucfirst($name) . '</option>'; 
			}
		}
	} else if(in_array($selected, array_values($colorsarray))){
		$selected = '#' . $selected;
		foreach ($colorsarray as $name => $hex){						
			$hex = '#' . $hex;
			if($selected == $hex){
				echo '<option selected="selected" value="' . $hex . '" >' 
				. ucfirst($name) . '</option>'; 
			} else{
				echo '<option value="' . $hex . '" >' . ucfirst($name) . '</option>'; 
			}
		}
	} else{
		echo '<option selected="selected" value="' 
		. get_option('SEOPager_bordercolor'). '" >Custom</option>'; 

		foreach ($colorsarray as $name => $hex){						
			$hex = '#' . $hex;
			echo '<option value="' . $hex . '" >' . ucfirst($name) . '</option>'; 
		}
	}
}


function SEOPager_admin_menu() {
	add_submenu_page( 'option_tree',
	'Pager',
	'Pager Settings',
	'administrator',
	'options-genera.php',
	'SEOPager_submenu'
	);
	}
// Add admin menu
add_action('admin_menu', 'SEOPager_admin_menu');
// Admin Menu for Pager configuration.
function SEOPager_submenu() {
	echo '<link rel="stylesheet" type="text/css" media="screen" href="' 
	. get_bloginfo('wpurl') . '/wp-content/plugins/wordpress-seo-pager.php?css=1" />';
	
	// Default options
	if (isset($_REQUEST['restore']) && $_REQUEST['restore']) {
		update_option('SEOPager_styles', 'boxed');
		update_option('SEOPager_prelabel', '&laquo; Previous Page');
		update_option('SEOPager_nextlabel', 'Next Page &raquo;');
		update_option('SEOPager_preallow', 'disallow');
		update_option('SEOPager_nextallow', 'disallow');
		update_option('SEOPager_linkallow', 'allow');
		update_option('SEOPager_bordercolor', 'black');
		update_option('SEOPager_bgcolor1', 'white');
		update_option('SEOPager_bgcolor2', 'white');
		update_option('SEOPager_fgcolor1', '#67A7C4');
		update_option('SEOPager_fgcolor2', '#7F7F7F');
		update_option('SEOPager_auto', 'disallow');
		update_option('SEOPager_title', 'disallow');
		update_option('SEOPager_titletext', '%wp_title% &mdash; Page %page_no%');
		update_option('SEOPager_excerpt', 'disallow');
		update_option('SEOPager_excerpt_pre', 'Archived; click post to view.
		<br /><b>Excerpt:</b>');
		update_option('SEOPager_excerpt_post', '...');
		update_option('SEOPager_excerpt_length', '25%');

		update_option('SEOPager_ellipse', '...');
		update_option('SEOPager_excerpt_first_page', 'disallow');
		update_option('SEOPager_num_of_pages', '10');
		
		echo '<div class="updated fade">
		<p><strong>Restored all settings to defaults.</strong></p>
		</div>';
	} 
	// Save options
	else if (isset($_REQUEST['save']) && $_REQUEST['save'] ) {
		update_option('SEOPager_styles', isset($_REQUEST['style']) && $_REQUEST['style']);
		update_option('SEOPager_prelabel', $_REQUEST['prelabel']);
		update_option('SEOPager_nextlabel', $_REQUEST['nextlabel']);
		update_option('SEOPager_preallow', isset($_REQUEST['preallow']) && $_REQUEST['preallow']);
		update_option('SEOPager_nextallow', isset($_REQUEST['nextallow']) && $_REQUEST['nextallow']);
		update_option('SEOPager_linkallow', isset($_REQUEST['SEOlink']) && $_REQUEST['SEOlink']);
		update_option('SEOPager_bordercolor', isset($_REQUEST['bordercolor']) && $_REQUEST['bordercolor']);
		update_option('SEOPager_bgcolor1', isset($_REQUEST['bgcolor1']) && $_REQUEST['bgcolor1']);
		update_option('SEOPager_bgcolor2', isset($_REQUEST['bgcolor2']) && $_REQUEST['bgcolor2']);
		update_option('SEOPager_fgcolor1', isset($_REQUEST['fgcolor1']) && $_REQUEST['fgcolor1']);
		update_option('SEOPager_fgcolor2', isset($_REQUEST['fgcolor2']) && $_REQUEST['fgcolor2']);
		update_option('SEOPager_auto', isset($_REQUEST['PagerON']) && $_REQUEST['PagerON']);
		update_option('SEOPager_title', $_REQUEST['Pagertitle']);
		update_option('SEOPager_titletext', $_REQUEST['titletext']);
		update_option('SEOPager_excerpt', $_REQUEST['excerptOn']);
		update_option('SEOPager_excerpt_pre', $_REQUEST['excerptpre']);
		update_option('SEOPager_excerpt_post', $_REQUEST['excerptpost']);
		update_option('SEOPager_excerpt_length', $_REQUEST['excerptlength']);
		
		update_option('SEOPager_ellipsis', $_REQUEST['ellipsis']);
		update_option('SEOPager_excerpt_first_page', $_REQUEST['excerptallow']);
		update_option('SEOPager_num_of_pages', $_REQUEST['numpagesmiddle']);
				
		echo '<div class="updated fade">
		<p><strong>Settings saved.</strong></p>
		</div>';
	}
	
 global $javascript;		
 echo $javascript;
 
 
?>
<form name="seopager" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" >

<div class="wrap">
	<h2>Pager Options</h2>
	
	<table border="0" cellpadding="3" cellspacing="3">
	<tr>
		<td scope="row" valign="middle">
			Previous Label:
		</td>
		<td> 
			<input type="text" name="prelabel" 
			value="<?php echo htmlspecialchars(get_option('SEOPager_prelabel'));?>" size="40">
			
		<!--	<table style="valign: bottom; display:inline;">
			<tr>
			<td class="radiobuttontable">
			<input type="checkbox" id="preallow1" name="preallow" value="allow" 
			<?php echo (get_option('SEOPager_preallow') == 'allow')?'checked="checked"':''?>>
			</td>
			<td class="radiobuttontable"><label for="preallow1">
			 Show Previous Label on first page.</label></td>
			</tr>
			</table> -->
		</td>
	</tr>
	<tr>
		<td valign="middle">
			Next Label:
		</td>
		<td> 
			<input type="text" name="nextlabel" 
			value="<?php echo htmlspecialchars(get_option('SEOPager_nextlabel')); ?>" size="40">
	<!--	<table style="valign: bottom; display:inline;">
			<tr>
			<td class="radiobuttontable"><input type="checkbox" id="nextallow1" 
			name="nextallow" value="allow" 
			<?php echo (get_option('SEOPager_nextallow') == 'allow') 
			? 'checked="checked"' :''?>>
			</td>
			<td class="radiobuttontable">
			<label for="nextallow1">Show Next Label on last page.</label>
			</td>
			</tr>
			</table> -->
		</td>
	</tr>
	<tr>
		<td scope="row" valign="middle" >
			Pager Ellipsis String:
		</td>
		<td> 
			<input type="text" name="ellipsis" 
			value="<?php echo htmlspecialchars(get_option('SEOPager_ellipsis')); ?>" size="10">
		</td>
	</tr>


	<tr>
		<td scope="row" valign="middle">
			Set Number of Pages in Middle: 
		</td>
		<td>
			<select name="numpagesmiddle">
			<?php
			for($i=5; $i < 11; $i++){
				if(get_option('SEOPager_num_of_pages') == $i){
					echo "<option selected=\"selected\" value=\"$i\">$i</option>";
				}else{
					echo "<option value=\"$i\">$i</option>";
				}
			}
			?>			
			</select>
		</td>
		<td >&nbsp;</td>
	</tr>	
	
	</table>
	<br /><br />


<table>
		<tr>		
		<td >
			<span class="submit"><input class="button-primary" name="save" value="Save Changes" 
			type="submit" /></span>

			<span class="submit"><input name="restore" value="Restore Defaults" 
			type="submit"/></span>
		</td>
		</tr>
</table>

</div>
</form>
<br />
<?php
}


// Generates Pager for blog.
function SEO_pager($seperator = ' ', $after_previous = '&nbsp;&nbsp;', 
$before_next = '&nbsp;&nbsp;', $prelabel='&laquo; Previous Page', 
$nxtlabel='Next Page &raquo;', $ellipsis_text_left = '...', 
$ellipsis_text_right = '...')
{	
		
	global $posts_per_page, $paged, $wp_query;

	$pager_date_format = get_option('date_format');
	
	if(get_option('SEOPager_prelabel') != ''){
		$prelabel = get_option('SEOPager_prelabel');
	}
	if(get_option('SEOPager_nextlabel') != ''){
		$nxtlabel = get_option('SEOPager_nextlabel');
	}

	$ellipsis_text_left = $ellipsis_text_right = get_option('SEOPager_ellipsis');
	
	$numposts = $wp_query->found_posts;
	$max_num_pages = ceil($numposts / $posts_per_page);

	$num_begin = 2;
	$num_end = 2;
	$center = get_option('SEOPager_num_of_pages');	
	$pages_in_center = get_option('SEOPager_num_of_pages');
	
	if($paged ==''){
		$paged = 1;
	}
	
	if(is_category()){
 		$catid = get_query_var('cat');
		$p = get_posts("numberposts=300&category=$catid"); 
		$p = query_posts("showposts=-1&cat=$catid"); 
	} else if(is_archive()){
		$monthnum = get_the_time('m');
		$year = get_the_time('Y');
		$p = query_posts("showposts=-1&year=$year&monthnum=$monthnum"); 
	} else{
		$p = query_posts("showposts=-1");
	}
	
	$current_page = $paged;
	list($start,$end) = get_begin_and_end($current_page, $max_num_pages, $center, 
	$pages_in_center, $num_begin, $num_end);
	
		
	if ($max_num_pages > 1) {
		echo '<!--http://www.seoegghead.com/software/wordpress-seo-pager.seo-->';

		echo '<div class="pagerbox">';			
		
		if($current_page == 1 ){
			if(get_option('SEOPager_preallow') == 'allow' && $max_num_pages > 1){
				echo '<span class="current" href="" > ' . $prelabel . '</span>';
				echo  $after_previous;
			}
		} else{
			$title = 'title="' . date($pager_date_format, 
			strtotime($p[(($current_page-2) * $posts_per_page )]->post_date))  
			. '"';
			echo '<a '.$title. ' href="'.get_pagenum_link(($current_page - 1))
			.'" >'.$prelabel . '</a>';
			echo  $after_previous;
		}
		
		if($start > $num_begin){
			for ($cnt = 1; $cnt <=  $num_begin; $cnt++) {
					$begin_link = ''; $end_link = '';
				$title = 'title="' .date($pager_date_format, 
				strtotime($p[(($cnt-1) * $posts_per_page )]->post_date))  . '"';
				$x[] =  '<a ' . $title . ' href="' . get_pagenum_link($cnt) 
				. '" >' . $cnt . '</a>';
			}
			if(($start - 1)  != $num_begin)
				$x[] = $ellipsis_text_left;
		}	
		
		for ($cnt = $start; $cnt <= $end; $cnt++) {
			if ( $current_page == $cnt) {
				$class = '  class="current">';
				$x[] = '<a class="current">' . $cnt . '</a>';	

			} else {
				$title = 'title="' .date($pager_date_format, 
				strtotime($p[(($cnt-1) * isset($posts_per_page) && $posts_per_page)]->post_date))  . '"';
				$x[] = '<a ' . $title . ' href="' . 
				get_pagenum_link($cnt) . '"  >'. $cnt . '</a>';
			}
			
		}
		if($end < ($max_num_pages - $num_end ) ){
			
			$x[] = $ellipsis_text_right ;
			
			for ($cnt =($max_num_pages - $num_end)+1; $cnt <= $max_num_pages; $cnt++){
				$title = 'title="' .date($pager_date_format, 
				strtotime($p[(($cnt-1) * $posts_per_page )]->post_date))  . '"';
				$x[] = '<a ' . $title . ' href="' . get_pagenum_link($cnt) . 
				'"  >'. $cnt . '</a>' . $end_link;
			}
		}
		
		echo join($seperator, $x);

		echo $before_next;
		
		if( $current_page == $max_num_pages){
			if(get_option('SEOPager_nextallow') == 'allow'){
				echo '<span class="current"' .'" > ' . $nxtlabel . '</span>';
			}
		} else{
			$title = 'title="' .date($pager_date_format, 
			strtotime($p[(($current_page) * $posts_per_page )]->post_date))  . '"';			
			echo '<a ' . $title .  ' href="' . 
			get_pagenum_link(($current_page + 1))  .'" > ' . $nxtlabel . '</a>';
		}
		echo '</div>';

		echo '<!--END http://www.seoegghead.com/software/wordpress-seo-pager.seo-->';
	}	
}

// Generates example page for submenu page
function example_pager($seperator = ' ', $after_previous = '&nbsp;&nbsp;', 
$before_next = '&nbsp;&nbsp;')
{	
	$prelabel = get_option('SEOPager_prelabel');
	$nxtlabel = get_option('SEOPager_nextlabel');
	
	$style = 'class="current"';

	$ellipsis_text_left = $ellipsis_text_right = get_option('SEOPager_ellipsis');
	$max_num_pages = 50;
	$link = get_bloginfo('wpurl') 
	. '/wp-admin/options-genera.php?page=wordpress-seo-pager.php&examplepage=';
	
	$num_begin = 2;
	$num_end = 2;
	$center = get_option('SEOPager_num_of_pages');	
	$pages_in_center = get_option('SEOPager_num_of_pages');

	if($_REQUEST['examplepage']){
		$current_page = $_REQUEST['examplepage'];
	} else{
		$current_page = 1;
	}
	list($start,$end) = get_begin_and_end($current_page, $max_num_pages, $center, 
	$pages_in_center, $num_begin, $num_end);
	echo '<div style="margin:0;padding:0;" class="pagerbox">';
	if($current_page == 1 ){
		if(get_option('SEOPager_preallow') == 'allow' && $max_num_pages > 1){
			echo '<span ' . $style . ' href="" > ' . $prelabel . '</span>';
			echo  $after_previous;		
		}
	} else{
		echo '<a ' . $title  . ' href="' .$link . ($current_page -1) 
		.'" > ' . $prelabel . '</a>';
		echo  $after_previous;
	}
		
	if($start > $num_begin){
		for ($cnt = 1; $cnt <=  $num_begin; $cnt++) {
			$x[] = '<a ' .$title . ' href="' . $link . $cnt . '"  ' 
			. '>' . $cnt . '</a>';
		}
		if(($start - 1)  != $num_begin)
			$x[] = $ellipsis_text_left;
	}	

	for ($cnt = $start; $cnt <= $end; $cnt++) {
		if ($current_page == $cnt) {
			$class = ' ' . $style . '>';
			$x[] =  '<span' . $class. $cnt . '</span>';	
		} else {
			$class = ' >';
			$x[] =  '<a ' .$title . ' href="' . $link . $cnt .'" ' . $class 
			. $cnt . '</a>';
		}
	}
	if($end < ($max_num_pages - $num_end ) ){
		
		$x[] = $ellipsis_text_right;
		
		for ($cnt = ($max_num_pages - $num_end) + 1; $cnt <= $max_num_pages; $cnt++) {
			if ($current_page == $cnt) {
				$class = ' "' . $style . '>';
			} else {
				$class = '" >';
			}
			$x[] = '<a ' .$title . ' href="' . $link . $cnt  . $class . $cnt . '</a>';
		}
	}
	echo join($seperator, $x);

	echo $before_next;
	
	if( $current_page == $max_num_pages){
		if(get_option('SEOPager_nextallow') == 'allow'){
			echo '<span ' . $style .'" > ' . $nxtlabel . '</span>';
		}
	} else{
		echo '<a ' . $title  . ' href="' . $link . ($current_page +1) 
		.'" > ' . $nxtlabel . '</a>';
	}
		
	echo '</div>';
}

function get_begin_and_end($page_number, $total_pages, $auto_center_minimum = 0, 
$max_listed_pages = 10, $number_of_pages_at_begin =2 , $number_of_pages_at_end = 2)
{

	if($auto_center_minimum == 0) {
		$auto_center_minimum = $max_listed_pages -1;
	}

	if (!$page_number) {
		$page_number = 1;	
	}

	$half_max = $max_listed_pages/2;
	
	if($auto_center_minimum >= $page_number ){
		//pager centers at half_max
		$start = floor($page_number / ($max_listed_pages)) * $max_listed_pages; 
	} else {
		//pager centers at selected page
		$start = floor($page_number -$half_max) + 1 ;
	}
	
	// if max listed pages < pages at begin adjust start
	if($max_listed_pages <= $number_of_pages_at_begin 
	&& $start <= $number_of_pages_at_begin  ){
		if($page_number == $max_listed_pages 
		|| ($page_number == $max_listed_pages + 1)){
			$start = floor($page_number -$half_max) + 1 ;
		}
	}
	
	// if max listed pages >= pages at begin adjust start
	if($max_listed_pages >= $number_of_pages_at_begin 
	&& $start >= $number_of_pages_at_begin 
	&& $page_number -1<= $max_listed_pages)
	{
		if($page_number == $max_listed_pages 
		|| ($page_number == $max_listed_pages + 1)){
			$start = floor($page_number -$half_max) + 1 ;
		}
	}
	
	// if page number is close to end add start to keep max_listed_pages # of pages
	if(($page_number + $half_max) > $total_pages 
	&& $page_number + 1 > $max_listed_pages){
		$pages_left = $total_pages - $page_number;
		$start -= floor($half_max  - $pages_left);
	}
	
	if (!$start || $start < 0) {
		$start = 1;	
	}
	
	$end = ($total_pages < ($start + $max_listed_pages - 1)) 
	? $total_pages : $start + $max_listed_pages - 1;
	
	if($end + $number_of_pages_at_end >= $total_pages ){
		$end = $total_pages;
	}
	
	if($total_pages <= ($number_of_pages_at_end + $number_of_pages_at_begin 
	+ $max_listed_pages)){
		$start = 1;
		$end = $total_pages;
	}
	return array($start,$end);
}
?>