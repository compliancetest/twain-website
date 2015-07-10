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
            $redirect = isset($_REQUEST['return']) ? base64_decode($_REQUEST['return']) : $_SERVER['REDIRECT_URL'];
            $result = deleteProfileTypeInstance( $action, $_REQUEST['id'] );
            addMessage( $result['message'], $result['status'] );
            wp_redirect($redirect);
            exit();
        }else if(wp_verify_nonce($action, 'copy-harness-instance')){
            $redirect = isset( $_REQUEST['return'] ) ? base64_decode( $_REQUEST['return'] ) : $_SERVER['REDIRECT_URL'];
            $profileId = intval( $_REQUEST['id'] );
            $result = copyProfileInstance( $profileId );
            if( $result['status'] == 'error' ){
                addMessage( $result['message'], 'error' );
                return;
            }
            addMessage( 'Profile instance was copied.' );
            wp_redirect( $redirect );
            exit;
        }else if(wp_verify_nonce($action, 'download-profile-type')){
            downloadProfileType();
        }else if(wp_verify_nonce($action, 'download-profile-instance')){
            downloadProfileTypeInstance();
        }else if(wp_verify_nonce($action, 'download-profile-error')){
            downloadProfileError();
        }else if(wp_verify_nonce($action, 'update-profile-lookup')){
            updateProfileLookup();
        }else if(wp_verify_nonce($action, 'create-expanded-version')){
            createExpandedVersion( $_REQUEST['id'], $_REQUEST['factor'] );
            exit('success');
        }else if(wp_verify_nonce($action, 'prepare_schedule')){
            render_view( 'test-data/views/schedule-popup.phtml', (object) $_GET, true );
        }else if(wp_verify_nonce($action, 'trigger_run')){
            render_view( 'test-data/views/trigger.phtml', (object) $_GET, true );
        }else if(wp_verify_nonce($action, 'save-schedule')){
            $profileId = intval( $_POST['profile_id'] );
            \MicroServices\MicroServices::prepareRunRequest($profileId);
        }else if( wp_verify_nonce($action, 'execute-schedule') ){
            if ( !empty($_POST['datetime']) && getUTCTimeStamp( strtotime( $_POST['datetime'] ) ) < strtotime(gmdate('Y-m-d H:i'))) {
                exit('error');
            }
            $profileId = intval( $_POST['profile_id'] );
            \MicroServices\MicroServices::executeRunRequest( $profileId, date( 'Y-m-d H:i:s', getUTCTimeStamp( strtotime( $_POST['datetime'].':00' ) ) ) );
            $profile = ProfileInstance::getProfileBy('id', $profileId);
            $esb = new ManageESB();
            $esb->updateStatusByProfileS3Url($profile->token, 'STARTING', 'PREPARED');
            exit('success');
        }else if( wp_verify_nonce($action, 'change-schedule-status') ){
            $esb = new ManageESB();
            $s3Url = $esb->updateStatus( $_POST['id'], $_POST['status'], $_POST['prevstatus']);
            if( 'DELETED' == $_POST['status'] ){
                $profileUrl = explode('/', $s3Url->PROFILE_S3_URL );
                $profileToken = str_replace( '.json', '', end( $profileUrl ) );
                //removing all Run profiles which were created from this Schedule
                $runProfile = ProfileInstance::getProfileBy( 'token', $profileToken );
                deleteProfileTypeInstance( wp_create_nonce('delete-profile-instance'), $runProfile->id, true  );
            }
            render_view( 'test-data/views/trigger-schedule.phtml', true, true );
        }


    }
}