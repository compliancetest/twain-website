<?php
/***
* Redirect to the site index page from the wp-login page 
*/

add_action('login_init', 'cp_manage_login');
function cp_manage_login()
{
    $redirect_to = isset($_GET['redirect_to']) ? urldecode($_GET['redirect_to']) : '';
    
    if(strpos($redirect_to, '/wp-admin') === false && (!isset($_REQUEST['action']) || $_REQUEST['action'] != 'logout'))
    {        
        //Goto Front Page with the redirect url
        wp_redirect(get_site_url() . "?redirect_to=" . urlencode($redirect_to));
        exit;
    }
    
    
}
