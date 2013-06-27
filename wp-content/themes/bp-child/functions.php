<?php
//Define Site Constants
if(!defined('MESSAGE_KEY'))
    define('MESSAGE_KEY', 'cp_messages');

if(!defined('CHILD_TEMPLATE_DIRECTORY'))
    define('CHILD_TEMPLATE_DIRECTORY', dirname( get_bloginfo('stylesheet_url')) );

if(!defined('GOOGLE_API_KEY'))
    define('GOOGLE_API_KEY', 'AIzaSyBwGPBjQXOTbPlzPGIFF7QHwX6VdH4mufE' );

    
//Session Start
add_action('init', 'cp_session_start');
function cp_session_start() 
{
    if(!session_id())
        session_start();
}

define('THE_FUNCTION', STYLESHEETPATH . '/functions');

//Include change role names
require_once(THE_FUNCTION . '/role-customize.php');
require_once(THE_FUNCTION . '/authorization.php');

//MORE FIELDS - allows for extra custom fields in the edit dashboard
require_once(THE_FUNCTION . '/more-fields/more-fields.php');

//MORE TYPES - allows extra custom post types
require_once(THE_FUNCTION . '/more-types/more-types.php');

//MORE TYPES - allows extra custom post types
require_once(THE_FUNCTION . '/more-taxonomies/more-taxonomies.php');


//Process Actions related user such as login, register
require_once(THE_FUNCTION . '/user/user.php');

//Test Suites Functions
require_once(THE_FUNCTION . '/test-suite/testsuite.class.php');
require_once(THE_FUNCTION . '/test-suite/add-meta-boxes.php');
require_once(THE_FUNCTION . '/test-suite/controller.php');

//Buddypress Custome Functions
require_once(THE_FUNCTION . '/buddypress/customize.php');
require_once(THE_FUNCTION . '/buddypress/buddypress-forum.php');
require_once(THE_FUNCTION . '/buddypress/buddypress-group-downloads.php');
require_once(THE_FUNCTION . '/buddypress/buddypress-docs.php');

//Test Case Function
require_once(THE_FUNCTION . '/test-case/testcase.class.php');
require_once(THE_FUNCTION . '/test-case/controller.php');
require_once(THE_FUNCTION . '/test-case/add-meta-boxes.php');

//Product & Service
require_once(THE_FUNCTION . '/test-case/testcase.class.php');
require_once(THE_FUNCTION . '/test-case/add-meta-boxes.php');
require_once(THE_FUNCTION . '/test-case/controller.php');

//Products And Services
require_once(THE_FUNCTION . '/product-and-service/add-meta-boxes.php');
require_once(THE_FUNCTION . '/product-and-service/class.productandservice.php');
require_once(THE_FUNCTION . '/product-and-service/controller.php');

 
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
    $actions_depends = array('jquery');
	if (stripos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) {
		wp_enqueue_script('pie', get_stylesheet_directory_uri().'/js/PIE.js', $actions_depends);
		$actions_depends[] = 'pie';
	}

    wp_enqueue_script('jquery_ui', get_stylesheet_directory_uri().'/js/jquery-ui-1.10.3.custom.js', $actions_depends);
    wp_enqueue_script('jquery_form', get_stylesheet_directory_uri().'/js/jquery.form.js', $actions_depends);
    wp_enqueue_script('cp-lightbox', get_stylesheet_directory_uri().'/js/jquery.custompopup.js', $actions_depends);
    wp_enqueue_script('custom_scripts', get_stylesheet_directory_uri().'/js/custom.js', $actions_depends);        
    wp_enqueue_script('cp-buddypress', get_stylesheet_directory_uri().'/functions/buddypress/buddypress.js', $actions_depends, '1.0', true);
    if(bp_is_groups_component()){        
        wp_enqueue_script('groups-download', get_stylesheet_directory_uri().'/groups/js/groups-downloads.js', $actions_depends, '1.0', true);
    }
    if(bp_is_item_admin()){
        wp_enqueue_script('groups-admin', get_stylesheet_directory_uri().'/groups/js/groups-admin.js', $actions_depends, '1.0', true);
    }
    
    //Add Buddypress Docs StyleSheet
    if(!bp_docs_is_docs_component() && bp_is_group())
    {
        wp_enqueue_style( 'bp-docs-css', plugins_url() . '/' . BP_DOCS_PLUGIN_SLUG . '/includes/' . 'css/screen.css' );
    }
    
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

//Add htaccess file to prevent direct access
function addHTAccessProtection($dir)
{
    $fp = fopen($dir . '/.htaccess', 'w');
    fwrite($fp, 'Order Deny,Allow' . PHP_EOL . 'Deny from all');
    fclose($fp);
}

//Format Bytes
function formatBytes($bytes, $precision = 2) { 
    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 

    $bytes = max($bytes, 0); 
    $pow = ($bytes ? log($bytes) : 0) / log(1024); 
    
    $idx = min(floor($pow), count($units) - 1); 
    
    return round(pow(1024, $pow - floor($pow)), $precision) . ' ' . $units[$idx]; 
}

/**
* Add message to session
* 
* @param String $message
* @param String $type: success, error, warning, notice
*/
function addMessage($message, $type = 'success')
{
    if(!isset($_SESSION[MESSAGE_KEY]))
        $_SESSION[MESSAGE_KEY] = array();
    
    $_SESSION[MESSAGE_KEY][] = array('message' => $message, 'type' => $type);
    
}

function flushMessages($class = '')
{
    if(isset($_SESSION[MESSAGE_KEY]))
    {
        echo '<div id="messages-wrapper"  class="' . $class . '">';
        foreach($_SESSION[MESSAGE_KEY] as $row)
        {
            echo '<div class="message ' . $row['type'] . '">' . $row['message'] . "</div>";
        }
        echo '</div>';
        unset($_SESSION[MESSAGE_KEY]);
    }
}

//Show Result Messages
add_action('bp_before_container', 'flushMessages');


//Get get params for search filter
function getFilterParam($name)
{
    $param = array();
    if(isset($_GET[$name]))
        $param = $_GET[$name];
    if(!is_array($param))
        $param = array($param);
        
    return $param;
}
