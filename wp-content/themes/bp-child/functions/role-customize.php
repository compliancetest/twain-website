<?php
/***
* Customize Role Names and add custom Capacity
*/

if(!defined('ROLE_LEVEL_SYSTEM_ADMINISTRATOR'))
    define('ROLE_LEVEL_SYSTEM_ADMINISTRATOR', 4);
if(!defined('ROLE_LEVEL_COMMUNITY_ADMINISTRATOR'))
    define('ROLE_LEVEL_COMMUNITY_ADMINISTRATOR', 3);
if(!defined('ROLE_LEVEL_CUSTOMER'))
    define('ROLE_LEVEL_CUSTOMER', 2);
if(!defined('ROLE_LEVEL_REGISTERED'))
    define('ROLE_LEVEL_REGISTERED', 2);
if(!defined('ROLE_LEVEL_ANONYMOUS'))
    define('ROLE_LEVEL_ANONYMOUS', 1);


//Define Custom Roles
$customCapabilities = array(
    //System Administrator
    'administrator' => array(
                            'create_group',
                            'edit_other_group',
                            'delete_other_group',
                            'edit_group',
                            'delete_group',
                            
                            'create_suite',
                            'edit_other_suite',
                            'delete_other_suite',
                            'edit_suite',
                            'delete_suite',
                            
                            'create_case',
                            'edit_other_case',
                            'delete_other_case',
                            'edit_case',
                            'delete_case',
                            'read_case',
                            
                            'create_product_service',
                            'edit_other_product_service',
                            'delete_other_product_service',
                            'edit_product_service',
                            'delete_product_service',
                            ),
    //Community Administrator
    'author'        => array(
                            'edit_group',
                            'delete_group',
                            
                            'create_suite',
                            'edit_other_suite',
                            'delete_other_suite',
                            'edit_suite',
                            'delete_suite',
                            
                            'create_case',
                            'edit_other_case',
                            'delete_other_case',
                            'edit_case',
                            'delete_case',
                            'read_case',
                            
                            'create_product_service',
                            'edit_other_product_service',
                            'delete_other_product_service',
                            'edit_product_service',
                            'delete_product_service'
                            ),
    //Customer
    'contributor'        => array(
                            'read_case',                            
                            'create_product_service',
                            'edit_product_service',
                            'delete_product_service'
                            )
    
    
    
);

//Customize Role Names
function customize_role_name()
{
    global $wp_roles;
    
    if(!isset($wp_roles))
        $wp_roles = new WP_Roles();
    
    //Change administrator to System Administrator
    $wp_roles->roles['administrator']['name'] = 'System Administrator';
    $wp_roles->role_names['administrator'] = 'System Administrator'; 
    
    //Change Author to Community Administrator
    $wp_roles->roles['author']['name'] = 'Community Administrator';
    $wp_roles->role_names['author'] = 'Community Administrator'; 
    
    //Change Contributor to Customer
    $wp_roles->roles['contributor']['name'] = 'Customer';
    $wp_roles->role_names['contributor'] = 'Customer'; 
    
    //Change Subscriber to Registered User
    $wp_roles->roles['subscriber']['name'] = 'Registered User';
    $wp_roles->role_names['subscriber'] = 'Registered User'; 
    
    
}
add_action('init', 'customize_role_name', 1);

add_action('init', 'add_custom_capabilities', 2);
function add_custom_capabilities()
{
    global $customCapabilities;
    
    $roles = array('administrator', 'author', 'contributor');
    foreach($roles as $r)
    {
        $role = get_role($r);
        foreach($customCapabilities[$r] as $c)   
        {
            $role->add_cap($c);
        }
    }
    
}