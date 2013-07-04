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
        remove_action('wp_head', '_admin_bar_bump_cb');
    }
    $cpAction = isset($_REQUEST['cp-action']) ? $_REQUEST['cp-action'] : null;
    if($cpAction == 'login')
    {
        compliancetest_login();
    }else if($cpAction == 'register'){
        compliancetest_create_new_user();
    }else if($cpAction == 'resend_email_verification'){
        resend_email_verification();
    }else if($cpAction == 'user_activation'){
        cp_activate_user();
    }else if(wp_verify_nonce($cpAction,'my_details_edit')){
        cp_user_detail_edit();
    }else if(wp_verify_nonce($cpAction ,'save_payment_method')){
        cp_user_payment_edit();
    }else if(wp_verify_nonce($cpAction ,'my_organisation_edit')){
        cp_user_organisation_edit();
    }else if(wp_verify_nonce($cpAction ,'delete_payment_method')){
        cp_delete_payment_method();
    }
}

function getUserCreditCards($user_id = null)
{
    global $wpdb;
    
    if(!$user_id)
        $user_id = get_current_user_id();
    
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "users_cards WHERE user_id=%d", $user_id);
    $rows = $wpdb->get_results($query);
    
    return $rows;
}

function getUserCardById($card_id, $user_id = null)
{
    global $wpdb;
    
    if(!$user_id)
        $user_id = get_current_user_id();
    
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "users_cards WHERE user_id=%d and id=%d", $user_id, $card_id);
    $row = $wpdb->get_row($query);
    
    return $row;
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


//Get Groups that the user is a admin of
function getUserAdminGroups($user_id)
{
    $groups = groups_get_groups( array('user_id' => $user_id) );
    
    $result = array();
    foreach($groups['groups'] as $g)
    {
        if(groups_is_user_admin($user_id, $g->id))
        {            
            $result[] = $g;
        }
    }
    
    return $result;
}

//Get User Test Suites
function getUserTestSuites($user_id = null)
{
    if($user_id == null)
        $user_id = get_current_user_id();
    
    //Getting User Groups
    $groups = groups_get_groups( array('user_id' => $user_id) );
    
    $args = array(
        'post_type' => 'test-suite', 
        'posts_per_page' => -1,
        'meta_query' => array(
            'relation' => 'OR'            
        )
    );
    
    if(!is_admin() && !is_super_admin())
    {        
        foreach($groups['groups'] as $group)
        {
            $args['meta_query'][] = array(
                    'key' => 'community_id',
                    'value' => $group->id,
                    'compare' => '='
                );
        }
    }
    
    $testsuites = get_posts( $args );
    
    return $testsuites;
}

function getUserProductsAndServices($user_id = null, $exclusive = array())
{
    if($user_id == null)
        $user_id = get_current_user_id();
    
    $args = array(
        'post_type' => 'product-service', 
        'posts_per_page' => -1,
        'author' => $user_id
    );
    
    
    $rows = get_posts($args);
    $results = array();
    
    if(!$exclusive)
    {        
        $results = $rows;
    }else{
        foreach($rows as $row)
        {
            if(in_array($row->ID, $exclusive))
                continue;
            $results[] = $row;
        }    
    }
    
    return $results;
}
