<?php
/**
* Test Data  
*/

require_once(THE_FUNCTION . '/test-data/functions.php');
require_once(THE_FUNCTION . '/test-data/controller.php');
require_once(THE_FUNCTION . '/test-data/view.php');

add_action('init', 'cp_process_test_data_actions');

function cp_process_test_data_actions()
{
    global $wpdb;
    
    $action = isset($_REQUEST['td-action']) ? $_REQUEST['td-action'] : null;
    if($action)
    {
        if(wp_verify_nonce($action, 'edit-profile-type'))
        {
            readProfileType();            
        }else if(wp_verify_nonce($action, 'save-profile-type')){
            saveProfileType();
        }else if(wp_verify_nonce($action, 'delete-profile-type')){
            deleteProfileType();
        }else if(wp_verify_nonce($action, 'get-harness-profile-ui') || wp_verify_nonce($action, 'get-tester-profile-ui')){
            createUIFromProfileType($action);
        }else if(wp_verify_nonce($action, 'save-harness-instance') || wp_verify_nonce($action, 'save-tester-instance')){
            saveProfileInstance($action);
        }else if(wp_verify_nonce($action, 'view-profile-type')){
            viewProfileType();
        }else if(wp_verify_nonce($action, 'view-profile-instance')){
            viewProfileInstance();
        }else if(wp_verify_nonce($action, 'delete-harness-instance') || wp_verify_nonce($action, 'delete-profile-instance')){
            deleteProfileTypeInstance($action);
        }else if(wp_verify_nonce($action, 'download-profile-type')){
            downloadProfileType();
        }else if(wp_verify_nonce($action, 'download-profile-instance')){
            downloadProfileTypeInstance();
        }else if(wp_verify_nonce($action, 'download-profile-error')){
            downloadProfileError();
        }else if(wp_verify_nonce($action, 'update-profile-lookup')){
            updateProfileLookup();
        }
    }
}