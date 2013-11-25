<?php
//Define Site Constants
if(!defined('MESSAGE_KEY'))
    define('MESSAGE_KEY', 'cp_messages');

if(!defined('CHILD_TEMPLATE_DIRECTORY'))
    define('CHILD_TEMPLATE_DIRECTORY', dirname( get_bloginfo('stylesheet_url')) );

if(!defined('GOOGLE_API_KEY'))
    define('GOOGLE_API_KEY', 'AIzaSyBwGPBjQXOTbPlzPGIFF7QHwX6VdH4mufE' );

if(!defined('DEFAULT_MAILCHIMP_LIST_ID'))
    define('DEFAULT_MAILCHIMP_LIST_ID', '5af09ce467');
    
//Session Start
add_action('init', 'cp_session_start');
function cp_session_start() 
{
    if(!session_id())
        session_start();
}

define('THE_FUNCTION', STYLESHEETPATH . '/functions');

require_once(THE_FUNCTION . '/esb/esb.php');

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
require_once(THE_FUNCTION . '/buddypress/buddypress-group-test-data.php');
require_once(THE_FUNCTION . '/buddypress/buddypress-docs.php');
require_once(THE_FUNCTION . '/buddypress/buddypress-members.php');


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

//Compliance Claim
require_once(THE_FUNCTION . '/compliance-claim/class.claim.php');
require_once(THE_FUNCTION . '/compliance-claim/controller.php');

//Test Plan
require_once(THE_FUNCTION . '/test-plan/class.plan.php');
require_once(THE_FUNCTION . '/test-plan/controller.php');

//Manage Login URLs
require_once(THE_FUNCTION . '/login-redirect.php');

//eWay Payment
require_once(THE_FUNCTION . '/eway/settings.php');
require_once(THE_FUNCTION . '/eway/controller.php');

require_once(THE_FUNCTION . '/rest.php');
$CPRest = new CPRest();

require_once(THE_FUNCTION . '/print/print.php');

//Support Ticket
require_once(THE_FUNCTION . '/support-ticket/index.php');

//Email Management
require_once(THE_FUNCTION . '/email-management/email-management.php');
require_once(THE_FUNCTION . '/email-management/customize.php');

//Blog
require_once(THE_FUNCTION . "/blog.php");

//Test Data(Profile Type and Profile Instances)
require_once(THE_FUNCTION . "/test-data/index.php");

//Trigger Message
require_once(THE_FUNCTION . "/message.php");

//Include Mailchimp
require_once(THE_FUNCTION . "/Mailchimp/Mailchimp.php");
 
require_once(THE_FUNCTION . "/external-actions.php");
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


function compliancetheme_setup()
{        
    add_theme_support('post-thumbnails');
    set_post_thumbnail_size( 300, 150, true );
    add_image_size( 'post-thumb', 300, 150, true );
}
add_action( 'after_setup_theme', 'compliancetheme_setup' );


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

    if(!bp_is_user_messages())
    {
        wp_enqueue_script('jquery_ui', get_stylesheet_directory_uri().'/js/jquery-ui-1.10.3.custom.js', $actions_depends);
        wp_enqueue_script('cp-combobox', get_stylesheet_directory_uri().'/js/jquery.combobox.js', $actions_depends);
    }
    wp_enqueue_script('jquery_form', get_stylesheet_directory_uri().'/js/jquery.form.js', $actions_depends);
    
    wp_enqueue_script('cp-lightbox', get_stylesheet_directory_uri().'/js/jquery.custompopup.js', $actions_depends);
    wp_enqueue_script('custom_scripts', get_stylesheet_directory_uri().'/js/custom.js', $actions_depends);        
    wp_enqueue_script('print', get_stylesheet_directory_uri().'/js/print.js', $actions_depends);        
    wp_enqueue_script('cp-buddypress', get_stylesheet_directory_uri().'/functions/buddypress/buddypress.js', $actions_depends, '1.0', true);
    
    if(is_page('my-transaction-log'))
        wp_enqueue_script('message-trigger', get_stylesheet_directory_uri().'/js/message.js', $actions_depends, '1.0', true);
        
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
    
    
    //Test Data
    wp_enqueue_script( 'iframe-trasport', get_stylesheet_directory_uri() . '/functions/test-data/jquery.iframe-transport.js', $actions_depends, '1.0', true );
    wp_enqueue_script( 'jquery-fileupload', get_stylesheet_directory_uri() . '/functions/test-data/jquery.fileupload.js', $actions_depends, '1.0', true );
    wp_enqueue_script( 'jsonary-super-bundle', get_stylesheet_directory_uri() . '/functions/test-data/jsonary-super-bundle.js', $actions_depends, '1.0', true );
    wp_enqueue_script( 'json-schema-validator', get_stylesheet_directory_uri() . '/functions/test-data/tv4.js', $actions_depends, '1.0', true );
    wp_enqueue_script( 'zclip', get_stylesheet_directory_uri() . '/js/jquery.zclip.js', $actions_depends, '1.0', true );
    
    
    
    if(is_user_logged_in())   
    {
        //Ticket Script
        wp_enqueue_script( 'support-ticket', get_stylesheet_directory_uri() . '/functions/support-ticket/support-ticket.js', $actions_depends, '1.0', true );
        wp_enqueue_script( 'testdata', get_stylesheet_directory_uri() . '/functions/test-data/testdata.js', $actions_depends, '1.0', true );
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


/*--------------------------------------------------
My Payment Method updates
--------------------------------------------------*/
//check card number

function check_cc($ccnumber, $allowTest = false){
    
    $cardtype = false;
    
    $ccnumber = preg_replace('/[^0-9]/','',$ccnumber); // Strip non-numeric characters

    $creditcard = array(
        'visa'            =>    "/^4\d{3}-?\d{4}-?\d{4}-?\d{4}$/",
        'mastercard'    =>    "/^5[1-5]\d{2}-?\d{4}-?\d{4}-?\d{4}$/",
        'discover'        =>    "/^6011-?\d{4}-?\d{4}-?\d{4}$/",
        'amex'            =>    "/^3[4,7]\d{13}$/",
        'diners'        =>    "/^3[0,6,8]\d{12}$/",
        'bankcard'        =>    "/^5610-?\d{4}-?\d{4}-?\d{4}$/",
        'jcb'            =>    "/^[3088|3096|3112|3158|3337|3528]\d{12}$/",
        'enroute'        =>    "/^[2014|2149]\d{11}$/",
        'switch'        =>    "/^[4903|4911|4936|5641|6333|6759|6334|6767]\d{12}$/"
    );

    if(empty($cardtype)){
        $match=false;
        foreach($creditcard as $cardtype=>$pattern){
            if(preg_match($pattern,$ccnumber)==1){
                $match=true;
                break;
            }
        }
        
        if(!$match){
            return false;
        }
    }else if(@preg_match($creditcard[strtolower(trim($cardtype))],$ccnumber)==0){
        return false;
    }    
    
    if($allowTest && !check_cc_validation($ccnumber))    
        return false;
    
    return $cardtype;
}

function check_cc_validation($ccnum){
    $checksum = 0;
    for ($i=(2-(strlen($ccnum) % 2)); $i<=strlen($ccnum); $i+=2){
        $checksum += (int)($ccnum{$i-1});
    }

    // Analyze odd digits in even length strings or even digits in odd length strings.
    for ($i=(strlen($ccnum)% 2) + 1; $i<strlen($ccnum); $i+=2){
        $digit = (int)($ccnum{$i-1}) * 2;
        if ($digit < 10){
            $checksum += $digit;
        }else{
            $checksum += ($digit-9);
        }
    }

    if(($checksum % 10) == 0){
        return true; 
    }else{
        return false;
    }
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

//Renaming Buddypress Documents to "WIKI"

function edit_admin_menus() {  
    global $menu;  
    global $submenu;  
    foreach($menu as $i=>$m)   
    {
        if($m[0] == 'BuddyPress Docs')
            $menu[$i][0] = 'Wiki';
        if($m[0] == 'Groups')
            $menu[$i][0] = 'Communities';
        
    }
    
    $submenu['edit.php?post_type=bp_doc'][5][0] = 'All Wikis';     
    $submenu['edit.php?post_type=bp_doc'][10][0] = 'Add New Wiki';  

}  
add_action( 'admin_menu', 'edit_admin_menus' );  

// Login page fix
function my_check_password_reset_key($key, $login) {
    global $wpdb;

    $key = preg_replace('/[^a-z0-9]/i', '', $key);

    if ( empty( $key ) || !is_string( $key ) )
	return null;
    if ( empty($login) || !is_string($login) )
	return null;

    $user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->users WHERE user_activation_key = %s AND user_login = %s", $key, $login));

    if ( empty( $user ) )
	return null;

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


function formatDate($date, $format = 'Y-m-d', $user_id = null)
{    
    if(is_numeric($date))
        $date = new DateTime(date("Y-m-d H:i:s", $date));
    else
        $date = new DateTime($date);
    
    if(!$user_id)
        $user_id = get_current_user_id();
    if($user_id && ($timezone = get_user_meta($user_id, 'timezone', true)))
    {        
    
        $dateTimeZone = new DateTimeZone($timezone);                        
        $date->setTimezone($dateTimeZone);        
    }
    
    return $date->format($format);    
}


function encrypt_card_number($num)
{
    $enum = substr($num, 0, 6) . 'XXXXXX' . substr($num, 12);
    
    return $enum;
}

function convert_css_name($string)
{
    $string = strtolower($string);
    $string = str_replace(" ", "-", $string);
    
    return $string;
}

function _convertHTMLToBBCode($html)
{
    $pattern = array(
//        '/[\r|\n]/',
        '/<br.*?>/i',
        '/<b.*?>/i',
        '/<\/b>/i',
        '/<strong.*?>/i',
        '/<\/strong>/i',
        '/<div(.*?)>/i',
        '/<\/div>/i',
        '/<pre(.*?)>/i',
        '/<\/pre>/i',
        '/<font(.*?)>/i',
        '/<\/font>/i',
        '/<span(.*?)>/i',
        '/<\/span>/i',
        '/<p(.*?)>/i',
        '/<\/p>/i',
//        '/<ul>/i',
//        '/<\/ul>/i',
//        '/<ol>/i',
//        '/<\/ol>/i',
//        '/<li>/i',
//        '/<\/li>/i',            
        '/<em.*?>/i',
        '/<\/em>/i',
        '/<u.*?>/i',
        '/<\/u>/i',
        '/<ins.*?>/i',
        '/<\/ins>/i',
        '/<strike>/i',
        '/<\/strike>/i',
        '/<del>/i',
        '/<\/del>/i',
        '/<a.*?href="(.*?)".*?>(.*?)<\/a>/i', '/<a.*?href=\\\"(.*?)\\\".*?>(.*?)<\/a>/i', 
        '/<a.*?href=\'(.*?)\'.*?>(.*?)<\/a>/i', "/<a.*?href=\\\\'(.*?)\\\\'.*?>(.*?)<\\/a>/i",
        '/<img(.*?)src="(.*?)"(.*?)>/i',     
        '/<i.*?>/i',
        '/<\/i>/i',
        '/<.*?>(.*?)<\/.*?>/'
    );
    
    $replace = array(
//      "",
      "\r\n",
      '[b]',
      '[/b]',
      '[b]',
      '[/b]',
      '[div$1]',
      '[/div]',
      '[code$1]',
      '[/code]',
      '[font$1]',
      '[/font]',
      '[span$1]',
      '[/span]',
      '[p$1]',
      '[/p]',
//      '[list]',
//      '[/list]',
//      '[list=1]',
//      '[/list]',
//      '[*]',
//      '[/*]',
      '[i]',
      '[/i]',          
      '[u]',
      '[/u]',
      '[u]',
      '[/u]',
      '[s]',
      '[/s]',
      '[s]',
      '[/s]',
      '[url=$1]$2[/url]', '[url=$1]$2[/url]', '[url=$1]$2[/url]', '[url=$1]$2[/url]', 
      '[img $1$3]$2[/img]',
      '[i]',
      '[/i]',
      '$1'
    );
    
    $html = preg_replace($pattern, $replace, $html);
    
    //Convert Single Quote to Double Quote for div, code, font, img tags
    $html = preg_replace_callback('/\[code(.*?)\](.*?)\[\/code\]/i', create_function('$matches', 'return "[code" . str_replace(\'"\', ";squote;", $matches[1]) . "]" . $matches[2] . "[/code]";'), $html);
    $html = preg_replace_callback('/\[font(.*?)\](.*?)\[\/font\]/i', create_function('$matches', 'return "[font" . str_replace(\'"\', ";squote;", $matches[1]) . "]" . $matches[2] . "[/font]";'), $html);
    $html = preg_replace_callback('/\[span(.*?)\](.*?)\[\/span\]/i', create_function('$matches', 'return "[span" . str_replace(\'"\', ";squote;", $matches[1]) . "]" . $matches[2] . "[/span]";'), $html);
    $html = preg_replace_callback('/\[div(.*?)\](.*?)\[\/div\]/i', create_function('$matches', 'return "[div" . str_replace(\'"\', ";squote;", $matches[1]) . "]" . $matches[2] . "[/div]";'), $html);
    $html = preg_replace_callback('/\[p(.*?)\](.*?)\[\/p\]/i', create_function('$matches', 'return "[p" . str_replace(\'"\', ";squote;", $matches[1]) . "]" . $matches[2] . "[/p]";'), $html);
    $html = preg_replace_callback('/\[img(.*?)\](.*?)\[\/img\]/i', create_function('$matches', 'return "[img" . str_replace(\'"\', ";squote;", $matches[1]) . "]" . $matches[2] . "[/img]";'), $html);
    
    return $html;
}

/**
* Convert BBCode To HTML
* 
* @param mixed $code
*/
function _convertBBCodeToHTML($code)
{
    $pattern = array(
 //       '/\\\r/',
//        '/\\\n/',
        '/\[b\]/i',
        '/\[\/b\]/i',
        '/\[code(.*?)\]/i',
        '/\[\/code\]/i',
        '/\[font(.*?)\]/i',
        '/\[\/font\]/i',
        '/\[div(.*?)\]/i',            
        '/\[\/div\]/i',            
        '/\[span(.*?)\]/i',
        '/\[\/span\]/i',
        '/\[p(.*?)\]/i',
        '/\[\/p\]/i',
        '/\[i\]/i',
        '/\[\/i\]/i',
        '/\[u\]/i',
        '/\[\/u\]/i',
        '/\[s\]/i',
        '/\[\/s\]/i',
        '/\[url=(.*?)\](.*?)\[\/url\]/i',
        '/\[img(.*?)\](.*?)\[\/img\]/i',
/*        '/\[list\](.*?)\[\/list\]/i',
        '/\[list=1\](.*?)\[\/list\]/i',
        '/\[list\]/i',
        '/\[list=1\]/i',
        '/\[\*\](.*?)\[\/\*\]/',
        '/\[\*\]/'*/
    );
    $replace = array(
//      "",
//      '<br />',
      '<b>',
      '</b>',
      '<pre$1>',
      '</pre>',
      '<font$1>',
      '</font>',
      '<div$1>',          
      '</div>',          
      '<span$1>',
      '</span>',
      '<p$1>',
      '</p>',
      '<i>',
      '</i>',
      '<u>',
      '</u>',
      '<strike>',
      '</strike>',
      '<a href=\'$1\'>$2</a>',
      '<img $1 src=\'$2\'>',
//      '<ul>$1</ul>',
//      '<ol>$1</ol>',
//      '<ul>',
//      '<ol>',
//      '<li>$1</li>',
//      '<li>'
    );
       
    $code = preg_replace($pattern, $replace, $code);
    
    return $code;
}

function _convertLineSymbolToBR($string)
{
    return str_replace("\r\n", "<br />", $string);
}

/**
* Customize Update Post Meta to allow some html tags such as a, b, i
* 
* @param mixed $post_id
* @param mixed $meta_key
* @param mixed $meta_value
* @param mixed $prev_value
*/
function cp_update_post_meta($post_id, $meta_key, $meta_value, $prev_value = '')
{
    //Allow Some HTML Tags
    if(is_array($meta_value)){
        $new_value = array();
        foreach($meta_value as $k=>$v)
        {
            $new_value[$k] = _convertHTMLToBBCode($v);
        }
        $meta_value = $new_value;
    }else{
        $meta_value = _convertHTMLToBBCode($meta_value);
    }
        
    return update_post_meta($post_id, $meta_key, $meta_value, $prev_value = '');
}

function cp_get_post_meta($post_id, $key = '', $single = false)
{
    $meta_value = get_post_meta($post_id, $key, $single);
    if(is_array($meta_value)){
        $new_value = array();
        foreach($meta_value as $k=>$v)
        {
            $new_value[$k] = _convertBBCodeToHTML($v);
        }
        $meta_value = $new_value;
    }else{
        $meta_value = _convertBBCodeToHTML($meta_value);
    }
    
    return $meta_value;
}

function cp_implode($arr, $gule = ';;')
{
    if(!$arr)
        return "";
        
    $str = implode($gule, $arr);
    if($str != "")
        $str = $gule . $str . $gule;
    
    return $str;
}

function cp_explode($str, $gule = ';;')
{
    if(!$str)
        return array();
        
    $arr = explode($gule, $str);
    
    $result = array();
    foreach($arr as $r)
    {
        if(!$r)
            continue;
        $result[] = $r;
    }
    
    return $result;
}


function cp_wrap($string, $length)
{
    return wordwrap($string, $length, "\n", true);
}

function cp_generate_password($length = 12) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    return substr(str_shuffle($chars),0,$length);
}

function getItemsPerPage($page = '', $default = 20)
{
    if(isset($_SESSION[$page . '_limit']))
    {
        return $_SESSION[$page . '_limit'];
    }else{
        return $default;
    }
}

function setItemsPerPage($value, $page = '')
{
    $_SESSION[$page . '_limit'] = $value;
}

function cp_get_group_permalink_by_id($group_id)
{
    $group = groups_get_group(array('group_id'=> $group_id));
    
    return bp_get_group_permalink($group);
}

function cp_checked($value1, $value2)
{
    return $value1 == $value2 || in_array($value1, $value2) ? "checked='checked'" : "";
}
function cp_selected($value1, $value2)
{
    return $value1 == $value2 ? "selected='selected'" : "";
}

//Add rewrite rule
add_action("generate_rewrite_rules", "add_ticket_rewrite");
function add_ticket_rewrite()
{
    add_rewrite_rule('^my-support-tickets/([0-9]*)$','index.php?pagename=my-support-tickets&ticket=$matches[1]', 'top');
}

add_filter('query_vars', 'add_ticket_query_var');
function add_ticket_query_var($public_query_vars)
{
    $public_query_vars[] = 'ticket';
    
    return $public_query_vars;
}


//Get User Name for Whole site
function cp_get_user_display_name($user)
{
    if(is_integer($user))
    {
        $user = get_user_by("id", $user);
    }else if(is_string($user)){
        $user = get_user_by("email", $user);
    }
    
    //Now only show user first name
    return $user->display_name;
}

/**
* Get Mailchimp API KEY from Mailchimp For WP Plugin
* 
*/
function get_mailchimp_api_key()
{
    $options = get_option('mc4wp');
    return $options['api_key'];
}

function cp_get_user_avatar($user_id, $args = '')
{
    $defaults = array(
        'type'   => 'thumb',
        'width'  => false,
        'height' => false,
        'html'   => true,
        'alt'    => sprintf( __( 'Profile picture of %s', 'buddypress' ), bp_get_loggedin_user_fullname() )
    );

    $r = wp_parse_args( $args, $defaults );
    extract( $r, EXTR_SKIP );

    return apply_filters( 'bp_get_loggedin_user_avatar', bp_core_fetch_avatar( array( 'item_id' => $user_id, 'type' => $type, 'width' => $width, 'height' => $height, 'html' => $html, 'alt' => $alt ) ) );
}