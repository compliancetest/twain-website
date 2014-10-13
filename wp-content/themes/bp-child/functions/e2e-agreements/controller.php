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
        $service = new Service( $wpdb->get_var( $wpdb->prepare( "SELECT requester_service_id FROM wp_e2e_agreement WHERE id = %d", $agreement_id ) ) );
        $service->load();
        //send notifications to sender, receiver and admin
        $sender = get_userdata( get_current_user_id() );
        $receiver = get_userdata( $service->service_user_id );
        $email_data = array(
            '[sender_name]'   => $sender->data->display_name,
            '[receiver_name]' => $receiver->data->display_name,
            '[agreement_url]' => home_url( '/service/service/'),
            '[test_suite]'    => get_the_title( $service->service_suite_id )
        );
        //send email to requester
        cp_send_email( array('name' => $sender->data->display_name, 'email' => $sender->data->user_email), 'e2e_request_accepted_sender', $email_data );

        //send email to receiver
        cp_send_email( array('name' => $receiver->data->display_name, 'email' => $receiver->data->user_email), 'e2e_request_accepted_receiver', $email_data );

        //send email to admin
        cp_send_email_to_admin( 'e2e_request_accepted_admin', $email_data );
        addMessage('Success');
        wp_redirect('/agreements/');
        exit;
    } else if( wp_verify_nonce($action, 'cancel-agreement' ) ) {
        $agreement_id = intval($_REQUEST['id']);
        $wpdb->query( $wpdb->prepare( "DELETE FROM wp_e2e_agreement WHERE id = %d ", $agreement_id ) );
        addMessage('Success');
        wp_redirect('/agreements/');
        exit;
    } else if( wp_verify_nonce($action, 'claim_agreement' ) ){
        $agreement_id = intval($_REQUEST['agreement_id']);
        if($_FILES["file"]["size"] > 0 ) {
            $fileName = $_FILES['file']['name'];
            $tmpName = $_FILES['file']['tmp_name'];
            $fileType = $_FILES['file']['type'];

            $fp = fopen($tmpName, 'r');
            $content = fread($fp, filesize($tmpName));
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
        if($_FILES["file"]["size"] > 0 ) {
            $fileName = $_FILES['file']['name'];
            $tmpName = $_FILES['file']['tmp_name'];
            $fileType = $_FILES['file']['type'];

            $fp = fopen($tmpName, 'r');
            $content = fread($fp, filesize($tmpName));
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
    } else if( wp_verify_nonce($action, 'get-agreement-file' ) ){
        $agreement_id = intval( $_REQUEST['agreement_id'] );
        //requester message
        $log = $wpdb->get_row( $wpdb->prepare("SELECT * FROM wp_e2e_agreement WHERE id=%d", $agreement_id ) );
        if( ! $log )
        {
            echo "Invalid Request!";
            exit;
        }
        $t = $_REQUEST['type'];
        if( $t == '1' ){
            $content_type = $log->requestor_audit_log_type;
            $file_name    = $log->requestor_audit_log_name;
            $content      = $log->requestor_audit_log;
        } else {
            $content_type = $log->responder_audit_log_type;
            $file_name    = $log->responder_audit_log_name;
            $content      = $log->responder_audit_log;
        }
//        var_dump( $_REQUEST, $content_type, $file_name);die;
        header("Content-type: ".$content_type );
        header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
        header("Last-Modified: " . gmdate("D,d M Y H:i:s") . " GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-Type: Application/octet-stream");
        header("Content-disposition: attachment; filename=".$file_name);

        echo  $content ;
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

