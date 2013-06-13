<?php
/**
* The Gate Way function for users action
*/
require_once('user-auth.php');
require_once('user-profile.php');


add_action('init', 'compliancetest_user_actions');
//Process User Login, Register Action
function compliancetest_user_actions()
{
    if ( ! current_user_can( 'manage_options' ) ) {
        show_admin_bar( false );
    }
    $cpAction = isset($_REQUEST['cp-action']) ? $_REQUEST['cp-action'] : null;
    
    switch($cpAction)
    {
        case 'login':
            compliancetest_login();
            break;
        case 'register':
            compliancetest_create_new_user();
            break;
        case 'resend_email_verification':
            resend_email_verification();
            break;
        case 'user_activation':
            cp_activate_user();
            break;
        case 'my_details_edit':
            cp_user_detail_edit();
            break;
        case 'my_payment_edit':
            cp_user_payment_edit();
            break;
        case 'my_organisation_edit':
            cp_user_organisation_edit();
            break;
        
    }
}

//Add Js File
add_action('wp_head', 'add_user_script');
function add_user_script()
{
    $actions_depends = array('jquery');
    if (stripos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) {
        wp_enqueue_script('pie', get_stylesheet_directory_uri().'/js/PIE.js', $actions_depends);
        $actions_depends[] = 'pie';
    }
    $actions_depends[] = 'jquery_form';
    $actions_depends[] = 'custom_scripts';
    
    wp_enqueue_script('user-auth', get_stylesheet_directory_uri() . '/functions/user/user.js', $actions_depends);
}