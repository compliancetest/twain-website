<?php

/*********************************************** PROMOTION FUNCTIONS ****************************************************************/

define('THE_FUNCTION', STYLESHEETPATH . '/functions');


//MORE FIELDS - allows for extra custom fields in the edit dashboard
require_once(THE_FUNCTION . '/more-fields/more-fields.php');

//MORE TYPES - allows extra custom post types
require_once(THE_FUNCTION . '/more-types/more-types.php');

//MORE TYPES - allows extra custom post types
require_once(THE_FUNCTION . '/more-taxonomies/more-taxonomies.php');


//Process Actions related user such as login, register
require_once(THE_FUNCTION . '/user/user.php');

//Buddypress Custome Functions
require_once(THE_FUNCTION . '/buddypress/template.php');
require_once(THE_FUNCTION . '/buddypress/buddypress-forum.php');
//Test Suites Functions
require_once(THE_FUNCTION . '/test-suites.php');
//Test Case Function
require_once(THE_FUNCTION . '/test-cases.php');

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
    
    //Check Post Type
    if($post->post_type != 'product-service')
    {
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
