<?php
add_action('init', 'process_agreement_actions');
function process_agreement_actions()
{
    global $wpdb;
    $action = isset($_REQUEST['_psnonce']) ? $_REQUEST['_psnonce'] : null;
    if(wp_verify_nonce($action, 'save-agreement')){
        saveAgreement();
    }else if(wp_verify_nonce($action, 'delete-agreement')){
        deleteAgreement();
    } else if( wp_verify_nonce($action, 'accept-agreement' ) ) {
        $agreement_id = intval($_REQUEST['agreement_id']);
        $wpdb->update('wp_e2e_agreement',
            array(
                'status'                 => 'Testing',
                'responder_profile'      => $_REQUEST['responder_profiles'],
                'responder_message'      => $_REQUEST['agreement_message'],
                'responder_message_date' => gmmktime()
            ),
            array(
                'id' => $agreement_id
            ),
            array( '%s', '%s', '%s' ),
            array('%d')
        );
        addMessage('Success');
        wp_redirect('/agreements/');
        exit;
    } else if( wp_verify_nonce($action, 'cancel-agreement' ) ) {
        $agreement_id = intval($_REQUEST['id']);
        //todo check permissions to perform any action
        $wpdb->query( $wpdb->prepare( "DELETE FROM wp_e2e_agreement WHERE id = %d ", $agreement_id ) );
        addMessage('Success');
        wp_redirect('/agreements/');
        exit;
    } else if( wp_verify_nonce($action, 'claim_agreement' ) ){
        $agreement_id = intval($_REQUEST['agreement_id']);
        //todo check permissions to perform any action
        if($_FILES["file"]["size"] > 0 ) {
            $fileName = $_FILES['file']['name'];
            $tmpName = $_FILES['file']['tmp_name'];
            $fileType = $_FILES['file']['type'];

            $fp = fopen($tmpName, 'r');
            $content = fread($fp, filesize($tmpName));
            $content = addslashes($content);
            fclose($fp);
            if( $_REQUEST['role'] == 'Requester' ){
                $file_field = 'requestor_audit_log';
                $name_field = 'requestor_audit_log_name';
                $type_field = 'requestor_audit_log_type';
            } else{
                $file_field = 'responder_audit_log';
                $name_field = 'responder_audit_log_name';
                $type_field = 'responder_audit_log_type';
            }
            $wpdb->update('wp_e2e_agreement',
                array(
                    'status'     => 'Claimed',
                    'scope'      => implode( ';;', @$_REQUEST['scope'] ),
                    $file_field  => $content,
                    $name_field  => $fileName,
                    $type_field  => $fileType
                ),
                array(
                    'id' => $agreement_id
                ),
                array( '%s', '%s', '%s', '%s', '%s' ),
                array( '%d' )
            );
        }
        addMessage( 'Success' );
        wp_redirect('/agreements/');
        exit;
    } else if( wp_verify_nonce($action, 'confirm-agreement' ) ){
        $agreement_id = intval($_REQUEST['agreement_id']);
        //todo check permissions to perform any action
        if($_FILES["file"]["size"] > 0 ) {
            $fileName = $_FILES['file']['name'];
            $tmpName = $_FILES['file']['tmp_name'];
            $fileType = $_FILES['file']['type'];

            $fp = fopen($tmpName, 'r');
            $content = fread($fp, filesize($tmpName));
            $content = addslashes($content);
            fclose($fp);
            if( $_REQUEST['role'] == 'Requester' ){
                $file_field = 'requestor_audit_log';
                $name_field = 'requestor_audit_log_name';
                $type_field = 'requestor_audit_log_type';
            } else{
                $file_field = 'responder_audit_log';
                $name_field = 'responder_audit_log_name';
                $type_field = 'responder_audit_log_type';
            }
            $wpdb->update('wp_e2e_agreement',
                array(
                    'status'     => 'Verified',
                    $file_field  => $content,
                    $name_field  => $fileName,
                    $type_field  => $fileType,
                    'claim_date' => gmmktime()
                ),
                array(
                    'id' => $agreement_id
                ),
                array( '%s', '%s', '%s', '%s' ),
                array( '%d' )
            );
        }
        addMessage( 'Success' );
        wp_redirect('/agreements/');
        exit;
    } else if( wp_verify_nonce($action, 'reject-pending-agreement' ) ){
        //todo send email with reason
        $agreement_id = intval($_REQUEST['agreement_id']);
        //todo check permissions to perform any action
        $wpdb->query( $wpdb->prepare( "DELETE FROM wp_e2e_agreement WHERE id = %d ", $agreement_id ) );
        addMessage('Success');
        wp_redirect('/agreements/');
        exit;
    }else if( wp_verify_nonce($action, 'reject-claimed-agreement' ) ){
        //todo send email with reason
        $agreement_id = intval($_REQUEST['agreement_id']);
        //todo check permissions to perform any action
        $wpdb->query( $wpdb->prepare( "DELETE FROM wp_e2e_agreement WHERE id = %d ", $agreement_id ) );
        addMessage('Success');
        wp_redirect('/agreements/');
        exit;
    }else if( wp_verify_nonce($action, 'reject-failed-agreement' ) ){
        //todo send email with reason
        $agreement_id = intval($_REQUEST['agreement_id']);
        //todo check permissions to perform any action
        $wpdb->update('wp_e2e_agreement',
            array(
                'status'                    => 'Testing',
                'requestor_audit_log'       => '',
                'requestor_audit_log_name'  => '',
                'requestor_audit_log_type'  => '',
                'responder_audit_log'       => '',
                'responder_audit_log_name'  => '',
                'responder_audit_log_type'  => ''
            ),
            array(
                'id' => $agreement_id
            ),
            array( '%s', '%s', '%s', '%s', '%s', '%s', '%s' ),
            array( '%d' )
        );
        addMessage('Success');
        wp_redirect('/agreements/');
        exit;
    }
}


function saveAgreement()
{
    //todo need to send emails
    $agreementModel = new Agreement();
    $agreementModel->addEntry( $_REQUEST );
    addMessage( 'Testing request has been sent successfully' );
    wp_redirect(get_permalink( $_REQUEST['responder_service']));
    exit;
}

