<?php

if (stripos(get_option('siteurl'), 'https://') === 0) {
    $_SERVER['HTTPS'] = 'on';
}

define('DEFAULT_AVATAR', '/wp-content/themes/bp-child/images/default-group-avatar.png');
//Session Start
if(!session_id())
    session_start();

//Define Site Constants
if(!defined('MESSAGE_KEY'))
    define('MESSAGE_KEY', 'cp_messages');

if(!defined('CHILD_TEMPLATE_DIRECTORY'))
    define('CHILD_TEMPLATE_DIRECTORY', dirname( get_bloginfo('stylesheet_url')) );

if(!defined('GOOGLE_API_KEY'))
    define('GOOGLE_API_KEY', 'AIzaSyBwGPBjQXOTbPlzPGIFF7QHwX6VdH4mufE' );

if(!defined('RECAPTCHA_PUBLIC_KEY'))
    define('RECAPTCHA_PUBLIC_KEY', get_option('recaptcha_public_key'));

if(!defined('RECAPTCHA_PRIVATE_KEY'))
    define('RECAPTCHA_PRIVATE_KEY', get_option('recaptcha_private_key'));

if(!defined('SEARCH_RESULTS_LIMIT'))
    define('SEARCH_RESULTS_LIMIT', 25);

if(!defined('DEFAULT_MAILCHIMP_LIST_ID'))
    define('DEFAULT_MAILCHIMP_LIST_ID', get_option('mailchimp_all_list_id'));
    
define('MESSAGE_WARNING_ANONYMOUS', 'You must be a registered member of the site to view this content. Registration is free - just go to the '.get_site_title().' home page and click on the Signup button.');
define('MESSAGE_WARNING_REGISTERED', 'You need to join the community to access this content. Community membership is free but applications must be approved by the community owner - just visit the community home page and click the "Join Community" button.');
define('MESSAGE_WARNING_COMMUNITY_MEMBER', 'You must subscribe to at least one test suite in the community to access this content. To subscribe once you are a community member, just select the desired suite from the community home page, and click on the "Access" bar.');
define('MESSAGE_WARNING_COMMUNITY_ADMIN', 'You must subscribe to at least one test suite in the community to access this content. To subscribe once you are a community member, just select the desired suite from the community home page, and click on the "Access" bar.');
define('MESSAGE_WARNING_COMMUNITY_SUBSCRIBER', 'You must subscribe to at least one test suite in the community to access this content. To subscribe once you are a community member, just select the desired suite from the community home page, and click on the "Access" bar.');

//Session Start
add_action('init', 'cp_session_start');
function cp_session_start() 
{
    if(!session_id())
        session_start();
}

/**
 * Return site name from Website configs section
 * @return string
 */
function get_site_title()
{
    return get_option('tw_site_title');
}

/**
 * Return site organisation from Website configs section
 * @return string
 */
function get_site_organisation()
{
    return get_option('tw_site_organisation');
}
/**
 * @param $path - path to view file, e.g. 'test-data/views/schedule-popup.phtml'
 * @param $view - object with variables needed in view
 * @param bool $is_ajax
 */
function render_view( $path, $view, $is_ajax = false )
{
    $path = THE_FUNCTION . '/' .$path;
    if( file_exists( $path ) ){
        require_once $path;
    }
    if( $is_ajax ){
        exit();
    }
}
define('THE_FUNCTION', STYLESHEETPATH . '/functions');

//Include Recaptcha library
require_once(THE_FUNCTION . '/recaptchalib.php');

require_once(THE_FUNCTION . '/cut_html_string.php');

require_once(THE_FUNCTION . '/esb/esb.php');

//Site Settings Page
require_once(THE_FUNCTION . '/settings.php');

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

require_once(THE_FUNCTION . '/reports/controller.php');

//Test Suites Functions
require_once(THE_FUNCTION . '/test-suite/testsuite.class.php');
require_once(THE_FUNCTION . '/test-suite/add-meta-boxes.php');
require_once(THE_FUNCTION . '/test-suite/controller.php');

//Buddypress Custome Functions
require_once(THE_FUNCTION . '/buddypress/customize.php');
require_once(THE_FUNCTION . '/buddypress/buddypress-forum.php');
require_once(THE_FUNCTION . '/buddypress/buddypress-group-downloads.php');
require_once(THE_FUNCTION . '/buddypress/buddypress-group-reports.php');
require_once(THE_FUNCTION . '/buddypress/buddypress-group-test-data.php');
require_once(THE_FUNCTION . '/buddypress/buddypress-docs.php');
require_once(THE_FUNCTION . '/buddypress/buddypress-members.php');

global $bp;
$bp->groups->setup_globals();

//ProfileInstance class allows to edit profile in single place
require_once(THE_FUNCTION . '/classes/ProfileInstance.php');

require_once(THE_FUNCTION . '/classes/ProfileType.php');

require_once(THE_FUNCTION . '/classes/User.php');
require_once(THE_FUNCTION . '/classes/MicroServices.php');
require_once(THE_FUNCTION . '/classes/LoginAttempts.php');

require_once(THE_FUNCTION . '/classes/ClaimsConversations.php');

//Test Case Function
require_once(THE_FUNCTION . '/test-case/testcase.class.php');
require_once(THE_FUNCTION . '/test-case/controller.php');
require_once(THE_FUNCTION . '/test-case/add-meta-boxes.php');

//Product & Service
require_once(THE_FUNCTION . '/test-case/testcase.class.php');
require_once(THE_FUNCTION . '/test-case/add-meta-boxes.php');
require_once(THE_FUNCTION . '/test-case/controller.php');

//Products
require_once(THE_FUNCTION . '/product-and-service/add-meta-boxes.php');
require_once(THE_FUNCTION . '/product-and-service/class.productandservice.php');
require_once(THE_FUNCTION . '/product-and-service/controller.php');

//Services
require_once(THE_FUNCTION . '/service/class.service.php');
require_once(THE_FUNCTION . '/service/controller.php');

//Tags
require_once(THE_FUNCTION . '/tags/Tag.php');
require_once(THE_FUNCTION . '/tags/controller.php');

//E2E Agreements
require_once(THE_FUNCTION . '/e2e-agreements/class.agreement.php');
require_once(THE_FUNCTION . '/e2e-agreements/class.agreement.log.php');
require_once(THE_FUNCTION . '/e2e-agreements/controller.php');

//Processes
require_once(THE_FUNCTION . '/processes/class.process.php');

require_once(THE_FUNCTION . '/aws/BaseAWS.php');

/**
 * wordpress and laravel uses same API version, so we shouldn load wordpress 2.* sdk to laravel
 **/
if(!isset($GLOBALS['loadFromLaravel'])) {
    //CloudSearch
    require_once(THE_FUNCTION . '/cloud-search/CloudSearch.php');
    require_once(THE_FUNCTION . '/cloud-search/FulltextSearch.php');
    require_once(THE_FUNCTION . '/cloud-search/cloudsearch-menu.php');

    //SQS
    require_once(THE_FUNCTION . '/aws/s3/Client.php');

    require_once(THE_FUNCTION . '/aws/sqs/Client.php');

    require_once(THE_FUNCTION . '/aws/ec2/Client.php');
}


//Compliance Claim
require_once(THE_FUNCTION . '/compliance-claim/class.claim.php');
require_once(THE_FUNCTION . '/compliance-claim/controller.php');

//Test Plan
require_once(THE_FUNCTION . '/test-plan/class.plan.php');
require_once(THE_FUNCTION . '/test-plan/controller.php');

//Test Patterns
require_once(THE_FUNCTION . '/test-patterns/test-patterns.php');

//Customers
require_once(THE_FUNCTION . '/customer/customer-post.php');

//Manage Login URLs
require_once(THE_FUNCTION . '/login-redirect.php');

//Manage Subscription
require_once(THE_FUNCTION . '/subscription/class.subscription.php');
require_once(THE_FUNCTION . '/subscription/controller.php');
require_once(THE_FUNCTION . '/subscription/function.php');

require_once(THE_FUNCTION . '/subscription/admin/index.php');

//Charges admin section
require_once(THE_FUNCTION . '/charges/admin.php');

//Batch jobs admin section
require_once(THE_FUNCTION . '/batch-jobs/admin.php');

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

require_once(THE_FUNCTION . "/tools.php");

require_once(THE_FUNCTION . "/tmp_functions.php");

require_once(STYLESHEETPATH . '/bbpress/customize.php');

require_once(THE_FUNCTION . '/gateways/gateways.php');

//Organisations
require_once(THE_FUNCTION . '/organisation/admin.php');
require_once(THE_FUNCTION . '/organisation/index.php');

//Xero Items
require_once(THE_FUNCTION . '/xero-items/admin.php');

//Pricing Plans
require_once(THE_FUNCTION . '/pricing-plans/admin.php');
require_once(THE_FUNCTION . '/pricing-plans/pricingplans.class.php');
//Xero Payments
require_once(THE_FUNCTION . '/organisations-payments/admin.php');

//Process Compliancetest Admin Actions
require_once(THE_FUNCTION . '/admin/index.php');

//Process Compliancetest Admin Actions
require_once(THE_FUNCTION . '/home-settings.php');

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

add_action('wp_enqueue_scripts', 'add_header_scripts');

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
    wp_enqueue_script('cp-bbpress', get_stylesheet_directory_uri().'/bbpress/bbpress.js', $actions_depends, '1.0', true);

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

    wp_enqueue_script( 'redactor-min', get_stylesheet_directory_uri() . '/js/redactor.js', $actions_depends, '1.0', true );
    wp_enqueue_style('redactor', get_stylesheet_directory_uri() . '/css/redactor.css');

    /*if(is_page('edit-test-suite') || is_page('add-new-test-suite') || is_page('edit-test-case') || is_page('add-new-test-case') || bp_is_group_admin_page())
    {
        //Include Redactor WYSIWYG Editor

    }*/

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

//Renaming Buddypress Documents to "Articles"

function edit_admin_menus() {
    global $menu;
    global $submenu;
    foreach($menu as $i=>$m)
    {
        if($m[0] == 'BuddyPress Docs')
            $menu[$i][0] = 'Articles';
        if($m[0] == 'Groups')
            $menu[$i][0] = 'Communities';

    }

    $submenu['edit.php?post_type=bp_doc'][5][0] = 'All Articles';
    $submenu['edit.php?post_type=bp_doc'][10][0] = 'Add New Article';

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

function getUTCTimeStamp($date, $user_id = null)
{
    if(!$user_id)
        $user_id = get_current_user_id();

    if($user_id && ($timezone = get_user_meta($user_id, 'timezone', true)))
        $dateTimeZone = new DateTimeZone($timezone);
    else
        $dateTimeZone = new DateTimeZone('UTC');

    if(is_numeric($date))
        $date = new DateTime(date("Y-m-d H:i:s", $date), $dateTimeZone);
    else
        $date = new DateTime($date, $dateTimeZone);

    $utc = new DateTimeZone('UTC');
    $date->setTimezone($utc);

    return $date->getTimestamp();
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

function cp_get_post_meta($post_id, $key = '', $single = false, $use_bbcode = true)
{
    $meta_value = get_post_meta($post_id, $key, $single);
    if(is_array($meta_value)){
        $new_value = array();
        foreach($meta_value as $k=>$v)
        {
            if($use_bbcode)
                $new_value[$k] = _convertBBCodeToHTML($v);
            else
                $new_value[$k] = $v;
        }
        $meta_value = $new_value;
    }else{
        if($use_bbcode)
            $meta_value = _convertBBCodeToHTML($meta_value);
        else
            $meta_value = $meta_value;
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
    if(is_array($value2))
        return in_array($value1, $value2) ? "checked='checked'" : "";
    else
        return $value1 == $value2 ? "checked='checked'" : "";
}
function cp_selected($value1, $value2)
{
    if(is_array($value2))
        return in_array($value1, $value2) ? "selected='selected'" : "";
    else
        return $value1 == $value2 ? "selected='selected'" : "";
}

//Add rewrite rule
add_action("init", "add_cp_custom_rewrites");
function add_cp_custom_rewrites()
{
    //Add Ticket Rewrite Rules
    add_rewrite_rule('^my-support-tickets/([0-9]*)$','index.php?pagename=my-support-tickets&ticket=$matches[1]', 'top');
    //Add Claim Rewrite Rules
    add_rewrite_rule('^claims/(.*)$','index.php?pagename=claim-certificate&claim=$matches[1]', 'top');
    //Add Agreement Rewrite Rules
    add_rewrite_rule('^agreement/(.*)$','index.php?pagename=agreement-certificate&claim=$matches[1]', 'top');
}

add_filter('query_vars', 'add_custom_query_var');
function add_custom_query_var($public_query_vars)
{
    $public_query_vars[] = 'ticket';
    $public_query_vars[] = 'claim';

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


/*
 * Search only a specific forum
 */
function cp_bbp_filter_search_results( $r ){

    //Get the submitted forum ID (from the hidden field added in step 2)
    $forum_id = sanitize_title_for_query( $_GET['bbp_search_forum_id'] );

    //If the forum ID exits, filter the query
    if( $forum_id && is_numeric( $forum_id ) ){

        $r['meta_query'] = array(
            array(
                'key' => '_bbp_forum_id',
                'value' => $forum_id,
                'compare' => '=',
            )
        );

    }

    return $r;
}
add_filter( 'bbp_after_has_search_results_parse_args' , 'cp_bbp_filter_search_results' );

/**
 * Remove Contact Form 7 scripts + styles unless we're on the contact page
 */
add_action( 'wp_enqueue_scripts', 'ac_remove_cf7_scripts' );

function ac_remove_cf7_scripts() {
    if ( !is_page('contact-us') ) {
        wp_deregister_style( 'contact-form-7' );
        wp_deregister_script( 'contact-form-7' );
    }
}


function get_valid_full_url($url)
{
    if(strpos($url, 'http://') === false && strpos($url, 'https://') === false)
    {
        $url = "http://" . $url;
    }

    return $url;
}


function get_products_args(){
    $post_type = 'product-service';

    if ( is_user_logged_in() && ! ( is_super_admin() || groups_is_user_admin_in_any_community( get_current_user_id() ) ) ) {

        $all_public_posts = get_posts(array(
            'post_type' => $post_type,
            'posts_per_page' => -1,
            'meta_key' => 'product_visibility',
            'meta_value' => 'Public'
        ));

        $current_user_private_posts = get_posts(array(
            'post_type' => $post_type,
            'posts_per_page' => -1,
            'meta_key' => 'product_visibility',
            'meta_value' => 'Private',
            'author' => get_current_user_id()
        ));

        $merged_posts = array_merge( $current_user_private_posts, $all_public_posts); //combine queries

        $post_ids = array();
        foreach( $merged_posts as $item ) {
            $post_ids[]=$item->ID; //create a new query only of the post ids
        }
        $unique_posts = array_unique($post_ids); //remove duplicate post ids

        $args = array(
            'post__in' => $unique_posts,
            'post_type' => $post_type,
            'posts_per_page' => -1,
        );
    } else {
        $args = array(
            'post_type' => $post_type,
            'posts_per_page' => -1,
            'tax_query' => array('relation' => 'and'),
        );
        if ( ! ( is_super_admin() || groups_is_user_admin_in_any_community( get_current_user_id() ) )) {
            $args['meta_key'] = 'product_visibility';
            $args['meta_value'] = 'Public';
        }
    }

    return $args;
}

function ct_cut_html_string($string, $length = 100)
{
    if(!$string){
        $rString = '';
    }else{
        $htmlCut = new HtmlCutString($string, $length);
        $rString = $htmlCut->cut();
    }

    return $rString;
}

function is_organisation_admin()
{
    global $wpdb;

    $current_user_id = get_current_user_id();

    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "organisations_members WHERE is_admin=1 AND user_id=%d", $current_user_id));

    if ($row) {
        return true;
    }

    return false;
}
function getTestSuitProducts( $productID ){
    global $wpdb;
    return $wpdb->get_results( $wpdb->prepare("SELECT suite_title FROM {$wpdb->prefix}test_plans AS tp
                                                LEFT JOIN {$wpdb->prefix}test_suites AS ts ON ts.suite_id = tp.suite_id
                                                WHERE tp.product_id = %d ", $productID ));
}
function getProductsByTestSuiteName( $name, $withoutTestSuite = false ){
    global $wpdb;
    if( $withoutTestSuite ){
        return $wpdb->get_var("SELECT GROUP_CONCAT(product_id SEPARATOR ',') FROM {$wpdb->prefix}test_plans AS tp JOIN {$wpdb->prefix}test_suites AS ts ON ts.suite_id = tp.suite_id");
    }
    return $wpdb->get_var( $wpdb->prepare("SELECT GROUP_CONCAT(product_id SEPARATOR ',') FROM {$wpdb->prefix}test_plans AS tp LEFT JOIN {$wpdb->prefix}test_suites AS ts ON ts.suite_id = tp.suite_id WHERE ts.suite_title = %s ", $name ));

}

function generateDataAndDownload( $data ){
    ob_clean();
    global $wpdb;
    header("Content-type: application/x-msdownload",true,200);
    header("Content-Disposition: attachment; filename=results.csv");
    $outstream = fopen("php://output", "w");
    fputcsv($outstream, array(
        'Product Name',
        'Product ID',
        'Product Owner',
        'Product Version',
        'Product Release Date',
        'Visibility',
        'Community Name',
        'Test Suite Name',
        'Test Suite Version',
        'Issuer',
        'Conformance Level',
        'Role',
        'Status',
        'Test Plan date',
        'Claim Date',
        'Claim ID'
    ));
    foreach($data as $key => $result){
        $product = new ProductAndService($result->ID);
        $product->load();
        $getItemTestPlans = getTestPlansByProductId($result->ID);
        if( count( $getItemTestPlans ) > 0 ){
            foreach( $getItemTestPlans AS $testPlan ){
                $suite = new TestSuite($testPlan->suite_id);
                $suite->load();
                $claim = getClaimByTestPlanData( array( 'product_id' => $result->ID, 'suite_id' => $testPlan->suite_id ) );
                $group = groups_get_group( array( 'group_id' => $suite->community_id ) );
                if( $claim && ( $claim->conformance_level !== str_replace(';;', '', $testPlan->level) || $claim->role !== str_replace(';;', '', $testPlan->role) ) ){
                    $tempArray = array(
                        $product->name,
                        $product->product_id,
                        $product->owner,
                        $product->version,
                        date('d-M-y', strtotime( $product->release_date ) ),
                        $product->visibility,
                        $group->name,
                        $suite->name,
                        isset( $claim->claim_id ) && ! empty( $claim->claim_id ) ?  get_the_title( $testPlan->suite_id ) : ct_get_suite_max_version( $testPlan->suite_id ),
                        $claim->issuer,
                        $claim->conformance_level,
                        $claim->role,
                        $claim->status,
                        $suite->issueDate,
                        date('d-M-y', strtotime( $claim->last_updated ) ),
                        $claim->claim_id
                    );
                    fputcsv( $outstream, $tempArray );
                    $tempArray = array(
                        $product->name,
                        $product->product_id,
                        $product->owner,
                        $product->version,
                        date('d-M-y', strtotime( $product->release_date ) ),
                        $product->visibility,
                        $group->name,
                        $suite->name,
                        isset( $claim->claim_id ) && ! empty( $claim->claim_id ) ?  get_the_title( $testPlan->suite_id ) : ct_get_suite_max_version( $testPlan->suite_id ),
                        $suite->issuer,
                        str_replace( ';;',' ', $testPlan->level ),
                        str_replace( ';;',' ', $testPlan->role ),
                        $suite->status,
                        $suite->issueDate
                    );
                    fputcsv( $outstream, $tempArray );
                } else if( $claim ){
                    $tempArray = array(
                        $product->name,
                        $product->product_id,
                        $product->owner,
                        $product->version,
                        date('d-M-y', strtotime( $product->release_date ) ),
                        $product->visibility,
                        $group->name,
                        $suite->name,
                        isset( $claim->claim_id ) && ! empty( $claim->claim_id ) ?  get_the_title( $testPlan->suite_id ) : ct_get_suite_max_version( $testPlan->suite_id ),
                        $claim->issuer,
                        $claim->conformance_level,
                        $claim->role,
                        $claim->status,
                        $suite->issueDate,
                        date('d-M-y', strtotime( $claim->last_updated ) ),
                        $claim->claim_id
                    );
                    fputcsv( $outstream, $tempArray );
                }
                 else {
                    $tempArray = array(
                        $product->name,
                        $product->product_id,
                        $product->owner,
                        $product->version,
                        date('d-M-y', strtotime( $product->release_date ) ),
                        $product->visibility,
                        $group->name,
                        $suite->name,
                        isset( $claim->claim_id ) && ! empty( $claim->claim_id ) ?  get_the_title( $testPlan->suite_id ) : ct_get_suite_max_version( $testPlan->suite_id ),
                        $suite->issuer,
                        str_replace( ';;',' ', $testPlan->level ),
                        str_replace( ';;',' ', $testPlan->role ),
                        $suite->status,
                        $suite->issueDate
                    );
                    fputcsv( $outstream, $tempArray );
                }
            }
        } else {
            $tempArray = array(
                $product->name,
                $product->product_id,
                $product->owner,
                $product->version,
                date('d-M-y', strtotime( $product->release_date ) ),
                $product->visibility,
            );
            fputcsv( $outstream, $tempArray );
        }
    }
    fclose($outstream);
    exit();
}

function generate_and_download( $data ){
    global $wpdb;
    ob_clean();
    header("Content-type: application/x-msdownload",true,200);
    header("Content-Disposition: attachment; filename=results.csv");
    $outstream = fopen("php://output", "w");
    fputcsv($outstream, array(
        'Product ID',
        'Product Name',
        'Version',
        'Owner',
        'Type',
        'Visibility',
        'Test Suite',
        'Role',
        'Level',
        'Test Status',
        'Test Type',
        'Start Date',
        'Claim Date',
        'Certificate Number',
        'Certificate URL',
        'Service ID',
        'Service Name',
        'Entity ID',
        'Entity ID Type',
        'E2E Partner Service ID'
    ));
    if (is_array($data['hits']['hit'])) {
        foreach( $data['hits']['hit'] as $row ){
            $row_data = $row['fields'];
            if( $row_data['type'][0] == 'Agreement' ){
                $agreement_id = str_replace( 'agreement_', '', $row['id'] );
                $agreement = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM wp_e2e_agreement WHERE id = %d ", $agreement_id ) );
                $requester_service = new Service( $agreement->requester_service_id );
                $requester_service->load();
                $responder_service = new Service( $agreement->responder_service_id );
                $responder_service->load();
                $claim_date = date( 'Y-m-d', strtotime( $row_data['date'][0] ) );
                $s3 = new S3Wrapper();
                //requester entry
                $tempArray = array(
                    $row_data['product_id'][0],
                    $row_data['product_name'][0],
                    ! empty( $requester_service->service_version )  ? $requester_service->service_version : '',
                    $requester_service->service_owner,
                    $row_data['type'][0],
                    process_visibility( $row_data['visibility'] ),
                    get_the_title( $requester_service->service_suite_id ),
                    implode( ', ', $requester_service->service_roles ),
                    implode( ', ', $requester_service->service_levels ),
                    $row_data['status'][0],
                    $row_data['test_type'][0],
                    date( 'Y-m-d', strtotime( $row_data['start_date'][0] ) ),
                    $claim_date != '1970-01-01' && $row_data['status'][0] == 'Verified' ? $claim_date : '',
                    $row_data['cert_number'][0],
                    $row_data['status'][0] == 'Verified' ? $row_data['cert_url'][0] : '',
                    $row_data['service_id'][0],
                    $row_data['service_name'][0],
                    $row_data['entity_id'][0],
                    $row_data['entity_id_type'][0],
                    $row_data['e2e_partner_service_id'][0]
                );
                fputcsv( $outstream, $tempArray );
                //responder entry
                $tempArray = array(
                    $responder_service->service_product_id,
                    get_the_title( $responder_service->service_product_id ),
                    ! empty( $responder_service->service_version )  ? $responder_service->service_version : '',
                    $responder_service->service_owner,
                    $row_data['type'][0],
                    process_visibility( $row_data['visibility'] ),
                    get_the_title( $responder_service->service_suite_id ),
                    implode( ', ', $responder_service->service_roles ),
                    implode( ', ', $responder_service->service_levels ),
                    $row_data['status'][0],
                    $row_data['test_type'][0],
                    date( 'Y-m-d', strtotime( $row_data['start_date'][0] ) ),
                    $claim_date != '1970-01-01' && $row_data['status'][0] == 'Verified' ? $claim_date : '',
                    $row_data['cert_number'][0],
                    $row_data['status'][0] == 'Verified' ? $s3->getAgreementClaimLink( $agreement->responder_token ) : '',
                    $responder_service->id,
                    $responder_service->service_name,
                    $responder_service->service_id,
                    $responder_service->service_type,
                    $requester_service->id
                );
                fputcsv( $outstream, $tempArray );
            } else{
                $claim_date = date( 'Y-m-d', strtotime($row_data['date'][0] ) );
                $tempArray = array(
                    $row_data['product_id'][0],
                    $row_data['product_name'][0],
                    isset( $row_data['version'][0] ) ? $row_data['version'][0] : '',
                    $row_data['owner'][0],
                    $row_data['type'][0] == 'Web Service' ? 'Service' : 'Product',
                    process_visibility( $row_data['visibility'] ),
                    $row_data['test_suite'][0],
                    ! empty( $row_data['role'] )  ? implode( ', ', $row_data['role'] ) : '',
                    ! empty( $row_data['level'] )  ? implode( ', ', $row_data['level'] ) : '',
                    $row_data['status'][0],
                    $row_data['test_type'][0],
                    isset( $row_data['start_date'][0] ) ? date( 'Y-m-d', strtotime($row_data['start_date'][0] ) ) : '',
                    $claim_date != '1970-01-01' && $row_data['status'][0] == 'Verified' ? $claim_date : '',
                    isset( $row_data['cert_number'][0] ) ? $row_data['cert_number'][0] : '',
                    isset( $row_data['cert_url'][0] ) ? $row_data['cert_url'][0] : '',
                    isset( $row_data['service_id'][0] ) ? $row_data['service_id'][0] : '',
                    isset( $row_data['service_name'][0] ) ? $row_data['service_name'][0] : '',
                    isset( $row_data['entity_id'][0] ) ? $row_data['entity_id'][0] : '',
                    isset( $row_data['entity_id_type'][0] ) ? $row_data['entity_id_type'][0] : '',
                    isset( $row_data['e2e_partner_service_id'][0] ) ? $row_data['e2e_partner_service_id'][0] : ''
                );
                fputcsv( $outstream, $tempArray );
            }
        }
    }
    fclose($outstream);
    exit();
}
function process_visibility( $visibility ){
    if( in_array( 1, $visibility ) ){
        return 'Public';
    }
    if( in_array( 2, $visibility ) ){
        return 'Community';
    }
    return 'Private';
}
function generate_and_download_site( $data ){
    ob_clean();
    header("Content-type: application/x-msdownload",true,200);
    header("Content-Disposition: attachment; filename=results.csv");
    $outstream = fopen("php://output", "w");
    fputcsv($outstream, array(
        'Title',
        'Description',
        'Type',
        'Community',
        'Last Update'
    ));
    if (is_array($data['hits']['hit'])) {
        foreach( $data['hits']['hit'] as $row ){
            $row_data = $row['fields'];
                $tempArray = array(
                    $row_data['post_title'][0],
                    $row_data['post_content'][0],
                    $row_data['post_type'][0],
                    ! empty( $row_data['community'] ) && is_array( $row_data['community'] ) ? implode( ',', $row_data['community'] ) : '',
                    date( 'Y-m-d', strtotime( $row_data['last_updated_date'][0] ) )
                );
                fputcsv( $outstream, $tempArray );
        }
    }
    fclose($outstream);
    exit();
}

function groups_is_user_admin_in_any_community( $user_id, $communitiesList = false  ){
    global $wpdb;
    $communities_ids = $wpdb->get_results("SELECT id FROM {$wpdb->prefix}bp_groups");
    foreach( $communities_ids AS $communities_id ){
        if( ! $communitiesList ){
            if( groups_is_user_admin( $user_id, $communities_id->id ) ){
                return true;
            }
        } else {
            if( groups_is_user_admin( $user_id, $communities_id->id ) && in_array( $communities_id->id, $communitiesList ) ){
                return true;
            }
        }
    }
    return false;
}

function ct_read_xml_from_amazon_s3($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}

/**
 * Determine if a variable is iterable. i.e. can be used to loop over.
 *
 * @return bool
 */
function is_iterable( $var )
{
    return $var !== null
    && (is_array($var)
        || $var instanceof Traversable
        || $var instanceof Iterator
        || $var instanceof IteratorAggregate
        || $var instanceof stdClass
    );
}

function _trace( $data, $exit = false ){
    echo '<pre>'.print_r( $data, true ).'</pre>';
    if( $exit ) exit();
}
add_filter( 'w3tc_can_print_comment', function( $w3tc_setting ) { return false; }, 10, 1 );

/**
 * this part execution cron batch cronjobs
 */
if( isset( $_GET['jobid'] ) && isset( $_GET['key'] ) ){
    require_once( THE_FUNCTION . '/classes/BatchJob.php' );
    $batchJob = new BatchJob();
    $batchJob->execute( $_GET['jobid'], $_GET['key'] );
}

function validateDate($date)
{
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') == $date;
}

/**
 * his hook used to allow non-wp-admins to attach files to articles
 */
