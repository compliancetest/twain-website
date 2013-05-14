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
	wp_deregister_script('jquery');
	
	//wp_enqueue_script('jquery_min', locate_template().'/js/jquery-1.7.2.min.js');
	if (stripos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) {
		wp_enqueue_script('pie', template_location(false).'/js/PIE.js', array('jquery_min'));
		$actions_depends = array('jquery_min', 'pie');
	} else {
		$actions_depends = array('jquery_min');
	}
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
	
	echo '<input type="hidden" name="custom_test_suites" value="', wp_create_nonce(basename(__FILE__)), '" />';
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
	
	echo '<input type="hidden" name="custom_relprod" value="', wp_create_nonce(basename(__FILE__)), '" />';
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
    add_meta_box("test_execution_metabox", "Test Execution", 'show_test_execution', "test-case", "normal", "high");
    add_meta_box("test_data_metabox", "Test Data", 'show_test_data', "test-case", "normal", "high");
    add_meta_box("test_steps_metabox", "Test Steps 2", 'show_test_steps2', "test-case", "normal", "high");
    add_meta_box("choose_initiating_message_metabox", "Choose Initiating Message", 'show_choose_initiating_message','test-case',"normal","high");
    add_meta_box("choose_roles_metabox", "Choose Roles", 'show_choose_roles','test-case',"normal","high");
}

add_action('admin_menu', 'add_test_execution_metaboxes');

function show_choose_roles(){
	global $post;
	$post_backup = $post;
	$id = $post->ID;
	
	global $wp_query;
	query_posts(array(
		'post_type' => 'test-suite'
	));
	while(have_posts()) : the_post();
		$tester_roles = get_post_meta($post->ID, 'tester_role_ts');
		$harness_roles = get_post_meta($post->ID, 'harness_role_ts');
		$initiators = get_post_meta($post->ID, 'initiator_ts');
		
		$test_cases = get_post_meta($post->ID, 'test_cases');
		
		//Get current Values
		$current_tester_role = get_post_meta($post->ID, 'tester_role');
		$current_harness_role = get_post_meta($post->ID, 'harness_role');
		$current_initiator = get_post_meta($post->ID, 'initiator_ts');
		
		if (in_array($id , $test_cases[0])) {
			//$messages = explode(',',$init_message[0]);
			$testers = explode(',',$tester_roles[0]);
			$selected_tester = get_post_meta($id, 'choose_tester_role', true);
			
			$harnesses = explode(',',$harness_roles[0]);
			$selected_harness = get_post_meta($id, 'choose_harness_role', true);
			
			$initiators = explode(',',$initiators[0]);
			$selected_initiator = get_post_meta($id, 'choose_initiator', true);
			
			?>
			<label for="choose_tester_role"><b>Tester Role</b></label><br />
			<select name="choose_tester_role">
				<option value="">Choose Tester Role</option>
			<?php	
			foreach($testers as $tester){ ?>
				<option value="<?php echo $tester;?>" <?php if ($tester == $selected_tester) { echo 'selected="selected"'; } ?> ><?php echo $tester ; ?></option>
			<?php 
			}
			?>
			</select> <br />
			
			<label for="choose_harness_role"><b>Harness Role</b></label><br />
			<select name="choose_harness_role">
				<option>Choose Harness Role</option>
			<?php	
			foreach($harnesses as $harness){ ?>
				<option value="<?php echo $harness;?>" <?php if ($harness == $selected_harness) { echo 'selected="selected"'; } ?> ><?php echo $harness ; ?></option>
			<?php 
			}
			?>
			</select> <br />
			
			
			<label for="choose_initiator"><b>Initiator</b></label><br />
			<select name="choose_initiator">
				<option>Choose Initiator</option>
			<?php	
			foreach($initiators as $initiator){ ?>
				<option value="<?php echo $initiator;?>" <?php if ($initiator == $selected_initiator) { echo 'selected="selected"'; } ?> ><?php echo $initiator ; ?></option>
			<?php 
			}
			?>
			</select> <br />
			<?php
			
		}
	endwhile;
	wp_reset_query();
	$post = $post_backup;
	}

function show_choose_initiating_message(){
	global $post;

	$post_backup = $post;
	$id = $post->ID;
	//the_field('ts_name');
	
	global $wp_query;
	query_posts(array(
		'post_type' => 'test-suite'
	));
	while(have_posts()) : the_post();
		$init_message = get_post_meta($post->ID, 'init_message');
		//echo '<pre>'. print_r($init_message) .'</pre>';
		$test_cases = get_post_meta($post->ID, 'test_cases');
		//echo '<pre>'. print_r($test_cases[0]) .'</pre>';
		if (in_array($id , $test_cases[0])) {
			$messages = explode(',',$init_message[0]);
			$selected = get_post_meta($id, 'choose_init_messages', true);
			?>
			<select name="choose_init_messages">
				<option>Choose Initating Message</option>
			<?php	
			foreach($messages as $key => $message){ ?>
				<option value="<?php echo $message;?>" <?php if ($message == $selected) { echo 'selected="selected"'; } ?> ><?php echo $message ; ?></option>
			<?php 
			}
			?>
			</select>
			<?php
			
		}
	endwhile;
	wp_reset_query();
    
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
    
    <a class="add_new_te button right">Add New</a>
    
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
    
    <a class="add_new_td button right">Add New</a>
    
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
    
    <a class="add_new_step button right">Add New</a>
    
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
    add_meta_box("test_cases_metabox", "Select Test Cases ", 'show_test_cases', "test-suite", "normal", "high");
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
	echo '<input type="hidden" name="custom_roles" value="', wp_create_nonce(basename(__FILE__)), '" />';?>
	<label for="tester_role_ts_id"><b>Tester Roles:</b></label> <br />
	<textarea name="tester_role_ts" id="tester_role_ts_id" rows="3" cols="100"><?php echo $current_tester_role;?></textarea>
	<br /><span class="description">Tester Roles (comma separated)</span> 
	<br />
	<label for="harness_role_ts"><b>Harness Roles:</b></label> <br />
	<textarea name="harness_role_ts" id="harness_role_ts_id" rows="3" cols="100"><?php echo $current_harness_role;?></textarea>
	<br /><span class="description">Harness Roles (comma separated)</span> 
	<br />
	<label for="harness_role_ts"><b>Initiators:</b></label> <br />
	<textarea name="initiator_ts" id="initiator_ts_id" rows="3" cols="100"><?php echo $current_intiator;?></textarea>
	<br /><span class="description">Initiators (comma separated)</span> 
	<br />
	<?	
	$post = $post_backup;
}

function show_initiating_message(){
	global $post;
	$post_backup = $post;
	$current_initiating_messages = get_post_meta($post->ID, 'init_message', true);
	echo '<input type="hidden" name="custom_initiating_message" value="', wp_create_nonce(basename(__FILE__)), '" />';?>
	<textarea name="init_message" id="initiating_message_id" rows="4" cols="100"><?php echo $current_initiating_messages;?></textarea>
	<br /><span class="description">Type Initiating Messages (comma separated)</span>
<?
	$post = $post_backup;	
}

function show_test_cases(){
	global $post;
	$post_backup = $post;
	$current_test_cases = get_post_meta($post->ID, 'test_cases', true);
	
	echo '<input type="hidden" name="custom_test_cases" value="', wp_create_nonce(basename(__FILE__)), '" />';
	$loop = new WP_Query( array( 'post_type' => 'test-case', 'posts_per_page' => -1) );
	
	
	foreach($current_test_cases as $key => $test_cases){?>
	<div class="elem-tc"> <div class="elem-tc">
		<select name="test_cases[]">
			<option value="">Select Test Cases</option>
			<?php
			while ( $loop->have_posts() ) : $loop->the_post();
				 ?>
				 
				 <option <?php if (get_the_ID() == $test_cases) { echo 'selected="selected"'; }; ?> value="<?php the_ID(); ?>" style="margin-right: 5px; margin-bottom: 5px;"><?php the_title(); ?> <br />
				<?php
			endwhile;
			?>
		</select>
		<div class="button remove_tc left">Remove Test Case</div>
		<br clear="all" />
	</div> </div>	
	<?php} 
	
	if (empty($current_test_cases)){?>
		<select name="test_cases[]">
				<option value="">Choose Related Suite</option>
				<?php
				while ( $loop->have_posts() ) : $loop->the_post();
					 ?>
					 <option value="<?php the_ID(); ?>" style="margin-right: 5px; margin-bottom: 5px;"><?php the_title(); ?> <br />
					<?php
				endwhile;
				?>
		</select>
		<br  clear="all" />
	<?php}

	$post = $post_backup;
} 

add_action('save_post', 'save_test_case_post');

function save_test_case_post($post_id) {
	// verify nonce
	//global $postid;
	if (!isset($_POST['custom_test_cases']) || !wp_verify_nonce($_POST['custom_test_cases'], basename(__FILE__))) {
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
	
	if ( (isset($_POST['group'])) && (($_POST['test_suites']) != '') ){
		global $wpdb;
		$ts_result = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "bp_groups_testsuites WHERE group_id={$_POST['group']} AND ts_ids={$_POST['postid']}");
		$ts_id = $ts_result->ts_ids;
		print $ts_id .'<br />';
		if ($ts_id != $_POST['postid']) {
			$wpdb->query(
					"UPDATE " . $wpdb->prefix . "bp_groups_testsuites
					SET group_id = {$_POST['group']}
					WHERE ts_ids = {$_POST['postid']}"
			);
		}
		
	}
	else {
		global $wpdb;
		die($_POST['group']);
			$wpdb->insert(
					$wpdb->prefix.'bp_groups_license', 
						array( 
							'group_id' => $_POST['group'], 
							'ts_ids' => $_POST['postid']
						), 
						array( 
							'%d', 
							'%d'
						) 
					);
		
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
	
	echo '<input type="hidden" name="custom_ts" value="', wp_create_nonce(basename(__FILE__)), '" />';
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
					<div class="button remove left">Remove Test Suite</div>
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
					<div class="button remove left">Remove Test Suite</div>

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
    
    <a class="add_new button right">Add related Suite</a>

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
	global $post;
	$post_backup = $post;
	$current_doc_name = get_post_meta($post->ID, 'doc_name', true);
	$current_doc_loc= get_post_meta($post->ID, 'doc_loc', true);
	$current_doc_desc= get_post_meta($post->ID, 'doc_desc', true);
	$current_doc_type = get_post_meta($post->ID, 'doc_type', true);
	//echo $current_ts;
	
	//$meta = get_post_meta($post->ID, $field['id'], true);  
	
	echo '<input type="hidden" name="custom_doc" value="', wp_create_nonce(basename(__FILE__)), '" />';
	?>
	<div id="suites2">
		<div class="elements2">
			
		<?php
		foreach($current_doc_name as $key => $doc_name){
		  foreach ($current_doc_loc as $key2 => $doc_loc){
		    foreach ($current_doc_desc as $key3 => $doc_desc){
			  foreach ($current_doc_type as $key4 => $doc_type){
			    if (($key == $key2) && ($key == $key3) && ($key == $key4)){ ?>
				<div class="elem2"> <div class="elem2">
					<select name="doc_type[]">
						<option value="0">Choose Document Type</option>
						<option value="implementation_guide" <?php if($doc_type == 'implementation_guide') {echo "selected='selected'"; } ; ?>>Implementation Guide</option>
						<option value="taxonomy" <?php if($doc_type == 'taxonomy') {echo "selected='selected'"; } ; ?>>Taxonomy</option>
						<option value="error" <?php if($doc_type == 'error') {echo "selected='selected'"; } ; ?>>Error Messages</option>
					</select> <br />
					<label for="doc_name"><b>Document Name: </b></label> <br />
					<input type="text" name="doc_name[]" value="<?php echo $doc_name; ?>" size="30" class="mf_text"/> 

					<br clear="all" />
					<label for="doc_loc"><b>Document Location: </b></label> <br />
					<input type="text" name="doc_loc[]" value="<?php echo $doc_loc; ?>" size="30" class="mf_text"/> 
					
					<br clear="all" />
					<label for="doc_desc"><b>Document Description: </b></label> <br />
					<input type="text" name="doc_desc[]" value="<?php echo $doc_desc; ?>" size="30" class="mf_text"/> 
					
					<br />
					<div class="button remove_doc left">Remove Document</div>
					<br /> <br />
				</div> </div> 
				
				<?php }
					}
				  }
			 }
			 		 	
		 }

			 if (empty($current_doc_name)){
				 ?>
				 <div class="elem2">
					 <select name="doc_type[]">
						<option value="0">Choose Document Type</option>
						<option value="implementation_guide">Implementation Guide</option>
						<option value="taxonomy">Taxonomy</option>
						<option value="error">Error Messages</option>
					</select> <br />
					
					<label for="doc_name"><b>Document Name: </b></label> <br />
					<input type="text" name="doc_name[]" size="30" class="mf_text"/> 
					<br clear="all" />
					
					<label for="doc_loc"><b>Document Location:</b></label> <br />
					<input type="text" name="doc_loc[]" size="30" class="mf_text"/> 
					<br clear="all" />
					
					<label for="doc_loc"><b>Document Description:</b></label> <br />
					<input type="text" name="doc_desc[]" size="30" class="mf_text"/> 
					<br clear="all" />
					
					<div class="button remove_doc left">Remove Document</div>
					<br /> <br />
				</div> 
				 <?php
				 }
		 ?>
	</div>

	<div class="copy-correct-docs">
    </div>
    
    </div>
    <a class="add_new_doc button right">Add Another Document</a>

    <div class="clear"></div>
    
    <style type="text/css">
		
		.right { float:right; }
		.clear { clear: both; }		
    </style>

    <script type="text/javascript">
    jQuery(document).ready(function() {		
		jQuery('.add_new_doc').click(function(data) {
			jQuery('.copy-correct-docs').append(jQuery('.elem2').html());
		});
		
		jQuery('.remove_doc').live('click', function() {
			jQuery(this).parents('.elem2').remove();
		});
	});
    </script>	
	
	<?php
	$post = $post_backup;
}

add_action('save_post', 'save_spec_docs');

function save_spec_docs($post_id) {
	// verify nonce
	if (!isset($_POST['custom_doc']) || !wp_verify_nonce($_POST['custom_doc'], basename(__FILE__))) {
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
    
    $doc_type = $_POST['doc_type'];
    $doc_name = $_POST['doc_name'] ;
    $doc_loc = $_POST['doc_loc'];
    $doc_desc = $_POST['doc_desc'];
    
	update_post_meta($post_id, 'doc_type', $doc_type);
	update_post_meta($post_id, 'doc_name', $doc_name);
	update_post_meta($post_id, 'doc_loc', $doc_loc);
	update_post_meta($post_id, 'doc_desc', $doc_desc);
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
		
	echo '<input type="hidden" name="custom_lvl" value="', wp_create_nonce(basename(__FILE__)), '" />';
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
					
					<div class="button remove_lvl left">Remove Conformance Description</div>
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
					
					<div class="button remove_lvl left">Remove Conformance Description</div>
					<br /> <br />
				</div> </div>
				 <?php
				 }
		 ?>
	</div>

	<div class="copy-correct-lvl">

	</div>
    
    </div>
    <a class="add_new_lvl button right">Add related Suite</a>

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
    
    $lvl_code = $_POST['lvl_code'];
    $lvl_desc = $_POST['lvl_desc'] ;
    
	update_post_meta($post_id, 'lvl_code', $lvl_code);
	update_post_meta($post_id, 'lvl_desc', $lvl_desc);
}



if  (isset($_POST['form_set'])){
	add_action('template_redirect', 'create_new_user');
}

function set_html_content_type()
{
	return 'text/html';
}

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



function create_new_user(){
	global $wpdb;
	$user_id = wp_create_user( $_POST['user_login'], $_POST['user_pass'], $_POST['user_email'] );  
	//die(print_r($user_id)); 
	wp_update_user( array ('ID' => $user_id, 'first_name' => $_POST['first_name'], 'last_name' => $_POST['last_name'])) ;
	$activation_key =  md5($_POST['user_email']);
	$wpdb->query("UPDATE $wpdb->users SET user_activation_key = '$activation_key', user_status=1 WHERE ID ='$user_id' ");

	update_user_meta ($user_id, 'organisation', $_POST['organisation']);
	update_user_meta ($user_id, 'contact_phone', $_POST['contact_phone']);
	
	//$headers[] = 'From: Nego Office <office@nego-solutions.com>';
	//$headers[] = 'Cc: John Q Codex <jqc@wordpress.org>';
	//$headers[] = 'Cc: iluvwp@wordpress.org'; // note you can just use a simple email address

	$to = $_POST['user_email'];
	$subject = 'ComplianceTest Confirmation Email';
	$message = 'Username: ';
	$message .= $_POST['user_login'];
	$message .= '<br />';
	$message .= 'Password: ';
	$message .= $_POST['user_pass'];
	$message .= '<br />To activate you account click the link below <br />';
	$message .= $_SERVER['SERVER_NAME'];
	$message .= $_SERVER['REQUEST_URI'];
	$message .='?user_activation=';
	$message .= md5($_POST['user_email']);

	add_filter( 'wp_mail_content_type', 'set_html_content_type' );
	wp_mail( $to, $subject, $message, $headers );
	remove_filter( 'wp_mail_content_type', 'set_html_content_type' );  
}

/*Login With Email address */
function login_with_email_address($username) {
	$user = get_user_by_email($username);
	if(!empty($user->user_login))
		$username = $user->user_login;
	return $username;
}
add_action('wp_authenticate','login_with_email_address');

/*Inactive users*/
if  (isset($_POST['wp-submit'])){
	add_action('wp_authenticate','inactive_users_login');
}

function redirect(){
	wp_redirect(get_bloginfo('home_url'));
	}

function inactive_users_login(){
	$username = $_POST['log'];
	$user2 = get_user_by('login', $username);
	$user_status = $user2->user_status;
	$redirect = wp_logout_url( home_url() );
	
	if($user_status > 0){
		//die($redirect);
	/*	function wp_logout() {
			//wp_clear_auth_cookie();
			//do_action('wp_logout');
			do_action('redirect');
			//die(get_bloginfo('home_url'));
			
			//exit;
		}*/
		// return wp_logout;
		 wp_logout();
		 wp_redirect( 'http://nego-solutions.com/dev-clients/compliance/' ); 
		 exit;
	}

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
add_action('registered_post_type','print_vars');
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


?>

