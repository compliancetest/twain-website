<?php

/*********************************************** PROMOTION FUNCTIONS ****************************************************************/

define('THE_FUNCTION', STYLESHEETPATH . '/functions');

//require_once(THE_FUNCTION . '/adminer/adminer.php'); NOt WORKING YET 
//require_once(THE_FUNCTION . '/sidebars.php');
//require_once(THE_FUNCTION . '/breadcrumb-trail.php');
//require_once(THE_FUNCTION . '/content_elements.php');
//require_once(THE_FUNCTION . '/portfolio.php');
//require_once(THE_FUNCTION . '/cutom_meta_boxes.php');
//require_once(THE_FUNCTION . '/sidebar/per-page-sidebars.php');
//require_once(THE_FUNCTION . '/pager.php');
//require_once(THE_FUNCTION . '/widgets/social-widget/social-widget.php');
//require_once(THE_FUNCTION . '/widgets/sidebar-login/sidebar-login.php');
//require_once(THE_FUNCTION . '/widgets/recent-posts-widget.php');
//require_once(THE_FUNCTION . '/widgets/twitter.php');
//require_once(THE_FUNCTION . '/recent-posts-slider/recent-posts-slider.php');
//require_once(THE_FUNCTION . '/easy-fancybox/easy-fancybox.php');

//CONTACT FORM 7 - ads simple contact form to posts and pages NOT WORKING YET
//require_once(THE_FUNCTION . '/contact-form-7/wp-contact-form-7.php');

//GRUNION CONTACT FORM - ads simple contact form to posts and pages NOT WORKING YET
//require_once(THE_FUNCTION . '/contact-form/grunion-contact-form.php');

//SHORTCODES URLIMATE - allows for unlimited shortcodes
//require_once(THE_FUNCTION . '/shortcodes-ultimate/shortcodes-ultimate.php');

//require_once(THE_FUNCTION . '/moover/moover.php');

//require_once(THE_FUNCTION . '/mobile_detect.php');

//MORE FIELDS - allows for extra custom fields in the edit dashboard
require_once(THE_FUNCTION . '/more-fields/more-fields.php');

//MORE TYPES - allows extra custom post types
require_once(THE_FUNCTION . '/more-types/more-types.php');

//MORE TYPES - allows extra custom post types
require_once(THE_FUNCTION . '/more-taxonomies/more-taxonomies.php');

//CUSTOM POST TEMPLATE
//require_once(THE_FUNCTION . '/custom-post-template/custom-post-templates.php');

//Recently updated pages and posts
require_once(THE_FUNCTION . '/recently-updated-pages-and-posts/recently_updated.php');

// wordpress-popular-posts
// require_once(THE_FUNCTION . '/wordpress-popular-posts/wordpress-popular-posts.php');

//DYNAMIC CUSTOM POST TYPES
//require_once(THE_FUNCTION . '/dinamic_custom_post_types.php');

//require_once(THE_FUNCTION . '/advanced-code-editor/advanced-code-editor.php');
/* 
 * Loads the Options Panel
 *
 * If you're loading from a child theme use stylesheet_directory
 * instead of template_directory
 */
 
if ( !function_exists( 'optionsframework_init' ) ) {
	define( 'OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/inc/' );
	require_once dirname( __FILE__ ) . '/inc/options-framework.php';
}


if ( !function_exists( 'bp_dtheme_enqueue_styles' ) ) :
    function bp_dtheme_enqueue_styles() {}
endif;


/*********************************************** PROMOTION ENQUEUE SCRIPTS *********************************************************/


/*********************************************************************** MISC **********************************************************************/
function get_options($section)
{
	return get_option($section.'_options');
}

function get_custom_field($field)
{
	return get_post_meta(get_the_ID(), $field, true);
}

function template_location($echo = true)
{
	if ($echo) {
		bloginfo('template_directory');
	} else {
		return get_bloginfo('template_directory');
	}
}

function get_the_excerpt_by_id($post_id)
{
	$excerpt = '';
	$my_query = new WP_Query('p='.$post_id.'&post_type=products');
	if ($my_query->have_posts()) {
		$my_query->the_post();
		$excerpt = get_the_excerpt();
	}
	wp_reset_postdata();
	return $excerpt;
}


/******************************************************************* SIDEBARS *****************************************************************/

add_action( 'widgets_init', 'initialize_widgets' );

function initialize_widgets() {
	register_sidebar(array(
		'name'					=> 'Sidebar',
		'id' 						=> 'sidebar',
		'description'   => __( 'Located in default page template and posts.'),
		'before_widget' => '<div id="%2$s" class="sidebar_widget">',
		'after_widget' => '</div>',
		'before_title' => '<h2>',
		'after_title' => '</h2>'
	));
	register_sidebar(array(
	'name' => 'Homepage Sidebar',
	'id' => 'homepage-sidebar',
	'description' => 'Appears as the sidebar on the custom homepage',
	'before_widget' => '<li id="%1$s" class="widget %2$s">',
	'after_widget' => '</li>',
	'before_title' => '<h2 class="widgettitle">',
	'after_title' => '</h2>',
	));
	register_sidebar(array(
	'name' => 'Dashboard Sidebar',
	'id' => 'dashboard-sidebar',
	'description' => 'Appears as the sidebar on the dashboard homepage',
	'before_widget' => '<li id="%1$s" class="widget %2$s">',
	'after_widget' => '</li>',
	'before_title' => '<h2 class="widgettitle">',
	'after_title' => '</h2>',
	));
}	

/****************************************************************** HEADER & FOOTER ****************************************************************/

add_action('wp_head', 'add_header_scripts');

function add_header_scripts()
{
	//wp_deregister_script('jquery');
	
	//wp_enqueue_script('jquery_min', get_stylesheet_directory_uri().'/js/jquery-1.7.2.min.js');
    
    $actions_depends = array('jquery');
	if (stripos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) {
		wp_enqueue_script('pie', get_stylesheet_directory_uri().'/js/PIE.js', $actions_depends);
		$actions_depends[] = 'pie';
	}

    wp_enqueue_script('jquery_form', get_stylesheet_directory_uri().'/js/jquery.form.js', $actions_depends);
    wp_enqueue_script('custom_scripts', get_stylesheet_directory_uri().'/js/custom.js', $actions_depends);
	//wp_enqueue_script('actions', template_location(false).'/js/custom.js', $actions_depends);
	//wp_enqueue_style('fonts', 'http://fonts.googleapis.com/css?family=Lobster|Arvo');
}

/******************************************************************* MENUS SUPPORT******************************************************************/
if (function_exists('wp_nav_menu')) {
	if (function_exists('add_theme_support')) {
		add_theme_support('nav-menus');
		add_action('init', 'register_my_menus');
		function register_my_menus() 
		{
			register_nav_menus(array(
							'header-menu' => __('Header Menu'),
							'footer-menu' => __('Footer Menu'),
							'footer-menu2' => __('Useful Links'),
							'dashboard-menu' => __('Dashboard Menu')
					));
		}
	}
}

function headermenu() { ?>
	<ul id="menu">
		<li class="header-menu-item"><a href="#">Home Page</a></li>
		<li class="header-menu-item"><a href="#">About Us</a></li>
		<li class="header-menu-item"><a href="#">New Offers</a></li>
		<li class="header-menu-item"><a href="#">My Account</a></li>
		<li class="header-menu-item"><a href="#">Lorem Ipsum</a></li>
	</ul>
<?php }

function footermenu() { ?>
	<ul id="menu-footer-menu">
		<li><a href="#">About Us</a> | </li>
		<li><a href="#">Main Menu</a> | </li>
		<li><a href="#">New Products</a> | </li>
		<li><a href="#">Our Services</a> | </li>
		<li><a href="#">Contact Us</a></li> 
	</ul>
<?php }

class headermenu_walker extends Walker_Nav_Menu
{
	function start_el(&$output, $item, $depth, $args)
	{
		$attributes  = ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
        
        $output .= '<li class="header-menu-item"><a'. $attributes .'>';
        $output .= apply_filters( 'the_title', $item->title, $item->ID );
        $output .= '</a></li>';
    }
}

class footer_walker extends Walker_Nav_Menu
{
    public $count;
    public $running_count;
    function __construct()
    {
        $this->count = 0;
        $this->running_count = 0;
    }
    function start_el(&$output, $item, $depth, $args)
    {
        $attributes  = ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
        $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
        
        $output .= '<li><a'. $attributes .'>';
        $output .= apply_filters( 'the_title', $item->title, $item->ID );
        $output .= '</a>';
    }
    function end_el(&$output, $item, $depth)
    {
        $this->running_count++;
        if($this->count > $this->running_count) {
            $output .= "</li>";
        } else {
        	$output .= "</li>";
        }
    }
    function walk( $elements, $max_depth, $r )
    {
        $this->count = count($elements);
        return parent::walk( $elements, $max_depth, $r );
    }
}

/******************************************************************* Thumbnail support******************************************************************/

add_theme_support('post-thumbnails');
set_post_thumbnail_size( 300, 150, true );
add_image_size( 'post-thumb', 300, 150, true );

/********************************************************************* Search form *********************************************************************/

add_filter( 'get_search_form', 'my_search_form' );

function my_search_form( $form ) {
    $form = '<form role="search" method="get" id="searchform" action="' . home_url( '/' ) . '" >
			    <div>
				    <label class="search_label" for="s">' . __('Search for:') . '</label>
				    <input type="text" value="' . get_search_query() . '" name="s" id="s" />
				    <input type="submit" id="searchsubmit" value="'. esc_attr__('Search') .'" />
			    </div>
		    </form>';

    return $form;
}

/*********************************************************************** Widgets ***********************************************************************/

add_action('widgets_init', 'Register_Widgets');
function Register_Widgets()
{
	register_widget('SampleWidget');
}

class SampleWidget extends WP_Widget {
	
	function SampleWidget()
	{
		$widget_ops = array('classname' => 'widget_SampleWidget', 'description' => 'Display SampleWidget');
		$control_ops = array('id_base' => 'SampleWidget_widget');
		$this->WP_Widget('services_widget', 'SampleWidget', $widget_ops, $control_ops);
	}
	
	function widget($args, $instance)
	{
		extract($args);
		
		$services[0]['title'] = $instance['first_SampleWidget_title'];
		$services[0]['img'] = $instance['first_SampleWidget_img'];
		$services[0]['text'] = $instance['first_SampleWidget_text'];
		
		$services[1]['title'] = $instance['second_SampleWidget_title'];
		$services[1]['img'] = $instance['second_SampleWidget_img'];
		$services[1]['text'] = $instance['second_SampleWidget_text'];
		
		$services[2]['title'] = $instance['third_SampleWidget_title'];
		$services[2]['img'] = $instance['third_SampleWidget_img'];
		$services[2]['text'] = $instance['third_SampleWidget_text'];
		
		echo $before_widget;
		?>
		<!-- BEGIN WIDGET -->
		<ul id="SampleWidget_list">
			<?php $count = 0;
			foreach ($SampleWidget as $sw) {
				$count++;
				if ($count % 3 == 0) {
					$class = ' class="last"';
				} else {
					$class = '';
				} ?>
				<li<?php echo $class; ?>>
					<h2><?php echo $sw['title']; ?></h2>
					<img src="<?php echo $sw['img']; ?>" alt="" />
					<p><?php echo $sw['text']; ?></p>
				</li>
			<?php } ?>
		</ul>
		<!-- END WIDGET -->
		<?php echo $after_widget;
	}
	
	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		
		$instance['first_SampleWidget_title'] = $new_instance['first_SampleWidget_title'];
		$instance['first_SampleWidget_img'] = $new_instance['first_SampleWidget_img'];
		$instance['first_SampleWidget_text'] = $new_instance['first_SampleWidget_text'];
		
		$instance['second_SampleWidget_title'] = $new_instance['second_SampleWidget_title'];
		$instance['second_SampleWidget_img'] = $new_instance['second_SampleWidget_img'];
		$instance['second_SampleWidget_text'] = $new_instance['second_SampleWidget_text'];
		
		$instance['third_SampleWidget_title'] = $new_instance['third_SampleWidget_title'];
		$instance['third_SampleWidget_img'] = $new_instance['third_SampleWidget_img'];
		$instance['third_SampleWidget_text'] = $new_instance['third_SampleWidget_text'];
		
		return $instance;
	}

	function form($instance)
	{
		$defaults = array(
				'first_SampleWidget_title' => '',
				'first_SampleWidget_img' => '',
				'first_SampleWidget_text' => '',
				'second_SampleWidget_title' => '',
				'second_SampleWidget_img' => '',
				'second_SampleWidget_text' => '',
				'third_SampleWidget_title' => '',
				'third_SampleWidget_img' => '',
				'third_SampleWidget_text' => ''
			);
		$instance = wp_parse_args((array) $instance, $defaults); ?>
		<p>
			<label for="<?php echo $this->get_field_id('first_SampleWidget_title'); ?>">First SampleWidget title: </label>
			<input class="widefat" style="width: 216px;" id="<?php echo $this->get_field_id('first_SampleWidget_title'); ?>" name="<?php echo $this->get_field_name('first_SampleWidget_title'); ?>" value="<?php echo $instance['first_SampleWidget_title']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('first_SampleWidget_img'); ?>">First SampleWidget image: </label>
			<input class="widefat" style="width: 216px;" id="<?php echo $this->get_field_id('first_SampleWidget_img'); ?>" name="<?php echo $this->get_field_name('first_SampleWidget_img'); ?>" value="<?php echo $instance['first_SampleWidget_img']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('first_SampleWidget_text'); ?>">First SampleWidget text: </label>
			<textarea class="widefat"  rows="16" cols="20" style="width: 216px;" id="<?php echo $this->get_field_id('first_SampleWidget_text'); ?>" name="<?php echo $this->get_field_name('first_SampleWidget_text'); ?>"><?php echo $instance['first_SampleWidget_text']; ?></textarea>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('second_SampleWidget_title'); ?>">Second SampleWidget title: </label>
			<input class="widefat" style="width: 216px;" id="<?php echo $this->get_field_id('second_SampleWidget_title'); ?>" name="<?php echo $this->get_field_name('second_SampleWidget_title'); ?>" value="<?php echo $instance['second_SampleWidget_title']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('second_SampleWidget_img'); ?>">Second SampleWidget image: </label>
			<input class="widefat" style="width: 216px;" id="<?php echo $this->get_field_id('second_SampleWidget_img'); ?>" name="<?php echo $this->get_field_name('second_SampleWidget_img'); ?>" value="<?php echo $instance['second_SampleWidget_img']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('second_SampleWidget_text'); ?>">Second SampleWidget text: </label>
			<textarea class="widefat"  rows="16" cols="20" style="width: 216px;" id="<?php echo $this->get_field_id('second_SampleWidget_text'); ?>" name="<?php echo $this->get_field_name('second_service_text'); ?>"><?php echo $instance['second_SampleWidget_text']; ?></textarea>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('third_SampleWidget_title'); ?>">Third SampleWidget title: </label>
			<input class="widefat" style="width: 216px;" id="<?php echo $this->get_field_id('third_SampleWidget_title'); ?>" name="<?php echo $this->get_field_name('third_SampleWidget_title'); ?>" value="<?php echo $instance['third_SampleWidget_title']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('third_SampleWidget_img'); ?>">Third SampleWidget image: </label>
			<input class="widefat" style="width: 216px;" id="<?php echo $this->get_field_id('third_SampleWidget_img'); ?>" name="<?php echo $this->get_field_name('third_SampleWidget_img'); ?>" value="<?php echo $instance['third_SampleWidget_img']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('third_SampleWidget_text'); ?>">Third SampleWidget text: </label>
			<textarea class="widefat"  rows="16" cols="20" style="width: 216px;" id="<?php echo $this->get_field_id('third_SampleWidget_text'); ?>" name="<?php echo $this->get_field_name('third_SampleWidget_text'); ?>"><?php echo $instance['third_SampleWidget_text']; ?></textarea>
		</p>
	<?php }
} 


/* Metaboxes from Products / Services */
/* Metabox CERTIFICATION - Choose Test Suites */

function add_test_suites_metabox(){
	// add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $callback_args );
    add_meta_box("test_suites_metabox", "Select Certifications (Test Suites) ", 'show_test_suites', "product-service", "normal", "high");
    add_meta_box("related_products_metabox", "Select Related Products / Services ", 'show_related_products', "product-service", "normal", "high");
}

add_action('admin_menu', 'add_test_suites_metabox');

function show_test_suites(){
	global $post;
	$post_backup = $post;
	$current_test_suite = explode(',', get_post_meta($post->ID, 'test_suites', true));
	
	//echo '<input type="hidden" name="custom_test_suites" value="', wp_create_nonce(basename(__FILE__)), '" />';
	$loop = new WP_Query( array( 'post_type' => 'test-suite', 'posts_per_page' => -1) );
	
	while ( $loop->have_posts() ) : $loop->the_post();
		 ?>
		 
		 <input type="checkbox" name="test_suites[]" <?php if (in_array(get_the_ID(), $current_test_suite)) { echo 'checked="checked"'; } ?> value="<?php the_ID(); ?>" style="margin-right: 5px; margin-bottom: 5px;"><?php the_title(); ?> <br />
		
		<?php
	endwhile;
	//wp_reset_postdata(); 
	$post = $post_backup;
}


function show_related_products(){
	global $post;
	$post_backup = $post;
	$current_product = explode(',', get_post_meta($post->ID, 'related_products', true));
	
	//echo '<input type="hidden" name="custom_relprod" value="', wp_create_nonce(basename(__FILE__)), '" />';
	$loop = new WP_Query( array( 'post_type' => 'product-service', 'posts_per_page' => -1, 'post__not_in' =>array($post->ID)) );
	
	while ( $loop->have_posts() ) : $loop->the_post();
		 ?>
		 
		 <input type="checkbox" name="related_products[]" <?php if (in_array(get_the_ID(), $current_product)) { echo 'checked="checked"'; } ?> value="<?php the_ID(); ?>" style="margin-right: 5px; margin-bottom: 5px;"><?php the_title(); ?> <br />
		
		<?php
	endwhile;
	//wp_reset_postdata();	 
	$post = $post_backup;
}

add_action('save_post', 'save_test_suites');

function save_test_suites($post_id) {
	//die('<pre>'.print_r($_POST, true).'</pre>');
    // verify nonce
    if (!isset($_POST['custom_test_suites']) || !wp_verify_nonce($_POST['custom_test_suites'], basename(__FILE__))) {
        //return $post_id;
    }
    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
    // check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }
      
    $test_suites = '';
    if (count($_POST['test_suites'])) {
		$test_suites = implode(',', $_POST['test_suites']);
	}
	
	update_post_meta($post_id, 'test_suites', $test_suites);
	
    $related_products = '';
    if (count($_POST['related_products'])) {
		$related_products = implode(',', $_POST['related_products']);
	}
		
	update_post_meta($post_id, 'related_products', $related_products);
} 



/*Metabox Test Case Page */
function add_test_execution_metaboxes(){
	// add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $callback_args );
	/*Metabox Test Executions */
    add_meta_box("select_test_suites_metabox", "Select Test Suite", 'select_test_suites', "test-case", "normal", "high");
    add_meta_box("test_execution_metabox", "Test Execution", 'show_test_execution', "test-case", "normal", "high");
    add_meta_box("test_data_metabox", "Test Data", 'show_test_data', "test-case", "normal", "high");
    add_meta_box("test_steps_metabox", "Test Steps 2", 'show_test_steps2', "test-case", "normal", "high");
    add_meta_box("choose_initiating_message_metabox", "Choose Initiating Message", 'show_choose_initiating_message','test-case',"normal","high");
    add_meta_box("choose_roles_metabox", "Choose Roles", 'show_choose_roles','test-case',"normal","high");
    add_meta_box("choose_level_metabox", "Choose Conformance Level", 'show_conformance_level','test-case',"normal","high");
}

add_action('admin_menu', 'add_test_execution_metaboxes');

function select_test_suites(){
	global $post;
	$post_backup = $post;
	$current_test_suites = get_post_meta($post->ID, 'test_suites', true);
	$loop = new WP_Query( array( 'post_type' => 'test-suite', 'posts_per_page' => -1) );
	
	
	foreach($current_test_suites as $key => $test_suites){?>
	<div class="elem-ts"> <div class="elem-ts">
		<select name="test_suites[]" class="testsuite_values">
			<option value="">Select Test Suites</option>
			<?php
			while ( $loop->have_posts() ) : $loop->the_post();
				 ?>
				 <option <?php if (get_the_ID() == $test_suites) { echo 'selected="selected"'; }; ?> value="<?php the_ID(); ?>" style="margin-right: 5px; margin-bottom: 5px;"><?php the_title(); ?> </option>
				<?php
			endwhile;
			?>
		</select>
		<!--<div class="button remove_ts left">Remove Test Suite</div>-->
	</div> </div>	
	<?php } 
	
	if (empty($current_test_suites)){?>
	<div class="elem-ts"> <div class="elem-ts">
		<select name="test_suites[]" class="testsuite_values">
				<option value="">Choose Related Suite</option>
				<?php
				while ( $loop->have_posts() ) : $loop->the_post();
					if (isset($_GET['set_ts'])){ 
						?>
						<option value="<?php the_ID(); ?>" <?php if(get_the_ID()==$_GET['set_ts']) {echo 'selected="selected"';} ?> > <?php the_title(); ?></option>
						<?php 
						}
						else{
					 ?>
					 <option value="<?php the_ID(); ?>"><?php the_title(); ?> </option>
					<?php }
				endwhile;
				?>
		</select>
		</div></div>
		
		 <?php
		 }
		$post = $post_backup;
		?>
		<div class="copy-correct-ts">	
		</div>
		
		<a class="add_new_ts button right">Add</a>
		
		<div class="clear"></div>
		
		<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery(".add_new_ts").click(function(data) {
				jQuery('.copy-correct-ts').append(jQuery('.elem-ts').html());
			});
			jQuery(".remove_ts").click( function() {
				jQuery(this).parents('.elem-ts').remove();
			});
			//Select Test Suite, Show Roles & Initiating Message
			
			if (jQuery(".testsuite_values").val() ==''){
				jQuery("#choose_roles_metabox .inside #tester_role").html('<b>Tester Role</b><br />First choose a test suite');
				jQuery("#choose_roles_metabox .inside #harness_role").html('<b>Harness Role</b><br />First choose a test suite');
				jQuery("#choose_roles_metabox .inside #initiator_role").html('<b>Initiator</b><br />First choose a test suite');
				jQuery("#choose_initiating_message_metabox .inside").html('First choose a test suite');
				jQuery("#choose_level_metabox .inside").html('First choose a test suite');
			}
			
			var checkElem_tester = jQuery('#checktester').size() > 0 ? 1 : 0;
			var checkElem_harness = jQuery('#checkharness').size() > 0 ? 1 : 0;
			var checkElem_initiator = jQuery('#checkinitiator').size() > 0 ? 1 : 0;
			var checkElem2 = jQuery('#checkinitmsg').size() > 0 ? 1 : 0;
			var checkElem3 = jQuery('#checkconflvl').size() > 0 ? 1 : 0;
			
			jQuery(document).on('change', '.testsuite_values', function(){
				
				checkElem_tester = jQuery('#checktester').size() > 0 ? 1 : 0;
				checkElem_harness = jQuery('#checkharness').size() > 0 ? 1 : 0;
				checkElem_initiator = jQuery('#checkinitiator').size() > 0 ? 1 : 0;
				checkElem2 = jQuery('#checkinitmsg').size() > 0 ? 1 : 0;
				checkElem3 = jQuery('#checkconflvl').size() > 0 ? 1 : 0;
				
				var testsuite_val = jQuery(this).val();
				
				//Tester Role
				var fields = {
						testsuite_id_tester: testsuite_val,
						checkElem_tester: checkElem_tester,
					}
				
				jQuery.ajax({
					url: '',
					data: fields,
					type:'POST',
					success: function(data){
						var getData_tester = data.split('##');
						var get_ts_val_tester = [];
						jQuery('select.testsuite_values option:selected').each(function(i) {
							get_ts_val_tester[i]=jQuery(this).val();
						});
						
						//Remove additional options
						jQuery('#checktester option').each(function() {
							var get_init_mess_class_tester = jQuery(this).attr('class');
							var get_init_mess_val_tester = jQuery(this).val();
							
							if(jQuery.inArray(get_init_mess_class_tester,get_ts_val_tester) == -1){
								jQuery(this).remove();
							}	
						});
						
						if(getData_tester[0] == 0){
							jQuery("#choose_roles_metabox .inside #tester_role").html(getData_tester[1]);
						}
						else if(getData_tester[0] == 1){
							jQuery("#choose_roles_metabox .inside #tester_role #checktester").append(getData_tester[1]);
							//Remove Duplicates
							var usedNames_tester = {};
							jQuery("#checktester option").each(function () {
								if(usedNames_tester[this.text]) {
									jQuery(this).remove();
								} else {
									usedNames_tester[this.text] = this.value;
								}
							});
						}
							
					},
					error: function(data){
					}
				});
				
				//Harness Role
				var fields_harness = {
						testsuite_id_harness: testsuite_val,
						checkElem_harness: checkElem_harness,
					}
				
				jQuery.ajax({
					url: '',
					data: fields_harness,
					type:'POST',
					success: function(data){
						var getData_harness = data.split('##');
						var get_ts_val_harness = [];
						jQuery('select.testsuite_values option:selected').each(function(i) {
							get_ts_val_harness[i]=jQuery(this).val();
						});
						
						//Remove additional options
						jQuery('#checkharness option').each(function() {
							var get_init_mess_class_harness = jQuery(this).attr('class');
							var get_init_mess_val_harness = jQuery(this).val();
							
							if(jQuery.inArray(get_init_mess_class_harness,get_ts_val_harness) == -1){
								jQuery(this).remove();
							}	
						});

						if(getData_harness[0] == 0){
							jQuery("#choose_roles_metabox .inside #harness_role").html(getData_harness[1]);
						}
						else if(getData_harness[0] == 1){
							jQuery("#choose_roles_metabox .inside #harness_role #checkharness").append(getData_harness[1]);
							//Remove Duplicates
							var usedNames_harness = {};
							jQuery("#checkharness option").each(function () {
								if(usedNames_harness[this.text]) {
									jQuery(this).remove();
								} else {
									usedNames_harness[this.text] = this.value;
								}
							});
						}
							
					},
					error: function(data){
					}
				});
				
				//Initiator
				var fields_initiator = {
						testsuite_id_initiator: testsuite_val,
						checkElem_initiator: checkElem_initiator,
					}
				
				jQuery.ajax({
					url: '',
					data: fields_initiator,
					type:'POST',
					success: function(data){
						var getData_initiator = data.split('##');
						var get_ts_val_initiator = [];
						jQuery('select.testsuite_values option:selected').each(function(i) {
							get_ts_val_initiator[i]=jQuery(this).val();
						});
						
						//Remove additional options
						jQuery('#checkinitiator option').each(function() {
							var get_init_mess_class_initiator = jQuery(this).attr('class');
							var get_init_mess_val_initiator = jQuery(this).val();
							
							if(jQuery.inArray(get_init_mess_class_initiator,get_ts_val_initiator) == -1){
								jQuery(this).remove();
							}	
						});

						if(getData_initiator[0] == 0){
							jQuery("#choose_roles_metabox .inside #initiator_role").html(getData_initiator[1]);
						}
						else if(getData_initiator[0] == 1){
							jQuery("#choose_roles_metabox .inside #initiator_role #checkinitiator").append(getData_initiator[1]);
							//Remove Duplicates
							var usedNames_initiator = {};
							jQuery("#checkinitiator option").each(function () {
								if(usedNames_initiator[this.text]) {
									jQuery(this).remove();
								} else {
									usedNames_initiator[this.text] = this.value;
								}
							});
						}
							
					},
					error: function(data){
					}
				});
				
				
				// Initiating Message
				var fields2 = {
					testsuite_id2 : testsuite_val,
					checkElem2: checkElem2
					}
				jQuery.ajax({
					url: '',
					data: fields2,
					type:'POST',
					success: function(data){
						var getData2 = data.split('##');
						var get_ts_val2 = [];
						jQuery('select.testsuite_values option:selected').each(function(i) {
							get_ts_val2[i]=jQuery(this).val();
						});
						
						//Remove additional options
						jQuery('#checkinitmsg option').each(function() {
							var get_init_mess_class2 = jQuery(this).attr('class');
							var get_init_mess_val2 = jQuery(this).val();
							
							if(jQuery.inArray(get_init_mess_class2,get_ts_val2) == -1){
								jQuery(this).remove();
							}	
						});
						
						if(getData2[0] == 0){
							jQuery("#choose_initiating_message_metabox .inside").html(getData2[1]);
						}
						else if(getData2[0] == 1){
							jQuery("#choose_initiating_message_metabox .inside #checkinitmsg").append(getData2[1]);
							//Remove Duplicates
							var usedNames2 = {};
							jQuery("#checkinitmsg option").each(function () {
								if(usedNames2[this.text]) {
									jQuery(this).remove();
								} else {
									usedNames2[this.text] = this.value;
								}
							});
						}
					},
					
					error: function(data){
					}
				});
				
				//Level Code
				var fields3 = {
						testsuite_id3 : testsuite_val,
						checkElem3: checkElem3
					}
				jQuery.ajax({
					url: '',
					data: fields3,
					type:'POST',
					success: function(data){
						var getData3 = data.split('##');
						var get_ts_val3 = [];
						jQuery('select.testsuite_values option:selected').each(function(i) {
							get_ts_val3[i]=jQuery(this).val();
						});
						
						//Remove additional options
						jQuery('#checkconflvl option').each(function() {
							var get_init_mess_class3 = jQuery(this).attr('class');
							var get_init_mess_val3 = jQuery(this).val();
							
							if(jQuery.inArray(get_init_mess_class3,get_ts_val3) == -1){
								jQuery(this).remove();
							}
							
						});
						
						if(getData3[0] == 0){
							jQuery("#choose_level_metabox .inside").html(getData3[1]);
						}else if(getData3[0] == 1){
							jQuery("#choose_level_metabox .inside #checkconflvl").append(getData3[1]);
							//Remove Duplicates
							var usedNames3 = {};
							jQuery("#checkconflvl option").each(function () {
								if(usedNames3[this.text]) {
									jQuery(this).remove();
								} else {
									usedNames3[this.text] = this.value;
								}
							});
						}	
					},
					
					error: function(data){
					}
				});
			});
					
		});
		</script>
<?php	
}
	
//Create New Test Case 

//Tester Roles
if(isset($_POST['testsuite_id_tester'])){
	//Terster Role
	$get_tester_roles = get_post_meta($_POST['testsuite_id_tester'], 'tester_role_ts', true);
	$test_roles = explode(',',$get_tester_roles);
	if($_POST['checkElem_tester']==0){
		echo '0##';
		//Tester Roles - Select
		echo '<div id="tester_role"><label for="choose_tester_role"><b>Tester Role</b></label><br />';
		echo '<select name="choose_tester_role" id="checktester">';
		echo '<option value="">Choose Tester Role</option> ';
		
		foreach($test_roles as $test_role){
			echo '<option value="'.$test_role.'" class="'.$_POST['testsuite_id_tester'].'">'.$test_role.'</option>';
			}
		echo '</select> </div>';
		exit();
	}
	else{
		echo '1##';
		foreach($test_roles as $test_role){
			echo '<option value="'.$test_role.'" class="'.$_POST['testsuite_id_tester'].'">'.$test_role.'</option>';
			}
		exit();	
		}
}
//Harness Role
if(isset($_POST['testsuite_id_harness'])){
	//Harness Role
	$get_harness_roles = get_post_meta($_POST['testsuite_id_harness'], 'harness_role_ts', true);
	$harness_roles = explode(',',$get_harness_roles);
	if($_POST['checkElem_harness']==0){
		echo '0##';
		//Harness Roles - Select
		echo '<div id="harness_role"><label for="choose_harness_role"><b>Harness Role</b></label><br />';
		echo '<select name="choose_harness_role" id="checkharness">';
		echo '<option value="">Choose Harness Role</option> ';
		
		foreach($harness_roles as $harness_role){
			echo '<option value="'.$harness_role.'" class="'.$_POST['testsuite_id_harness'].'">'.$harness_role.'</option>';
			}
		echo '</select> </div> ';
		exit();
	}
	else{
		echo '1##';
		foreach($harness_roles as $harness_role){
			echo '<option value="'.$harness_role.'" class="'.$_POST['testsuite_id_harness'].'">'.$harness_role.'</option>';
			}
		exit();	
		}
}

//Initiator Role
if(isset($_POST['testsuite_id_initiator'])){
	//Initiator Role
	$get_initiator_roles = get_post_meta($_POST['testsuite_id_initiator'], 'initiator_ts', true);
	$initiator_roles = explode(',',$get_initiator_roles);
	if($_POST['checkElem_initiator']==0){
		echo '0##';
		//Initiator Roles - Select
		echo '<div id="initiator_role"><label for="choose_initiator"><b>Initiator</b></label><br />';
		echo '<select name="choose_initiator" id="checkinitiator">';
		echo '<option value="">Choose Initiator</option> ';
		
		foreach($initiator_roles as $initiator_role){
			echo '<option value="'.$initiator_role.'" class="'.$_POST['testsuite_id_initiator'].'">'.$initiator_role.'</option>';
			}
		echo '</select> </div> <br />';
		exit();
	}
	else{
		echo '1##';
		foreach($initiator_roles as $initiator_role){
			echo '<option value="'.$initiator_role.'" class="'.$_POST['testsuite_id_initiator'].'">'.$initiator_role.'</option>';
			}
		exit();	
		}
}

if (isset($_POST['testsuite_id2'])){
	//Initiating Message
	$get_initiating_message = get_post_meta($_POST['testsuite_id2'], 'init_message', true);
	$initiating_messages = explode(',',$get_initiating_message);
	//Initiating Message - Select
	if($_POST['checkElem2']==0){
		echo '0##';
		echo '<label for="choose_init_messages"><b>Initiating Message</b></label><br />';
		echo '<select name="choose_init_messages" id="checkinitmsg">';
		echo '<option value="">Choose Initiating Message</option>';
		foreach($initiating_messages as $initiating_message){
			echo '<option value="'.$initiating_message.'" class="'.$_POST['testsuite_id2'].'">'.$initiating_message.'</option>';
			}
		echo '</select>';
		exit();
	}else{
		echo '1##';
		foreach($initiating_messages as $initiating_message){
			echo '<option value="'.$initiating_message.'" class="'.$_POST['testsuite_id2'].'">'.$initiating_message.'</option>';
			}
		exit();
	}
}
	
if (isset($_POST['testsuite_id3'])){

	//Initiating Message
	$get_lvl_codes = get_post_meta($_POST['testsuite_id3'], 'lvl_code', true);
	
	if($_POST['checkElem3']==0){
		echo '0##';
		
		echo '<select name="conformance_level" id="checkconflvl">';
		echo '<option value="">Choose Level Code</option> ';
		foreach($get_lvl_codes as $get_lvl_code){
			echo '<option value="'.$get_lvl_code.'" class="'.$_POST['testsuite_id3'].'">'.$get_lvl_code.'</option>';
			}
		echo '</select>';
		exit();
	}else{
			echo '1##';
			foreach($get_lvl_codes as $get_lvl_code){
				echo '<option value="'.$get_lvl_code.'" class="'.$_POST['testsuite_id3'].'">'.$get_lvl_code.'</option>';
			}
			exit();
		}
}	

function show_choose_initiating_message(){
	global $post;
	$post_backup = $post;
	if (isset($_GET['set_ts'])){
		$current_test_suite[0] = $_GET['set_ts'];
	}
	else{
		$current_test_suite = get_post_meta($post->ID, 'test_suites', true);
	}
	$current_init_message = get_post_meta($post->ID, 'choose_init_messages', true);
	
	$all_init_messages = array();
	echo '<select name="choose_init_messages" id="checkinitmsg">';
	echo '<option value="">Choose Initiating Message</option>';
	foreach ($current_test_suite as $test_suite){
		$metas_result = get_post_meta($test_suite, 'init_message', true);
		$metas_array = explode(',', $metas_result);
		foreach($metas_array as $ts_init_message){
			if(!in_array($ts_init_message,$all_init_messages)){
				if($ts_init_message == $current_init_message){
					$selected_init_message = 'selected = "selected"';
				}
				else {
					$selected_init_message = '';
				}
			echo '<option value="'.$ts_init_message.'" '.$selected_init_message.' class="'.$test_suite.'">'.$ts_init_message.'</option>';
			array_push($all_init_messages,$ts_init_message);
			}
		}	
	}
	echo '</select>';
	$post = $post_backup;
}

function show_conformance_level(){
	global $post;
	$post_backup = $post;
	if (isset($_GET['set_ts'])){
		$current_test_suite[0] = $_GET['set_ts'];
	}
	else{
		$current_test_suite = get_post_meta($post->ID, 'test_suites', true);
	}
	$current_conformance_level = get_post_meta($post->ID, 'conformance_level', true);
	$all_conf_level = array();
	echo '<select name="conformance_level" id="checkconflvl">';
	echo '<option value="">Choose Conformance Level</option>';
	foreach ($current_test_suite as $test_suite){
		$metas_array = get_post_meta($test_suite, 'lvl_code', true);
		//$metas_array = explode(',', $metas_result);
		foreach($metas_array as $ts_lvl){
			if(!in_array($ts_lvl,$all_conf_level)){
				if($ts_lvl == $current_conformance_level){
					$selected_conf_lvl = 'selected = "selected"';
				}
				else {
					$selected_conf_lvl = '';
				}
			echo '<option value="'.$ts_lvl.'" '.$selected_conf_lvl.' class="'.$test_suite.'">'.$ts_lvl.'</option>';
			array_push($all_conf_level,$ts_lvl);
			}
		}	
	}
	echo '</select>';
	$post = $post_backup;
}

function show_choose_roles(){
	global $post;
	$post_backup = $post;
	if (isset($_GET['set_ts'])){
		$current_test_suite[0] = $_GET['set_ts'];
	}
	else{
		$current_test_suite = get_post_meta($post->ID, 'test_suites', true);
	}
	
	//Current Roles Selected
	$current_tester_role = get_post_meta($post->ID, 'choose_tester_role', true);
	$current_harness_role = get_post_meta($post->ID, 'choose_harness_role', true);
	$current_initiator = get_post_meta($post->ID, 'choose_initiator', true);
	
	$all_tester_roles = array();
	$all_harness_roles = array();
	$all_initiators = array();
	
	//Roles Test Suite Associated
	//Tester Role
	echo '<div id="tester_role"><label for="choose_tester_role"><b>Tester Role</b></label><br />';
	echo '<select name="choose_tester_role" id="checktester">';
	echo '<option value="">Choose Tester Role</option>';
	foreach ($current_test_suite as $test_suite){
		$get_metas_tester_role = get_post_meta($test_suite, 'tester_role_ts', true);
		$tester_roles = explode(',', $get_metas_tester_role);
		foreach($tester_roles as $tester_role){
			if(!in_array($tester_role,$all_tester_roles)){
				if($tester_role == $current_tester_role){
					$selected_tester_role = 'selected = "selected"';
				}
				else {
					$selected_tester_role = '';
				}
			echo '<option value="'.$tester_role.'" '.$selected_tester_role.' class="'.$test_suite.'">'.$tester_role.'</option>';
			array_push($all_tester_roles,$tester_role);
			}
		}	
	}
	echo '</select> </div> <br />';
	
	//Harness Role
	echo '<div id="harness_role"><label for="choose_harness_role"><b>Harness Role</b></label><br />';
	echo '<select name="choose_harness_role">';
	echo '<option value="">Choose Harness Role</option>';
	foreach ($current_test_suite as $test_suite){
		$get_metas_harness_role = get_post_meta($test_suite, 'harness_role_ts', true);
		$harness_roles = explode(',', $get_metas_harness_role);
		foreach($harness_roles as $harness_role){
			if(!in_array($harness_role,$all_harness_roles)){
				if($harness_role == $current_harness_role){
					$selected_harness_role = 'selected = "selected"';
				}
				else {
					$selected_harness_role = '';
				}
			echo '<option value="'.$harness_role.'" '.$selected_harness_role.' class="'.$test_suite.'">'.$harness_role.'</option>';
			array_push($all_harness_roles,$harness_role);
			}
		}	
	}
	echo '</select> </div> <br />';
	
	//Initiator
	echo '<div id="initiator_role"><label for="choose_initiator"><b>Initiator</b></label><br />';
	echo '<select name="choose_initiator">';
	echo '<option value="">Choose Initiator</option>';
	foreach ($current_test_suite as $test_suite){
		$get_metas_initiators = get_post_meta($test_suite, 'initiator_ts', true);
		$initiators = explode(',', $get_metas_initiators);
		foreach($initiators as $initiator){
			if(!in_array($initiator,$all_initiators)){
				if($initiator == $current_initiator){
					$selected_initiator = 'selected = "selected"';
				}
				else {
					$selected_initiator = '';
				}
			echo '<option value="'.$initiator.'" '.$selected_initiator.' class="'.$test_suite.'">'.$initiator.'</option>';
			array_push($all_initiators,$initiator);
			}
		}	
	}
	echo '</select> </div>';
	
	$post = $post_backup;
}



function show_test_execution(){
	global $post;
	$post_backup = $post;
	$test_url = get_post_meta($post->ID, 'test_url', true);
	$protocol_binding2 = get_post_meta($post->ID, 'protocol_binding2', true);
	$current_property_name_exec= get_post_meta($post->ID, 'property_name_exec', true);
	$current_property_value_exec= get_post_meta($post->ID, 'property_value_exec', true);
	echo '<input type="hidden" name="custom_test_execution" value="', wp_create_nonce(basename(__FILE__)), '" />';
	?>
	<label for="test_url"><b>Test endpoint URL:</b></label> <br />
	<input type="text" name="test_url" value="<?php echo $test_url; ?>" size="30" class="mf_text"/> 
	<br />
	<label for="protocol_binding2"><b>Protocol Binding:</b></label> <br />
	<input type="text" name="protocol_binding2" value="<?php echo $protocol_binding2; ?>" size="30" class="mf_text"/> 
	<br />	<br />
				
	<?php
	foreach($current_property_name_exec as $key => $property_name_exec){
		foreach($current_property_value_exec as $key2 => $property_value_exec){
			if( $key == $key2){ ?>
			<div class="elem-te"> <div class="elem-te"> 
				<label for="property_name_exec"><b>Property Name:</b></label> <br />
				<input type="text" name="property_name_exec[]" value="<?php echo $property_name_exec; ?>" size="30" class="mf_text"/> 
				<br />
				
				<label for="property_value_exec"><b>Property Value:</b></label> <br />
				<input type="text" name="property_value_exec[]" value="<?php echo $property_value_exec; ?>" size="30" class="mf_text"/> 
				<br />
				
				<div class="button remove_te left">Remove</div>
				<br clear="all" /> <br />
				</div> </div>
	  <?php }
		}
	}
	
	if (empty($current_property_name_exec)){ ?>
		<div class="elem-te">
			<label for="property_name_exec"><b>Property Name:</b></label> <br />
			<input type="text" name="property_name_exec[]" size="30" class="mf_text"/> 
			<br />
			
			<label for="property_value_exec"><b>Property Value:</b></label> <br />
			<input type="text" name="property_value_exec[]" size="30" class="mf_text"/> 
			<br /> <br />
		</div>
	<?php
	$post = $post_backup;
	} ?>
	
	<div class="copy-correct-te">	
    </div>
    
    <a class="add_new_te button right">Add</a>
    
    <div class="clear"></div>

    <script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery(".add_new_te").click(function(data) {
			jQuery('.copy-correct-te').append(jQuery('.elem-te').html());
			//jQuery('.copy-correct input, .copy-correct select').val('');
		});
		jQuery(".remove_te").live('click', function() {
			jQuery(this).parents('.elem-te').remove();
		});
	});
    </script>	
<?php	
}

function show_test_data(){
	global $post;
	$post_backup = $post;
	$current_property_name_data= get_post_meta($post->ID, 'property_name_data', true);
	$current_property_value_data= get_post_meta($post->ID, 'property_value_data', true);
	echo '<input type="hidden" name="custom_test_data" value="', wp_create_nonce(basename(__FILE__)), '" />';
	?>
				
	<?php
	foreach($current_property_name_data as $key => $property_name_data){
		foreach($current_property_value_data as $key2 => $property_value_data){
			if( $key == $key2){ ?>
			<div class="elem-td"> <div class="elem-td"> 
				<label for="property_name_data"><b>Property Name:</b></label> <br />
				<input type="text" name="property_name_data[]" value="<?php echo $property_name_data; ?>" size="30" class="mf_text"/> 
				<br />
				
				<label for="property_value_data"><b>Property Value:</b></label> <br />
				<input type="text" name="property_value_data[]" value="<?php echo $property_value_data; ?>" size="30" class="mf_text"/> 
				<br />
				
				<div class="button remove_td left">Remove</div>
				<br clear="all" /> <br />
				</div> </div>
	  <?php }
		}
	}
	
	if (empty($current_property_name_data)){ ?>
		<div class="elem-td">
			<label for="property_name_data"><b>Property Name:</b></label> <br />
			<input type="text" name="property_name_data[]" size="30" class="mf_text"/> 
			<br />
			
			<label for="property_value_data"><b>Property Value:</b></label> <br />
			<input type="text" name="property_value_data[]" size="30" class="mf_text"/> 
			<br /> <br />
		</div>
	<?php
	$post = $post_backup;
	} ?>
	
	<div class="copy-correct-td">	
    </div>
    
    <a class="add_new_td button right">Add</a>
    
    <div class="clear"></div>

    <script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery(".add_new_td").click(function(data) {
			jQuery('.copy-correct-td').append(jQuery('.elem-td').html());
			//jQuery('.copy-correct input, .copy-correct select').val('');
		});
		jQuery(".remove_td").live('click', function() {
			jQuery(this).parents('.elem-td').remove();
		});
	});
    </script>	
<?php	
}


function show_test_steps2(){
	global $post;
	$post_backup = $post;
	$current_step_action= get_post_meta($post->ID, 'step_action', true);
	$current_step_expected= get_post_meta($post->ID, 'step_expected', true);
	echo '<input type="hidden" name="custom_test_data" value="', wp_create_nonce(basename(__FILE__)), '" />';
	?>
				
	<?php
	foreach($current_step_action as $key => $step_action){
		foreach($current_step_expected as $key2 => $step_expected){
			if( $key == $key2){ ?>
			<div class="elem-step"> <div class="elem-step"> 
				<label for="step_action"><b>Step <?php echo ($key+1); ?>. Action:</b></label> <br />
				<input type="text" name="step_action[]" value="<?php echo $step_action; ?>" size="30" class="mf_text"/> 
				<br />
				
				<label for="step_expected"><b>Step <?php echo($key+1); ?>. Expected Result:</b></label> <br />
				<input type="text" name="step_expected[]" value="<?php echo $step_expected; ?>" size="30" class="mf_text"/> 
				<br />
				
				<div class="button remove_step left">Remove</div>
				<br clear="all" /> <br />
				</div> </div>
	  <?php }
		}
	}
	
	if (empty($current_step_action)){ ?>
		<div class="elem-step">
			<label for="step_action"><b>Step Action:</b></label> <br />
			<input type="text" name="step_action[]" size="30" class="mf_text"/> 
			<br />
			
			<label for="step_expected"><b>Step Expected Result:</b></label> <br />
			<input type="text" name="step_expected[]" size="30" class="mf_text"/> 
			<br /> <br />
		</div>
	<?php
	$post = $post_backup;
	} ?>
	
	<div class="copy-correct-step">	
    </div>
    
    <a class="add_new_step button right">Add</a>
    
    <div class="clear"></div>

    <script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery(".add_new_step").click(function(data) {
			jQuery('.copy-correct-step').append(jQuery('.elem-step').html());
			//jQuery('.copy-correct input, .copy-correct select').val('');
		});
		jQuery(".remove_step").live('click', function() {
			jQuery(this).parents('.elem-step').remove();
		});
	});
    </script>	
<?php	
}



add_action('save_post', 'save_test_execution');

function save_test_execution($post_id) {
	// verify nonce
	if (!isset($_POST['custom_test_execution']) || !wp_verify_nonce($_POST['custom_test_execution'], basename(__FILE__))) {
	return $post_id;
}

    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
 
	// check permissions
     if (!current_user_can('edit_post', $post_id)) {
      return $post_id;
    }

    $test_suite = $_POST['test_suites'];
    update_post_meta($post_id, 'test_suites', $test_suite);
     
    $test_url = $_POST['test_url']; 
	update_post_meta($post_id, 'test_url', $test_url);
	$protocol_binding2 = $_POST['protocol_binding2']; 
	update_post_meta($post_id, 'protocol_binding2', $protocol_binding2);
	
	$property_name_exec = $_POST['property_name_exec']; 
	update_post_meta($post_id, 'property_name_exec', $property_name_exec);
	$property_value_exec = $_POST['property_value_exec']; 
	update_post_meta($post_id, 'property_value_exec', $property_value_exec);
	
	$property_name_data = $_POST['property_name_data']; 
	update_post_meta($post_id, 'property_name_data', $property_name_data);
	$property_value_data = $_POST['property_value_data']; 
	update_post_meta($post_id, 'property_value_data', $property_value_data);
	
	$step_expected = $_POST['step_expected']; 
	update_post_meta($post_id, 'step_expected', $step_expected);
	$step_action = $_POST['step_action']; 
	update_post_meta($post_id, 'step_action', $step_action);
	
	//Conformance Level 
	
	$conformance_level = $_POST['conformance_level'];
	update_post_meta($post_id, 'conformance_level', $conformance_level);
	
	// Initiating Message
	$message_type = $_POST['choose_init_messages'] ;
	update_post_meta($post_id, 'choose_init_messages',$message_type);
	
	//Roles
	$tester_role = $_POST['choose_tester_role'];
	update_post_meta($post_id, 'choose_tester_role',$tester_role);
	$harness_role = $_POST['choose_harness_role'];
	update_post_meta($post_id, 'choose_harness_role',$harness_role);
	$initiator = $_POST['choose_initiator'];
	update_post_meta($post_id, 'choose_initiator',$initiator);
} 
/* Metaboxes Test Suite Page */

function add_custom_metaboxes(){
	// add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $callback_args );
	/* Metabox Choose Test Cases*/
    add_meta_box("test_cases_metabox", "Test Cases Associated", 'show_test_cases', "test-suite", "normal", "high");
    /* Metabox Declare Initiating Message*/
    add_meta_box("initiating_message_metabox", "Initiating Messages", 'show_initiating_message', "test-suite", "normal", "high");
    /* Metabox Declare Roles*/
    add_meta_box("roles_ts_metabox", "Roles", 'show_roles_ts', "test-suite", "normal", "high");
    // Metabox Associate Communities
    add_meta_box("community_metabox", "Choose Community", 'show_community', "test-suite", "normal", "high");
}
add_action('admin_menu', 'add_custom_metaboxes');

function show_community(){
	global $wpdb;
	$groups_result = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "bp_groups");
	$group_result = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "bp_groups_testsuites WHERE ts_ids={$_GET['post']}");
	$group_id = $group_result->group_id;
	echo '<select name="group">';
	echo '<option value="">Choose Community</option>';
	foreach ( $groups_result as $group ) 
	{
		if ($group_id == $group->id) {
			$selected = 'selected="selected"';
			}
			else $selected ='';
		echo '<option value="'.$group->id.'" '.$selected.' >'.$group->name.'</option>';
	}
	echo '</select>';
	echo '<input type="hidden" value="'.$_GET['post'].'" name="postid"/>';
}

function show_roles_ts(){
	global $post;
	$post_backup = $post;
	$current_tester_role = get_post_meta($post->ID, 'tester_role_ts', true);
	$current_harness_role = get_post_meta($post->ID, 'harness_role_ts', true);
	$current_intiator = get_post_meta($post->ID, 'initiator_ts', true);
	//echo '<input type="hidden" name="custom_roles" value="', wp_create_nonce(basename(__FILE__)), '" />';
	?>
	<label for="tester_role_ts_id"><b>Tester Roles:</b></label> <br />
	<textarea name="tester_role_ts" id="tester_role_ts_id" rows="3" cols="100"><?php echo $current_tester_role; ?></textarea>
	<br /><span class="description">Tester Roles (comma separated)</span> 
	<br />
	<label for="harness_role_ts"><b>Harness Roles:</b></label> <br />
	<textarea name="harness_role_ts" id="harness_role_ts_id" rows="3" cols="100"><?php echo $current_harness_role; ?></textarea>
	<br /><span class="description">Harness Roles (comma separated)</span> 
	<br />
	<label for="harness_role_ts"><b>Initiators:</b></label> <br />
	<textarea name="initiator_ts" id="initiator_ts_id" rows="3" cols="100"><?php echo $current_intiator; ?></textarea>
	<br /><span class="description">Initiators (comma separated)</span> 
	<br />
	<?php	
	$post = $post_backup;
}

function show_initiating_message(){
	global $post;
	$post_backup = $post;
	$current_initiating_messages = get_post_meta($post->ID, 'init_message', true);
	//echo '<input type="hidden" name="custom_initiating_message" value="', wp_create_nonce(basename(__FILE__)), '" />';
	?>
	<textarea name="init_message" id="initiating_message_id" rows="4" cols="100"><?php echo $current_initiating_messages;?></textarea>
	<br /><span class="description">Type Initiating Messages (comma separated)</span>
<?php
	$post = $post_backup;	
}

function show_test_cases(){
	global $post;
	$post_backup = $post;
	$loop = new WP_Query( array( 'post_type' => 'test-case', 'posts_per_page' => -1) );
	$found = false;
	while ( $loop->have_posts() ) : $loop->the_post();
		$id = get_the_ID();
		$test_cases_assoc = get_post_meta($id, 'test_suites', true);
		//var_dump($test_cases_assoc);
		if (in_array($_GET['post'], $test_cases_assoc)){
			$found = true;
			echo '<div class="the_test_case">';
			echo '<a href="'.get_permalink().'" target="_blank"><b>'.get_the_title().'</b></a>';
			echo ' - ';
			echo '<a href="post.php?post='.$id.'&action=edit" target="_blank" style="margin-right: 10px;">Edit</a>';
			echo '<a class="action_testcase" data-action="hide_testcase" data-id="'.$id.'" style="margin-right: 10px; cursor:pointer; text-decoration: underline;">Hide</a>';
			echo '<a class="action_testcase" data-action="delete_testcase" data-id="'.$id.'" style="cursor:pointer; text-decoration: underline;">Delete</a>';
			echo '</div>';
		}
	endwhile;
	if (!$found){
		echo 'No test cases associated';
	}

	echo '<div class="clear"></div>';	
	echo '<a class="add_new_testcase button right" href="post-new.php?post_type=test-case&set_ts='.$_GET['post'].'" target="_blank">Add</a>';
	?>
	<div class="clear"></div>
	<!-- Script Hide / Delete Test Case
	-->
	<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery(document).on('click', '.action_testcase', function(){
			var the_id = jQuery(this).attr('data-id');
			var the_action = jQuery(this).attr('data-action');
			var get_parent = jQuery(this);
			
			var field_tc = {
				testcase_id : the_id,
				action : the_action
				};
			
			jQuery.ajax({
				url: window.location.href,
				data: field_tc,
				type:'POST',
				success: function(data){
					get_parent.parent().remove();
				},
				error: function(data){
				}
			});	
		});
	});
	</script>
	
	<?php
	$post = $post_backup;
}

function testcase_actions(){
	if(isset($_POST['testcase_id'])){
		if ($_POST['action'] == 'hide_testcase'){
			//Hide Test Case
			$test_case = array();
			$the_test_cases = get_post_meta($_POST['testcase_id'], 'test_suites', true);
			foreach ($the_test_cases as $the_test_case){
				if ($_GET['post'] != $the_test_case){
					array_push($test_case, $the_test_case);
					} 
				}
			update_post_meta($_POST['testcase_id'], 'test_suites', $test_case);
			
			
		}
		
		else if ($_POST['action'] == 'delete_testcase'){
			//Delete Test Case
			wp_delete_post($_POST['testcase_id'], true );
			
		}
		exit();
		
	}
}

add_action('admin_init', 'testcase_actions');

add_action('save_post', 'save_test_case_post');

function save_test_case_post($post_id) {
	// check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
 
	// check permissions
     if (!current_user_can('edit_post', $post_id)) {
      return $post_id;
    }
    $init_message = $_POST['init_message'];
    update_post_meta($post_id, 'init_message', $init_message);
    
    $test_cases = $_POST['test_cases']; 
	update_post_meta($post_id, 'test_cases', $test_cases);
	
	$tester_roles = $_POST['tester_role_ts'];
	update_post_meta($post_id, 'tester_role_ts', $tester_roles);
	
	$harness_roles = $_POST['harness_role_ts'];
	update_post_meta($post_id, 'harness_role_ts', $harness_roles);
	$initiators = $_POST['initiator_ts'];
	update_post_meta($post_id, 'initiator_ts', $initiators);

	if ( (isset($_POST['group'])) && (($_POST['group']) != '') ){
		//die($_POST['group']);
		global $wpdb;
		//die($_POST['postid']);
		$ts_result = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "bp_groups_testsuites WHERE ts_ids={$_POST['postid']} AND ts_ids={$_POST['postid']}");
		if($ts_result){
			$ts_id = $ts_result->ts_ids;
			$current_group_id = $ts_result -> group_id;
			print $ts_id .'<br />';
			if ($current_group_id != $_POST['group']) {
				$wpdb->update(
						$wpdb->prefix.'bp_groups_testsuites', //table
						array( 
							'group_id' => $_POST['group']	// data
						), 
						array( 'ts_ids' => $_POST['postid'] ), //where
						array( 
							'%d'	// format
						), 
						array( '%d' ) 
						 
					);
				}
			}
			else {
				global $wpdb;
				$wpdb->insert(
					$wpdb->prefix.'bp_groups_testsuites', //table
						array(
							'group_id' => $_POST['group'], //data
							'ts_ids' => $_POST['postid']
						), 
						array( 
							'%d', //format
							'%d'
						) 
					);
				}
		
		
		
	}
	else {
    // Not set yet	
		
	}
}

/*Metabox Choose Related Suites */
function add_ts_metaboxes(){
	// add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $callback_args );
    add_meta_box("ts_metabox", "Related Test Suites ", 'show_ts', "test-suite", "normal", "high");
}

add_action('admin_menu', 'add_ts_metaboxes');

function show_ts(){
	global $post;
	$post_backup = $post;
	$current_ts = get_post_meta($post->ID, 'ts', true);
	$current_desc = get_post_meta($post->ID, 'ts_desc', true);
	//print_r ($current_ts);
	
	//echo '<input type="hidden" name="custom_ts" value="', wp_create_nonce(basename(__FILE__)), '" />';
	$loop = new WP_Query( array( 'post_type' => 'test-suite', 'posts_per_page' => -1, 'post__not_in' =>array($post->ID) ) );
	?>
	<div id="rel_suite"> 
		<div class="elements">	
		<?php
		foreach($current_desc as $key => $post_desc){
			foreach ($current_ts as $key2 => $post_sel){
				if($key == $key2) {?>
				<div class="elem">  <div class="elem">
					<label for="ts"><b>Related Suites: </b></label> <br />
					<select name="ts[]">
						<option value="">Choose Related Suite</option>
						<?php
						while ( $loop->have_posts() ) : $loop->the_post();
							 ?>
							 
							 <option <?php if (get_the_ID() == $post_sel) { echo 'selected="selected"'; } ?> value="<?php the_ID(); ?>" style="margin-right: 5px; margin-bottom: 5px;"><?php the_title(); ?> </option>
							<?php
						endwhile;
						?>
					</select>
					<div class="button remove left">Remove</div>
					<br clear="all" />
					
					<label for="ts_desc"><b>Description</b></label> <br />
					<input type="text" name="ts_desc[]" value="<?php echo $post_desc; ?>" size="30" class="mf_text"/> 
					<br /><span class="description">Description for this Related Suite</span>
					<br /> <br />
				</div> </div>
				
				<?php }
				}
			 }
			 if (empty($current_desc)){
				 ?>
				 <div class="elem"> 
					<label for="ts"><b>Related Suites: </b></label> <br />
					<select name="ts[]">
						<option value="">Choose Related Suite</option>
						<?php
						while ( $loop->have_posts() ) : $loop->the_post();
							 ?>
							 <option <?php // if (get_the_ID() == $post_sel) { echo 'selected="selected"'; } ?> value="<?php the_ID(); ?>" style="margin-right: 5px; margin-bottom: 5px;"><?php the_title(); ?> </option>
							<?php
						endwhile;
						?>
					</select>
					<div class="button remove left">Remove</div>

				<br clear="all" />
					<label for="ts_desc"><b>Description</b></label> <br />
					<input type="text" name="ts_desc[]" size="30" class="mf_text"/> 
					<br /><span class="description">Description for this Related Suite</span>
					<br /> <br />
				</div> 
				 <?php
				 }
		 ?>
		 </div>
	</div>

	<div class="copy-correct">
		
    </div>
    
    <a class="add_new button right">Add</a>

    <div class="clear"></div>
    
	<?php
	$post = $post_backup;
	?>
	<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery(".add_new").click(function(data) {
			jQuery('.copy-correct').append(jQuery('.elem').html());
			//jQuery('.copy-correct input, .copy-correct select').val('');
		});
		jQuery(".remove").live('click', function() {
			jQuery(this).parents('.elem').remove();
		});
	});
    </script>	
	<?php
}

add_action('save_post', 'save_ts');

function save_ts($post_id) {
	// verify nonce
	if (!isset($_POST['custom_ts']) || !wp_verify_nonce($_POST['custom_ts'], basename(__FILE__))) {
	//return $post_id;
}

    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
 
	// check permissions
     if (!current_user_can('edit_post', $post_id)) {
      return $post_id;
    }
    
    $ts = $_POST['ts'] ;
    $ts_desc = $_POST['ts_desc'];

	//print_r($_POST['ts']);
	update_post_meta($post_id, 'ts', $ts);
	update_post_meta($post_id, 'ts_desc', $ts_desc);
}


/*Metabox Specification Documents */
function add_spec_doc_metaboxes(){
	// add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $callback_args );
   add_meta_box("specdoc_metabox", "Specification Documents", 'show_spec_doc', "test-suite", "normal", "high");
}

add_action('admin_menu', 'add_spec_doc_metaboxes');

function show_spec_doc(){
	global $post, $wpdb;
	$post_backup = $post;
	$myrows = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "ts_options_documents WHERE ts_id={$_GET['post']}");
	foreach($myrows as $row){
		$doc_name = $row->doc_name;
		$doc_desc = $row->doc_desc;
		$doc_loc = $row->doc_loc_url;
		$doc_file_name = $row->doc_file_name;
		$doc_file_url = $row->doc_loc_url;
		echo '<div class="elem2"> <div class="elem2">
				<input type="hidden" name="doc_id[]" value="'.$row->id.'"/>
				<label for="doc_name"><b>Document Name: </b></label> <br />
				<input type="text" name="doc_name[]" value="'.$doc_name.'" size="30" class="mf_text"/> 
				<br clear="all" />
				<label for="doc_desc"><b>Document Description: </b></label> <br />
				<input type="text" name="doc_desc[]" value="'.$doc_desc.'" size="30" class="mf_text"/>
				<br clear="all" /> <br clear="all" />	
				<label for="doc_loc"><b>Document Location: </b></label> <br />
				<input type="text" name="doc_loc[]" value="'.$doc_loc.'" size="30" class="mf_text"/> 					
				<br clear="all" />
				OR<br />
				<label for="doc_upload"><b>Upload a Document: </b></label> 
				<br /><a href="'.$doc_file_url.'" target="_blank">'.$doc_file_name.'</a><br clear="all" /> ';
				echo '<div class="button remove_doc left" data-id="'.$row->id.'">Remove</div> </div></div>';
		}
	
	if(empty($myrows)){
		echo '<div class="elem2">
					<input type="hidden" name="doc_id[]" value="0" />
					<label for="doc_name"><b>Document Name: </b></label> <br />
					<input type="text" name="doc_name[]" size="30" class="mf_text"/> 
					<br clear="all" />
					
					<label for="doc_desc"><b>Document Description:</b></label> <br />
					<input type="text" name="doc_desc[]" size="30" class="mf_text"/> 
					<br clear="all" />
					<label for="doc_loc"><em>Provide a </em><b>Document Location URL:</b></label> <br />
					<input type="text" name="doc_loc[]" size="30" class="mf_text"/> 
					<br clear="all" />
					<label for="doc_upload"><em>OR </em><b>Upload a Document: </b></label> <br />
					<input type="file" name="attachment_doc[]">
					<br clear="all" />
					<div class="button remove_doc left">Remove</div>
					<br clear="all" />
				</div>';
		}	
	?>
	<input type="hidden" name="testsuiteid" value="<?php echo $post->ID;?>">

	<div class="copy-correct-docs">
    </div>
    

    <a class="add_new_doc button right">Add</a>

    <div class="clear"></div>
    
    <style type="text/css">
		
		.right { float:right; }
		.clear { clear: both; }		
    </style>

    <script type="text/javascript">
    jQuery(document).ready(function() {
		jQuery('.add_new_doc').click(function(data) {
			jQuery('.copy-correct-docs').html('<input type="hidden" name="doc_id[]" value="0" /> <label for="doc_name"><b>Document Name: </b></label> <br /><input type="text" name="doc_name[]" size="30" class="mf_text"/> <br clear="all" /><label for="doc_desc"><b>Document Description:</b></label> <br /> <input type="text" name="doc_desc[]" size="30" class="mf_text"/> <br clear="all" /> <label for="doc_loc"><em>Provide a </em><b>Document Location URL:</b></label> <br />	<input type="text" name="doc_loc[]" size="30" class="mf_text"/> <br clear="all" /> <label for="doc_upload"><em>OR </em><b>Upload a Document: </b></label> <br /> <input type="file" name="attachment_doc[]"> <br clear="all" /> <div class="button remove_doc left">Remove</div> <br clear="all" />');
		});
		
		jQuery('.remove_doc').live('click', function(data) {
			/*
			jQuery(this).parents('.elem2').remove();*/
			var doc_id = jQuery(this).attr('data-id');
			var elem = this;
			jQuery.post(HOMEURL + '?doc_id=' + doc_id + '&action=deletedoc',
				{}, function(data){
					jQuery(elem).parents('.elem2').remove();
			});
			
		});
		jQuery('form#post').attr('enctype','multipart/form-data');
	});
    </script>	
	
	<?php
	$post = $post_backup;
}

add_action('save_post', 'save_spec_docs');

function save_spec_docs($post_id) {
	static $visited = false;
	if ($visited) {
		return;
	} else {
		$visited = true;
	}
	global $wpdb;
    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
 
	// check permissions
     if (!current_user_can('edit_post', $post_id)) {
      return $post_id;
    }

	$ts_id = $_POST['testsuiteid'];
    $docs_name = $_POST['doc_name'];
    $docs_desc = $_POST['doc_desc'];
    $docs_loc = $_POST['doc_loc'];
    $docs_file = $_FILES['attachment_doc'];
    $docs_id = $_POST['doc_id'];
    
    //echo '<pre>'.print_r($docs_file, true).'</pre>';die();
    foreach ($docs_name as $key => $doc_name) {
		$doc_desc = $docs_desc[$key];
		$doc_loc = $docs_loc[$key];
		$doc_desc = $docs_desc[$key];
		$doc_id = $docs_id[$key];
		$dest = '';
		if (!$doc_loc) {
			//Process attachments
			$doc_file = array(
				'name' => $docs_file['name'][$key],
				'tmp_name' => $docs_file['tmp_name'][$key],
				'error' => $docs_file['error'][$key],
				'size' => $docs_file['size'][$key],
				'type' => $docs_file['type'][$key],
			);
			if ($doc_file['error'] != 0) {
				continue;
			}
			$uploads = wp_upload_dir();
			$uploads_dir = $uploads['basedir'].DIRECTORY_SEPARATOR.'docs_attachments';
			$url_dir = $uploads['baseurl'].DIRECTORY_SEPARATOR.'docs_attachments'.DIRECTORY_SEPARATOR;
			if(!file_exists($uploads_dir)){
				mkdir($uploads_dir);
			}
			if (file_exists($uploads_dir.DIRECTORY_SEPARATOR.$doc_file['name'])) {
				$i = 1;
				while (file_exists($uploads_dir.DIRECTORY_SEPARATOR.$i.'-'.$doc_file['name'])) {
					$i++;
				}
				$dest = $uploads_dir.DIRECTORY_SEPARATOR.$i.'-'.$doc_file['name'];
				$url = $url_dir.$i.'-'.$doc_file['name'];
			} else {
				$dest = $uploads_dir.DIRECTORY_SEPARATOR.$doc_file['name'];
				$url = $url_dir.$doc_file['name'];
			}
			move_uploaded_file($doc_file['tmp_name'], $dest);
			$doc_loc = $url;
		}
		if($doc_id == 0){
			echo $doc_id.' ';
			//INSERT
			$wpdb->insert(
				$wpdb->prefix.'ts_options_documents', 
				array( 
					'ts_id' => $ts_id,
					'doc_name' => $doc_name,
					'doc_desc' => $doc_desc,
					'doc_loc_url' => $doc_loc,
					'doc_file_name'=> $doc_file['name'],
					'doc_file_path' => $dest
					
				), 
				array( 
					'%d',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s'
				)
			);
		}
		else {
			echo $doc_id.' ';
		//UPDATE
			$wpdb->update(
				$wpdb->prefix.'ts_options_documents', 
				array( 
					'ts_id' => $ts_id,
					'doc_name' => $doc_name,
					'doc_desc' => $doc_desc,
					'doc_loc_url' => $doc_loc,
					'doc_file_name'=> $doc_file['name'],
					'doc_file_path' => $dest
				), 
				array( 
					'id' => $doc_id 
				),
				array(
					'%d',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s'				
				) , 
				 
				array( 
					'%d'	// value2
				)
			);
		}
	}
}

/*Metabox Conformance Levels */
function add_conf_levels_metaboxes(){
	// add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $callback_args );
    add_meta_box("conf_levels_metabox", "Conformance Levels", 'show_conf_levels', "test-suite", "normal", "high");
}

add_action('admin_menu', 'add_conf_levels_metaboxes');

function show_conf_levels(){
	global $post;
	$post_backup = $post;
	$current_lvl_code = get_post_meta($post->ID, 'lvl_code', true);
	$current_lvl_desc= get_post_meta($post->ID, 'lvl_desc', true);
		
	//echo '<input type="hidden" name="custom_lvl" value="', wp_create_nonce(basename(__FILE__)), '" />';
	?>
	<div id="suites3">
		<div class="elements3">
		<?php
		foreach($current_lvl_code as $key => $lvl_code){
		  foreach ($current_lvl_desc as $key2 => $lvl_desc){
			    if ($key == $key2) { ?>
				<div class="elem3"><div class="elem3">
					<label for="lvl_code"><b>Conformance Level Code:</b></label> <br />
					<input type="text" name="lvl_code[]" value="<?php echo $lvl_code; ?>" size="30" /> 

					<br clear="all" />
					<label for="lvl_desc"><b>Conformance Level Description:</b></label> <br />
					<input type="text" name="lvl_desc[]" value="<?php echo $lvl_desc; ?>" size="30" class="mf_text"/> 
					
					<br clear="all" />
					
					<div class="button remove_lvl left">Remove</div>
					<br /> <br />
				</div> </div>
				
				<?php }
					}
				  }

			 if (empty($current_lvl_code)){
				 ?>
				 <div class="elem3"> <div class="elem3">
					<label for="lvl_code"><b>Conformance Level Code:</b></label> <br />
					<input type="text" name="lvl_code[]" size="30" /> 
					<br clear="all" />
					
					<label for="lvl_desc"><b>Conformance Level Description:</b></label> <br />
					<input type="text" name="lvl_desc[]" size="30" class="mf_text"/> 
					<br clear="all" />
					
					<div class="button remove_lvl left">Remove</div>
					<br /> <br />
				</div> </div>
				 <?php
				 }
		 ?>
	</div>

	<div class="copy-correct-lvl">

	</div>
    
    </div>
    <a class="add_new_lvl button right">Add</a>

    <div class="clear"></div>

    <script type="text/javascript">
    jQuery(document).ready(function() {		
		jQuery('.add_new_lvl').click(function() {
			 jQuery('.copy-correct-lvl').append(jQuery('.elem3').html());
			// jQuery('.copy-correct-docs input').val('');
		});
		jQuery('.remove_lvl').live('click', function() {
			jQuery(this).parents('.elem3').remove();
		});

	});
    </script>	

	<?php
	$post = $post_backup;
}

add_action('save_post', 'save_conf_level');

function save_conf_level($post_id) {
	// verify nonce
	if (!isset($_POST['custom_lvl']) || !wp_verify_nonce($_POST['custom_lvl'], basename(__FILE__))) {
	//return $post_id;
	}

    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
 
	// check permissions
     if (!current_user_can('edit_post', $post_id)) {
      return $post_id;
    }
    
    $lvl_code = $_POST['lvl_code'];
    $lvl_desc = $_POST['lvl_desc'] ;
    
	update_post_meta($post_id, 'lvl_code', $lvl_code);
	update_post_meta($post_id, 'lvl_desc', $lvl_desc);
}


/*--------------------------------------------------
Check captcha
--------------------------------------------------*/
if (isset($_GET['check_captcha'])) {
	if (!isset($_SESSION)) {
		session_start();
	}
	if ($_POST['captcha'] == $_SESSION['captcha']) {
		echo 'success';
	} else {
		echo 'error';
	}
	exit();
}


/*--------------------------------------------------
Create new user account
--------------------------------------------------*/
function set_html_content_type()
{
	return 'text/html';
}

function send_email_verification($email, $username, $password){
    //$headers[] = 'From: Nego Office <office@nego-solutions.com>';
	//$headers[] = 'Cc: John Q Codex <jqc@wordpress.org>';
	//$headers[] = 'Cc: iluvwp@wordpress.org'; // note you can just use a simple email address

	$to = $email;
	$subject = 'ComplianceTest Confirmation Email';
    
    if($username !='' && $password!= ''){
        $message = 'Username: ';
        $message .= $username;
        $message .= '<br />';
        $message .= 'Password: ';
        $message .= $password;
    }
	$message .= '<br />To activate you account click the link below <br />';
	$message .= $_SERVER['SERVER_NAME'];
	$message .= $_SERVER['REQUEST_URI'];
	$message .='?user_activation=';
	$message .= md5($email);

	add_filter( 'wp_mail_content_type', 'set_html_content_type' );
	return wp_mail( $to, $subject, $message, $headers );
	remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
}



function create_new_user(){
	global $wpdb;
    
	$user_id = wp_create_user( $_POST['user_login'], $_POST['user_pass'], $_POST['user_email'] );  
	//die(print_r($user_id)); 
    
	wp_update_user( array ('ID' => $user_id, 'first_name' => $_POST['first_name'], 'last_name' => $_POST['last_name'])) ;
	
    $activation_key =  md5($_POST['user_email']);
	$wpdb->query("UPDATE $wpdb->users SET user_activation_key = '$activation_key', user_status=3 WHERE ID ='$user_id' ");

	update_user_meta ($user_id, 'user_organisation', $_POST['organisation']);
	update_user_meta ($user_id, 'contact_phone', $_POST['contact_phone']);
	
    send_email_verification($_POST['user_email'], $_POST['user_login'], $_POST['user_pass']);
    
    //auto login user
    wp_set_auth_cookie($user_id);
}

if  (isset($_POST['form_set'])){
	add_action('template_redirect', 'create_new_user');
}

if (isset($_POST['resend_email_verification'])){
    
    global $current_user;
    
    send_email_verification($_POST['uemail'], '', '');
    //echo $_POST['uemail'].'=>'.$_POST['uname'];
    echo 'success';
    exit();
}



/*--------------------------------------------------
Login With Email address 
--------------------------------------------------*/
function login_with_email_address($username) {
    $user = get_user_by_email($username);
    if(!empty($user->user_login))
        $username = $user->user_login;
    return $username;
}
add_action('wp_authenticate','login_with_email_address');



/*--------------------------------------------------
PROGRESS LOGIN
--------------------------------------------------*/
if(isset($_POST['user_log'])){
    
    $parsUsername = $_POST['log'];
    $parsPassword = $_POST['pwd'];
    $email_regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/'; 
    
    //check the username type (email/user)
    if(preg_match($email_regex, $parsUsername)){
        $get_user = get_user_by('email', $parsUsername);
    }else{
        $get_user = get_user_by('login', $parsUsername);
    }
    
    //check if user and pass are correct
    if ( $get_user && wp_check_password( $parsPassword, $get_user->data->user_pass, $get_user->ID) ){

        /*$user_status = $get_user->user_status;
        
        //check user status (active/inactive)
        if($user_status > 0){
            die('inactive'); 
             exit();
        }else{
            echo'active';
            exit();
        }*/
         echo'active';
         exit();

    }else{
        die('wrong');
    }
    
}


//redirect to user profile after login
function custom_login_redirect( $redirect_to, $request, $user ){
    return home_url().'/my-profile';
}
add_filter( 'login_redirect', 'custom_login_redirect', 10, 3 );



/*--------------------------------------------------
My Details updates
--------------------------------------------------*/
if(isset($_POST['my_details_edit'])){
    
    $user_id = $_POST['user_id'];
    $uname = explode(' ', $_POST['uname']);
    $user_email = email_exists( $_POST['email']);
    $email_regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/'; 
    $newPass = $_POST['new_pass'];
    $confPass = $_POST['conf_pass'];
    $errors = 'no_errors';
    
    //update user name
    if($_POST['uname'] && $_POST['uname']!=''){
        update_user_meta($user_id, 'first_name', $uname[1]);
        update_user_meta($user_id, 'last_name', $uname[0]);
    }
    
    //update user email
    if(isset($_POST['email']) && preg_match($email_regex, $_POST['email'])){
        if($user_email==false){
            wp_update_user(array('ID' => $user_id, 'user_email' => esc_attr( $_POST['email'])));

        }else if($user_email!=$user_id){
           $errors = 'This email address already exists!';
        }
    }else{
        $errors = 'This email address is not valid!';
    }
    
    //update user passwords
    if(isset($confPass) && $confPass!=''){
    
        if($newPass == $confPass){
            $user_pass = get_userdata($_POST['user_id']);
            
            wp_update_user( array ('ID' => $user_id, 'user_pass' => $confPass) ) ;

        }else{
            $errors = 'The passwords do no match!';
        }
    }
 
    echo $errors;
    
    //die('success');
    exit();
}


/*--------------------------------------------------
My Payment Method updates
--------------------------------------------------*/
//check card number
function check_cc($cc, $extra_check = false){
    $cards = array(
        "visa" => "(4\d{12}(?:\d{3})?)",
        "amex" => "(3[47]\d{13})",
        "jcb" => "(35[2-8][89]\d\d\d{10})",
        "maestro" => "((?:5020|5038|6304|6579|6761)\d{12}(?:\d\d)?)",
        "solo" => "((?:6334|6767)\d{12}(?:\d\d)?\d?)",
        "mastercard" => "(5[1-5]\d{14})",
        "switch" => "(?:(?:(?:4903|4905|4911|4936|6333|6759)\d{12})|(?:(?:564182|633110)\d{10})(\d\d)?\d?)",
    );
    $names = array("Visa", "American Express", "JCB", "Maestro", "Solo", "Mastercard", "Switch");
    $matches = array();
    $pattern = "#^(?:".implode("|", $cards).")$#";
    $result = preg_match($pattern, str_replace(" ", "", $cc), $matches);
    if($extra_check && $result > 0){
        //$result = (validatecard($cc))?1:0;
    }
    return ($result>0)?$names[sizeof($matches)-2]:false;
}

//check the expiry card date
function check_exp_date($month, $year) {
    
    /* Get timestamp of midnight on day after expiration month. */
    $exp_ts = mktime(0, 0, 0, $month + 1, 1, $year);

    $cur_ts = time();
    /* Don't validate for dates more than 10 years in future. */
    $max_ts = $cur_ts + (10 * 365 * 24 * 60 * 60);

    if ($exp_ts > $cur_ts && $exp_ts < $max_ts) {
        return true;
    } else {
        return false;
    }
}


//save my payment method
if(isset($_POST['my_payment_edit'])){
    
    $user_id = $_POST['user_id'];
    $card_number = str_replace(' ', '', $_POST['card_number']);
    $name_on_card = $_POST['name_on_card'];
    $card_expiry = explode('/', $_POST['card_expiry']);
    $card_cvc = $_POST['card_cvc'];
    
    $errors = 'no_errors';
    
    $check = check_cc($card_number);//4533345657653245
    
    
    if($check && $card_number!=''){
        update_user_meta( $user_id, 'card_number', $card_number);
    }else{
        $errors = 'Credit card invalid. Please make sure that you entered a valid card number';
    }
    
    
    if($name_on_card!=''){
        update_user_meta( $user_id, 'name_on_card', $name_on_card);
    }
    
    if($_POST['card_expiry']==''){
        $errors = 'Please specify your card expiry date!';
    }else{
        if($card_expiry[0] > 12){
            $errors = 'Your expiry date is incorrect!';

        }else if(check_exp_date($card_expiry[0], $card_expiry[1])){
            update_user_meta( $user_id, 'card_expiry', $_POST['card_expiry']); 
        }else{
            $errors = 'Your card has expired or your expiry date is incorrect!';
        }
    }
    
    
    if($card_cvc!='' && (strlen($card_cvc)==3 || strlen($card_cvc)==4)){
        update_user_meta( $user_id, 'card_cvc', $card_cvc);
    }else{
        $errors = 'Your CVC code is incorrect';
    }
    
    echo $errors;
    
    exit();
}


/*--------------------------------------------------
My Organisation updates
--------------------------------------------------*/
if(isset($_POST['my_organisation_edit'])){
    
    $user_id = $_POST['user_id'];
    $user_organisation = $_POST['user_organisation'];
    $user_organisation_web = $_POST['user_organisation_web'];
    $user_organisation_desc = $_POST['user_organisation_desc'];
    $user_organisation_abn = $_POST['user_organisation_abn'];
    
    $errors = 'no_errors';
    
    update_user_meta($user_id, 'user_organisation', $user_organisation);
    update_user_meta($user_id, 'user_organisation_web', $user_organisation_web);
    update_user_meta($user_id, 'user_organisation_desc', $user_organisation_desc);
    update_user_meta($user_id, 'user_organisation_abn', $user_organisation_abn);
    
    echo $errors;
    
    exit();
}


/* Frontend Add new Test Suite */
function insert_attachment($file_handler,$post_id,$setthumb=false) {

	// check to make sure its a successful upload
	if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) __return_false();

	require_once(ABSPATH . "wp-admin" . '/includes/image.php');
	require_once(ABSPATH . "wp-admin" . '/includes/file.php');
	require_once(ABSPATH . "wp-admin" . '/includes/media.php');

	$attach_id = media_handle_upload( $file_handler, $post_id );

	if ($setthumb) update_post_meta($post_id,'_thumbnail_id',$attach_id);
	return $attach_id;
}

function process_add_recipe()
{
	if(isset($_POST['ts_name_frontend'])){
        if (isset($_FILES['upload_attachment']['name'][0]) && !($_FILES['upload_attachment']['error'][0])) {
            $img_info = getimagesize($_FILES['upload_attachment']['tmp_name'][0]);
            if ($img_info[0] < 470 || $img_info[1] < 325) {
               // return;
            }
        }
        
        global $current_user;
        get_currentuserinfo();
        
      //  die('<pre>'.print_r($_FILES, true).'</pre>');
		
        $post_testsuite = wp_insert_post(array(
			'post_status' => 'publish',
			'post_type' => 'test-suite',
			'post_author' => $current_user->ID,//1, //(admin)
			'post_title' => $_POST['ts_name_frontend']
		));	
		
		if ($post_testsuite) {
			// Test Suite Information
			update_post_meta($post_testsuite, 'ts_name', $_POST['ts_name_frontend']);
            update_post_meta($post_testsuite, 'ts_identifier', $_POST['ts_identifier']);
            update_post_meta($post_testsuite, 'ts_issue_date', $_POST['ts_issue_date']);
            update_post_meta($post_testsuite, 'ts_issuer', $_POST['ts_issuer']);
            update_post_meta($post_testsuite, 'ts_status', $_POST['ts_status']);
            update_post_meta($post_testsuite, 'ts_revision_description', $_POST['ts_revision_description']);
            update_post_meta($post_testsuite, 'ts_version', $_POST['ts_version']);
            
            // Initiating Message
            update_post_meta($post_testsuite, 'init_message', $_POST['init_message']);
            
            // Conformance Levels
            update_post_meta($post_testsuite, 'lvl_code', $_POST['lvl_code']);
            update_post_meta($post_testsuite, 'lvl_desc', $_POST['lvl_desc']);
            
            // Test Cases
            update_post_meta($post_testsuite, 'test_cases', $_POST['test_cases']);
            
            // Related Test Suites
            update_post_meta($post_testsuite, 'ts', $_POST['ts']);
            update_post_meta($post_testsuite, 'ts_desc', $_POST['ts_desc']);
            
            // Roles
            update_post_meta($post_testsuite, 'tester_role_ts', $_POST['tester_role_ts']);
            update_post_meta($post_testsuite, 'harness_role_ts', $_POST['harness_role_ts']);
            update_post_meta($post_testsuite, 'initiator_ts', $_POST['initiator_ts']);
            
            // Specification Documents
            update_post_meta($post_testsuite, 'doc_type', $_POST['doc_type']);
            update_post_meta($post_testsuite, 'doc_name', $_POST['doc_name']);
            update_post_meta($post_testsuite, 'doc_loc', $_POST['doc_loc']);
            update_post_meta($post_testsuite, 'doc_desc', $_POST['doc_desc']);

		       
        } else {
            return;
        }

		if ( $post_testsuite && $_FILES ) {
		
			$files = $_FILES['upload_attachment'];
			//die($files);
			foreach ($files['name'] as $key => $value) {
				if ($files['name'][$key]) {
					$file = array(
					'name' => $files['name'][$key],
					'type' => $files['type'][$key],
					'tmp_name' => $files['tmp_name'][$key],
					'error' => $files['error'][$key],
					'size' => $files['size'][$key]
					);

					$_FILES = array("upload_attachment" => $file);

					foreach ($_FILES as $file => $array) {
						$newupload = insert_attachment($file, $post_testsuite, true);
					}
				}
			}
		}
		
        
		
        header('Location: '.get_permalink($post_testsuite));
        die();
       //echo '<script type="text/javascript">window.location.href = "'.get_permalink($post_recipe).'";</script>' 
	}
}

process_add_recipe();

add_action('admin_head','print_vars');
add_action('wp_head','print_vars');
function print_vars(){
?>
<script type="text/javascript">
var HOMEURL = "<?php echo get_home_url(); ?>";
</script>
<?php 
}

function remove_file($id){
	global $wpdb;
	$result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}bp_groups_downloads WHERE id={$id}");
	unlink($result->location);
	$wpdb->query("DELETE FROM {$wpdb->prefix}bp_groups_downloads WHERE id={$id}");
}

if((isset($_GET['action'])) && ($_GET['action'] == 'deletefile') ){
	add_action('template_redirect','ajax_remove_file');
}
function ajax_remove_file() {
	remove_file($_GET['file_id']);
	exit();
}

//Remove Docs
function remove_doc($id_doc){
	global $wpdb;
	$result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ts_options_documents WHERE id={$id_doc}");
	unlink($result->doc_file_path);
	$wpdb->query("DELETE FROM {$wpdb->prefix}ts_options_documents WHERE id={$id_doc}");
}

if((isset($_GET['action'])) && ($_GET['action'] == 'deletedoc') ){
	add_action('template_redirect','ajax_remove_doc');
}
function ajax_remove_doc() {
	remove_doc($_GET['doc_id']);
	exit();
}

//Renaming Buddypress Documents to "WIKI"

function edit_admin_menus() {  
    global $menu;  
    global $submenu;  
   // echo '<pre>'.print_r($menu).'</pre>';
    $menu[26][0] = 'Wiki'; // Change Buddypress Docs to Wiki   
    $submenu['edit.php?post_type=bp_doc'][5][0] = 'All Wikis';     
    $submenu['edit.php?post_type=bp_doc'][10][0] = 'Add New Wiki';  
    
    $menu[102][0] = 'Communities'; // Change Gropus to Communities 

}  
add_action( 'admin_menu', 'edit_admin_menus' );  

add_action( 'wp_login_failed', 'my_front_end_login_fail' );  // hook failed login

function my_front_end_login_fail( $username ) {
//	die('111');
   $referrer = $_SERVER['HTTP_REFERER'];  // where did the post submission come from?
   // if there's a valid referrer, and it's not the default log-in screen
   if ( !empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin') ) {
      wp_redirect( $referrer . '?login=failed' );  // let's append some information (login=failed) to the URL for the theme to use
      exit;
   }
}

// Login page fix
function my_check_password_reset_key($key, $login) {
    global $wpdb;

    $key = preg_replace('/[^a-z0-9]/i', '', $key);

    if ( empty( $key ) || !is_string( $key ) )
	return new WP_Error('invalid_key', __('Invalid key'));
    if ( empty($login) || !is_string($login) )
	return new WP_Error('invalid_key', __('Invalid key'));

    $user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->users WHERE user_activation_key = %s AND user_login = %s", $key, $login));

    if ( empty( $user ) )
	return new WP_Error('invalid_key', __('Invalid key'));

    return $user;
}
