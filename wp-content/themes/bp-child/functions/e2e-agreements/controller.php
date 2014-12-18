<?php

add_action('template_redirect', 'ct_agreement_certification_view');
//Display Agreement Certificate
function ct_agreement_certification_view()
{
    if(get_query_var('pagename') == 'agreement-certificate')
    {
        $token = str_replace( ".pdf", "", get_query_var('claim') );
        wp_redirect( S3Wrapper::getAgreementClaimLink( $token ), 301 );exit;
    }
}

add_action('init', 'process_agreement_actions');
function process_agreement_actions()
{
    global $wpdb;
    $action = isset($_REQUEST['_psnonce']) ? $_REQUEST['_psnonce'] : null;
    //new request for e2e testing
    if(wp_verify_nonce($action, 'save-agreement')){
        saveAgreement();
    }else if(wp_verify_nonce($action, 'delete-agreement')){
        if( is_super_admin() ){
            $agreement_id = intval($_REQUEST['id']);
            //delete item from CloudSearch domain
            $cloud_search = new CloudSearch();
            $cloud_search->cloud_search_delete_item( $agreement_id, 'agreement' );
            //delete S3 files
            $certificate = $wpdb->get_row( $wpdb->prepare("SELECT * FROM wp_e2e_agreement WHERE id = %d ", $agreement_id ) );
            $s3 = new S3Wrapper();
            $s3->deleteObject( '/attachments/agreements/'. $certificate->requester_token . '.'. pathinfo( $certificate->requestor_audit_log_name, PATHINFO_EXTENSION ) );
            $s3->deleteObject( '/attachments/agreements/'. $certificate->responder_token . '.'. pathinfo( $certificate->responder_audit_log_name, PATHINFO_EXTENSION ) );

            $s3->deleteObject( '/claims/agreements/'. $certificate->requester_token . '.pdf' );
            $s3->deleteObject( '/claims/agreements/'. $certificate->responder_token . '.pdf' );
            //delete local data
            $wpdb->query( $wpdb->prepare( "DELETE FROM wp_e2e_agreement WHERE id = %d ", $agreement_id ) );
            $wpdb->query( $wpdb->prepare( "DELETE FROM wp_e2e_agreement_log WHERE agreement_id = %d ", $agreement_id ) );
            addMessage('Success!');
        }else{
            addMessage('Forbidden!', 'error');
        }
        wp_redirect( wp_get_referer() );
        exit;
        //accept e2e testing request
    } else if( wp_verify_nonce($action, 'accept-agreement' ) ) {
        $agreement_id = intval($_REQUEST['agreement_id']);
        $service = new Service( $wpdb->get_var( $wpdb->prepare( "SELECT requester_service_id FROM wp_e2e_agreement WHERE id = %d", $agreement_id ) ) );
        $service->load();
        Agreement::has_access( 'edit-agreement', false, $agreement_id );
        $wpdb->update('wp_e2e_agreement',
            array(
                'status'                 => 'Testing',
                'responder_profile'      => $_REQUEST['responder_profiles'],
                'responder_name'         => get_user_meta( get_current_user_id(), 'first_name', true ).' '.get_user_meta( get_current_user_id(), 'last_name', true ),
                'responder_message'      => $_REQUEST['agreement_message'],
                'responder_message_date' => gmmktime(),
                'requester_token'        => createClaimToken(),
                'responder_token'        => createClaimToken()
            ),
            array(
                'id' => $agreement_id
            ),
            array( '%s', '%s', '%s', '%s', '%s' ),
            array('%d')
        );
        AgreementLog::add_entry( array(
            'agreement_id' => $agreement_id,
            'sent_by'      => 2,
            'message'      => $_REQUEST['agreement_message'],
            'state'        => 'Accept'

        ));
        $agreement = $wpdb->get_row( $wpdb->prepare("SELECT * FROM wp_e2e_agreement WHERE id = %d ", $agreement_id ) );
        //send notifications to sender, receiver and admin
        Agreement::send_agreement_email( 'accept', get_current_user_id(), $service->service_user_id, array('text'                => $_REQUEST['agreement_message'],
                                                                                                'sender_service_id'   =>  $agreement->responder_service_id,
                                                                                                'receiver_service_id' => $service->id )
        );
        $cloud_search = new CloudSearch();
        $cloud_search->cloud_search_update_agreement( $agreement_id );
        addMessage('Success');
        wp_redirect('/agreements/');
        exit;
    //cancel e2e testing
    } else if( wp_verify_nonce($action, 'cancel-agreement' ) ) {
        $agreement_id = intval($_REQUEST['agreement_id']);
        $requester_service = new Service( $wpdb->get_var( $wpdb->prepare( "SELECT requester_service_id FROM wp_e2e_agreement WHERE id = %d", $agreement_id ) ) );
        $requester_service->load();
        $responder_service = new Service( $wpdb->get_var( $wpdb->prepare( "SELECT responder_service_id FROM wp_e2e_agreement WHERE id = %d", $agreement_id ) ) );
        $responder_service->load();
        Agreement::has_access( 'edit-agreement', false, $agreement_id );

        if( ct_get_user_organisation( get_current_user_id() ) == ct_get_user_organisation( $requester_service->service_user_id ) ){
            $responder_user_id    = $responder_service->service_user_id;
            $requester_service_id = $requester_service->id;
            $responder_service_id = $responder_service->id;
        } else{
            $responder_user_id    = $requester_service->service_user_id;
            $requester_service_id = $responder_service->id;
            $responder_service_id = $requester_service->id;
        }
        Agreement::send_agreement_email( 'cancel', get_current_user_id(), $responder_user_id, array('text'     => $_REQUEST['deny-reason-field'],
                                                                                                'sender_service_id'   => $requester_service_id,
                                                                                                'receiver_service_id' => $responder_service_id
        ));
        //delete item from CloudSearch domain
        $cloud_search = new CloudSearch();
        $cloud_search->cloud_search_delete_item( $agreement_id, 'agreement' );

        $wpdb->query( $wpdb->prepare( "DELETE FROM wp_e2e_agreement WHERE id = %d ", $agreement_id ) );

        $wpdb->query( $wpdb->prepare( "DELETE FROM wp_e2e_agreement_log WHERE agreement_id = %d ", $agreement_id ) );

        addMessage('Success');
        wp_redirect('/agreements/');
        exit;
        //cancel e2e testing
    } else if( wp_verify_nonce($action, 'reject-agreement' ) ) {
        $agreement_id = intval($_REQUEST['agreement_id']);
        $service = new Service( $wpdb->get_var( $wpdb->prepare( "SELECT requester_service_id FROM wp_e2e_agreement WHERE id = %d", $agreement_id ) ) );
        $service->load();
        Agreement::has_access( 'edit-agreement', false, $agreement_id );

        $agreement = $wpdb->get_row( $wpdb->prepare("SELECT * FROM wp_e2e_agreement WHERE id = %d ", $agreement_id ) );


        Agreement::send_agreement_email( 'cancel', get_current_user_id(), $service->service_user_id, array( 'text'                => $_REQUEST['deny-reason-field'],
                                                                                                            'sender_service_id'   => $agreement->responder_service_id,
                                                                                                            'receiver_service_id' => $agreement->requester_service_id
        ));
        //delete item from CloudSearch domain
        $cloud_search = new CloudSearch();
        $cloud_search->cloud_search_delete_item( $agreement_id, 'agreement' );

        $wpdb->query( $wpdb->prepare( "DELETE FROM wp_e2e_agreement WHERE id = %d ", $agreement_id ) );

        $wpdb->query( $wpdb->prepare( "DELETE FROM wp_e2e_agreement_log WHERE agreement_id = %d ", $agreement_id ) );

        addMessage('Success');
        wp_redirect('/agreements/');
        exit;
    //claim accepted e2e testing agreement
    } else if( wp_verify_nonce($action, 'claim_agreement' ) ){
        $agreement_id = intval($_REQUEST['agreement_id']);
        Agreement::has_access( 'edit-agreement', false, $agreement_id );
        if($_FILES["file"]["size"] > 0 ) {
            $fileName = $_FILES['file']['name'];
            $tmpName = $_FILES['file']['tmp_name'];
            $fileType = $_FILES['file']['type'];

            $fp = fopen($tmpName, 'r');
            $content = fread($fp, filesize($tmpName));
            fclose($fp);
            if( $_REQUEST['role'] == 'Requester' ){
                $file_field   = 'requestor_audit_log';
                $name_field   = 'requestor_audit_log_name';
                $type_field   = 'requestor_audit_log_type';
                $r_name_field = 'requestor_name';
                $token_field  = 'requester_token';
                $service_id   = $wpdb->get_var( $wpdb->prepare( "SELECT responder_service_id FROM wp_e2e_agreement WHERE id = %d", $agreement_id ) );
                $sent_by      = 1;
            } else{
                $file_field   = 'responder_audit_log';
                $name_field   = 'responder_audit_log_name';
                $type_field   = 'responder_audit_log_type';
                $r_name_field = 'responder_name';
                $token_field  = 'responder_token';
                $service_id   = $wpdb->get_var( $wpdb->prepare( "SELECT requester_service_id FROM wp_e2e_agreement WHERE id = %d", $agreement_id ) ) ;
                $sent_by      = 2;
            }
            $service = new Service($service_id);
            $service->load();

            $certificate = $wpdb->get_row( $wpdb->prepare("SELECT * FROM wp_e2e_agreement WHERE id = %d ", $agreement_id ) );
            $s3 = new S3Wrapper();
            $s3->putObject( '/attachments/agreements/' . $certificate->{$token_field} . '/'. $fileName, $content, $type_field );

            $wpdb->update('wp_e2e_agreement',
                array(
                    'status'     => 'Claimed',
                    'scope'      => @implode( ';;', @$_REQUEST['scope'] ),
//                    $file_field  => $content,
                    $name_field  => $fileName,
                    $type_field  => $fileType,
                    $r_name_field => cp_get_user_fullname( get_current_user_id() ),
                ),
                array(
                    'id' => $agreement_id
                ),
                array( '%s', '%s', '%s', '%s', '%s' ),
                array( '%d' )
            );

            AgreementLog::add_entry( array(
                'agreement_id' => $agreement_id,
                'sent_by'      => $sent_by,
                'message'      => '',
                'state'        => 'Claim'

            ));

            $service->load();

            $agreement = $wpdb->get_row( $wpdb->prepare("SELECT * FROM wp_e2e_agreement WHERE id = %d ", $agreement_id ) );

            Agreement::send_agreement_email( 'claim', get_current_user_id(), $service->service_user_id, array('text'                => $_REQUEST['deny-reason-field'],
                                                                                                    'sender_service_id'   => $agreement->responder_service_id == $service_id ? $agreement->requester_service_id : $agreement->responder_service_id,
                                                                                                    'receiver_service_id' => $service_id )
            );
        }

        $cloud_search = new CloudSearch();
        $cloud_search->cloud_search_update_agreement( $agreement_id );

        addMessage( 'Success' );
        wp_redirect('/agreements/');
        exit;
    //accept claim
    } else if( wp_verify_nonce($action, 'confirm-agreement' ) ){
        $agreement_id = intval($_REQUEST['agreement_id']);
        Agreement::has_access( 'edit-agreement', false, $agreement_id );
        if($_FILES["file"]["size"] > 0 ) {
            $fileName = $_FILES['file']['name'];
            $tmpName  = $_FILES['file']['tmp_name'];
            $fileType = $_FILES['file']['type'];

            $fp = fopen($tmpName, 'r');
            $content = fread($fp, filesize($tmpName));
            fclose($fp);
            if( $_REQUEST['role'] == 'Requester' ){
                $file_field   = 'requestor_audit_log';
                $name_field   = 'requestor_audit_log_name';
                $type_field   = 'requestor_audit_log_type';
                $r_name_field = 'requestor_name';
                $token_field  = 'requester_token';
                $sent_by = 1;
            } else{
                $file_field   = 'responder_audit_log';
                $name_field   = 'responder_audit_log_name';
                $type_field   = 'responder_audit_log_type';
                $r_name_field = 'responder_name';
                $token_field  = 'responder_token';
                $sent_by = 2;
            }
            //generate PDF

            $service = new Service( $wpdb->get_var( $wpdb->prepare( "SELECT requester_service_id FROM wp_e2e_agreement WHERE id = %d", $agreement_id ) ) );
            $service->load();

            $wpdb->update('wp_e2e_agreement',
                array(
                    'status'      => 'Verified',
//                    $file_field   => $content,
                    $name_field   => $fileName,
                    $type_field   => $fileType,
                    $r_name_field => cp_get_user_fullname( get_current_user_id() ),
                    'claim_date'  => gmmktime(),
                    'claim_id'    => getClaimID( null, $service->service_suite_id ),

                ),
                array(
                    'id' => $agreement_id
                ),
                array( '%s', '%s', '%s', '%s', '%d', '%s' ),
                array( '%d' )
            );
            $certificate = $wpdb->get_row( $wpdb->prepare("SELECT * FROM wp_e2e_agreement WHERE id = %d ", $agreement_id ) );
            $s3 = new S3Wrapper();
            $s3->putObject( '/attachments/agreements/' . $certificate->{$token_field} . '/'. $fileName, $content, $type_field );
            AgreementLog::add_entry( array(
                'agreement_id' => $agreement_id,
                'sent_by'      => $sent_by,
                'message'      => '',
                'state'        => 'Claim Accept'

            ));

            $req_pdf = create_agreement_pdf( $agreement_id );
            $res_pdf = create_agreement_pdf( $agreement_id, true );

//            $wpdb->update('wp_e2e_agreement',
//                array(
//
//                    'responder_certificate' => $res_pdf,
//                    'requester_certificate' => $req_pdf,
//
//                ),
//                array(
//                    'id' => $agreement_id
//                ),
//                array( '%s', '%s' ),
//                array( '%d' )
//            );
            $certificate = $wpdb->get_row( $wpdb->prepare("SELECT * FROM wp_e2e_agreement WHERE id = %d ", $agreement_id ) );
            $s3 = new S3Wrapper();
            $s3->putObject('/claims/agreements/' . $certificate->requester_token . '.pdf', $req_pdf, 'application/pdf');
            $s3->putObject('/claims/agreements/' . $certificate->responder_token . '.pdf', $res_pdf, 'application/pdf');
            if( get_current_user_id() == $service->service_user_id ){
                $service = new Service( $wpdb->get_var( $wpdb->prepare( "SELECT responder_service_id FROM wp_e2e_agreement WHERE id = %d", $agreement_id ) ) );
                $service->load();
            }
            $agreement = $wpdb->get_row( $wpdb->prepare("SELECT * FROM wp_e2e_agreement WHERE id = %d ", $agreement_id ) );

            Agreement::send_agreement_email( 'confirm_claim', get_current_user_id(), $service->service_user_id, array('text'         => '',
                                                                                                    'sender_service_id'   => $agreement->responder_service_id == $service->id ? $agreement->requester_service_id : $agreement->responder_service_id,
                                                                                                    'receiver_service_id' => $service->id )
            );
        }

        $cloud_search = new CloudSearch();
        $cloud_search->cloud_search_update_agreement( $agreement_id );

        addMessage( 'Success' );
        wp_redirect('/agreements/');
        exit;
//    }else if( wp_verify_nonce($action, 'reject-claimed-agreement' ) ){
//        $agreement_id = intval($_REQUEST['agreement_id']);
//        Agreement::has_access( 'edit-agreement', false, $agreement_id );
//
//        $cloud_search = new CloudSearch();
//        $cloud_search->cloud_search_delete_item( $agreement_id, 'agreement' );
//        //delete local data
//        $wpdb->query( $wpdb->prepare( "DELETE FROM wp_e2e_agreement WHERE id = %d ", $agreement_id ) );
//        $wpdb->query( $wpdb->prepare( "DELETE FROM wp_e2e_agreement_log WHERE agreement_id = %d ", $agreement_id ) );
//
//        addMessage('Success');
//        wp_redirect('/agreements/');
//        exit;
    //reject claim
    }else if( wp_verify_nonce($action, 'reject-failed-agreement' ) ){
        $agreement_id = intval($_REQUEST['agreement_id']);
        Agreement::has_access( 'edit-agreement', false, $agreement_id );

        //delete S3 files
        $certificate = $wpdb->get_row( $wpdb->prepare("SELECT * FROM wp_e2e_agreement WHERE id = %d ", $agreement_id ) );
        $s3 = new S3Wrapper();
        $s3->deleteObject( '/attachments/agreements/'. $certificate->requester_token . '.'. pathinfo( $certificate->requestor_audit_log_name, PATHINFO_EXTENSION) );
        $s3->deleteObject( '/attachments/agreements/'. $certificate->responder_token . '.'. pathinfo( $certificate->responder_audit_log_name, PATHINFO_EXTENSION) );

        $service = new Service( $wpdb->get_var( $wpdb->prepare( "SELECT requester_service_id FROM wp_e2e_agreement WHERE id = %d", $agreement_id ) ) );
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


        $service->load();
        $sent_by = 2;
        if( ct_get_user_organisation( get_current_user_id() ) == ct_get_user_organisation( $service->service_user_id ) ){
            $service = new Service( $wpdb->get_var( $wpdb->prepare( "SELECT responder_service_id FROM wp_e2e_agreement WHERE id = %d", $agreement_id ) ) );
            $service->load();
            $sent_by = 1;
        }

        AgreementLog::add_entry( array(
            'agreement_id' => $agreement_id,
            'sent_by'      => $sent_by,
            'message'      => $_REQUEST['deny-reason-field'],
            'state'        => 'Claim Fail'

        ));
        $agreement = $wpdb->get_row( $wpdb->prepare("SELECT * FROM wp_e2e_agreement WHERE id = %d ", $agreement_id ) );

        Agreement::send_agreement_email( 'reject_claim', get_current_user_id(), $service->service_user_id, array('text'                 => $_REQUEST['deny-reason-field'],
                                                                                                        'sender_service_id'   => $agreement->responder_service_id == $service->id ? $agreement->requester_service_id : $agreement->responder_service_id,
                                                                                                        'receiver_service_id' => $service->id )
        );

        $cloud_search = new CloudSearch();
        $cloud_search->cloud_search_update_agreement( $agreement_id );

        addMessage('Success');
        wp_redirect('/agreements/');
        exit;
    } else if( wp_verify_nonce($action, 'get-agreement-file' ) || $_REQUEST['_psnonce'] == 'get-agreement-file' ){
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
        header("Content-type: ".$content_type );
        header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
        header("Last-Modified: " . gmdate("D,d M Y H:i:s") . " GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-Type: Application/octet-stream");
        header("Content-disposition: attachment; filename=".$file_name);

        echo  $content ;
        exit;
    } else if( wp_verify_nonce($action, 'get-agreement-info-popup' ) ){
        get_agreement_info_popup();
    }else if( wp_verify_nonce($action, 'get-agreement-id' ) ){
        $requester_service_id = $_POST['requester_service'];
        $responder_service_id = $_POST['responder_service'];
        
        $new_id = get_post_meta($requester_service_id, 'service_id', true) . "." . get_post_meta($responder_service_id, 'service_id', true);
        //Check Duplication
        $counter = 1;
        do{
            $result = $new_id.'.'.str_pad( $counter++, 2, 0, STR_PAD_LEFT);
        } while( $wpdb->get_row( $wpdb->prepare("SELECT * FROM wp_e2e_agreement WHERE str_id = %s ", $result ) ) );
        exit( $result );
    } else if( wp_verify_nonce($action, 'get-agreement-pdf' ) ){
            $token = str_replace( ".pdf", "", $_GET['claim'] );
            $certificate = S3Wrapper::getAgreementClaim( $token );
            if( ! $certificate){
                exit( "Invalid Request!" );
            }
            header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
            header("Last-Modified: " . gmdate("D,d M Y H:i:s") . " GMT");
            header("Cache-Control: no-cache, must-revalidate");
            header("Pragma: no-cache");
            header("Content-Type: Application/octet-stream");
            header("Content-disposition: attachment; filename=" . $token . ".pdf");
            echo $certificate;
            exit;
    }
}


function saveAgreement()
{
    $agreementModel = new Agreement();
    $agreementModel->addEntry( $_REQUEST );
    addMessage( 'Testing request has been sent successfully' );
    wp_redirect(get_permalink( $_REQUEST['responder_service']));
    exit;
}

function get_agreement_info_popup(){
    global $wpdb;
    $agreement_model = new Agreement();
    $agreement = $agreement_model->get_service_agreements( false, intval( $_REQUEST['id'] ) );
    $logs = AgreementLog::get_agreement_log( intval( $_REQUEST['id'] ) );
    ?>
    <div class="popup-box agreement-details-popup" id="agreement-details-popup" style="display: none; width: 500px">
       <div class="popup-box-header radius6 noradiusbottom">Agreement</div>
       <div class="popup-box-content">
           <div class="tabs-contr">
               <ul class="tab-nav">
                   <li class="active">
                       <a href="javascript: void(0)" rel="tab_general_information_<?php echo $agreement->id;?>">General Information</a>
                   </li>
                   <li>
                       <a href="javascript: void(0)" rel="tab_message_log_<?php echo $agreement->id;?>">Message Log</a>
                   </li>
               </ul>
               <div class="tab-content agreement-general-info" id="tab_general_information_<?php echo $agreement->id;?>" style="display: block;">
                   <dl class="common-info">
                       <dt>Status:</dt>
                       <dd><strong class="status-<?php echo strtolower( $agreement->status );?>"><?php echo $agreement->status;?></strong></dd>
    <?php if( $agreement->claim_date ):?>
        <dt>Date:</dt>
        <dd><?php echo formatDate( $agreement->claim_date );?></dd>
    <?php endif;?>
    <?php if( $agreement->scope ):?>
        <dt>Scope:</dt>
        <dd><?php echo str_replace( ';;', ', ', $agreement->scope );?></dd>
    <?php endif;?>
    </dl>
    <div class="info-per-item clearfix">
        <dl>
            <?php
            $profile = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM wp_community_profile_instances WHERE id = %d ", $agreement->requestor_profile ) );
            ?>
            <dt class="item-title"><?php echo get_post_meta($agreement->requester_service_id, 'service_roles', true) ;?></dt>
            <dt>Owner</dt>
            <dd><?php echo $agreement->requester_service->service_owner;?></dd>
            <dt>Service</dt>
            <dd><?php echo get_the_title( $agreement->requester_service->id );?></dd>
            <dt>Claimant</dt>
            <dd><?php if( in_array( $agreement->status, array('Verified', 'Claimed' ) ) )  echo $agreement->requestor_name;?></dd>
            <dt>Profile</dt>
            <dd><a href="<?php echo get_site_url()?>?td-action=<?php echo wp_create_nonce('view-profile-instance')?>&id=<?php echo $profile->id?>" class="view-profile-instance-link" ><?php echo $profile->profile_name;?></a></dd>
            <?php if( $agreement->requestor_audit_log_name ):?>
                <dt>Audit Log</dt>
                <dd><a href="<?php echo S3Wrapper::getAttachmentLink( $agreement->requester_token, $agreement->requestor_audit_log_name, 'agreements' );?>" target="_blank"><?php echo $agreement->requestor_audit_log_name;?></a></dd>
            <?php endif;?>
        </dl>
        <dl>
            <?php
            $resp_profile = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM wp_community_profile_instances WHERE id = %d ", $agreement->responder_profile ) );
            ?>
            <dt class="item-title"><?php echo get_post_meta($agreement->responder_service_id, 'service_roles', true) ;?></dt>
            <dt>Owner</dt>
            <dd><?php echo $agreement->responder_service->service_owner;?></dd>
            <dt>Service</dt>
            <dd><?php echo get_the_title( $agreement->responder_service->id );?></dd>
            <dt>Claimant</dt>
            <dd><?php if( in_array( $agreement->status, array('Verified', 'Claimed' ) ) ) echo $agreement->responder_name;?></dd>
            <dt>Profile</dt>
            <dd><a href="<?php echo get_site_url()?>?td-action=<?php echo wp_create_nonce('view-profile-instance')?>&id=<?php echo $resp_profile->id?>" class="view-profile-instance-link" ><?php echo $resp_profile->profile_name;?></a></dd>
            <?php if( $agreement->responder_audit_log_name ):?>
                <dt>Audit Log</dt>
                <dd><a href="<?php echo S3Wrapper::getAttachmentLink( $agreement->responder_token,  $agreement->responder_audit_log_name, 'agreements' );?>" target="_blank"><?php echo $agreement->responder_audit_log_name;?></a></dd>
            <?php endif;?>
        </dl>
    </div>
    </div>
    <div class="tab-content agreements-message-log" id="tab_message_log_<?php echo $agreement->id;?>" style="display: none;">
        <div class="agreements-message-log-list" style="height: 350px;">
            <ul>
                <?php if( $logs ):?>
                    <?php foreach( $logs AS $log ):?>
                        <?php
                            $css_class = $log->sent_by == '1' ? 'employer' : 'fund';
                            $profile_name = $log->sent_by == '1' ? get_post_meta($agreement->requester_service_id, 'service_roles', true) : get_post_meta($agreement->responder_service_id, 'service_roles', true);
                        ?>
                        <li class="<?php echo $css_class;?>" style="margin-bottom: 10px;">
                            <div class="author-name"><?php echo $profile_name.' ('.$log->state.')<br>'.$log->sent_by_user ;?></div>
                                <div class="message-content">
                                    <?php if( ! empty( $log->message ) ):?>
                                        <div class="message-body"><span class="message-box-arrow"></span><?php echo stripcslashes( $log->message );?></div>
                                    <?php endif;?>
                                    <div class="message-date"><?php echo formatDate( $log->date, 'Y-m-d H:i:s' );?></div>
                                </div>
                            <div class="clear"></div>
                            <div class="padding20"></div>
                        </li>
                    <?php endforeach;?>
                <?php endif;?>
            </ul>
        </div>
    </div>
    </div>
    </div>
    <div class="popup-box-footer radius6 noradiustop">
        <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Close</span></a>
        <div class="clear"></div>
    </div>
    <a class="close_btn"></a>
    </div>
    <script>
        jQuery( document).ready( function($){
            jQuery('.tabs-contr .tab-nav a').click(function(){
                if($(this).parent().hasClass('active'))
                    return false;
                var rid = jQuery(this).attr('rel');
                jQuery('.tabs-contr > .tab-content:visible').hide();
                jQuery('.tabs-contr .tab-nav .active').removeClass('active');
                $(this).parent().addClass('active');
                $('#' + rid).show();
                return false;
            })
            $('.tab-nav a').click(function(){
                $('.agreements-message-log-list').jScrollPane({
                    autoReinitialise: true
                });
            });
            jQuery('.view-profile-instance-link').cplightbox({
                type: 'ajax',
                onLoad: function()
                {
                    jQuery('.popup-box:visible .zcliplink').each(function(){
                        if(!jQuery(this).data('zclipId'))
                        {
                            jQuery('.popup-box:visible .zcliplink').zclip({
                                path: '/wp-content/themes/bp-child/js/ZeroClipboard.swf',
                                copy: function(){
                                    return jQuery('#' + jQuery(this).attr('data-id')).val();
                                },
                                afterCopy: function(){
                                    jQuery('.popup-box:visible .zclipsucces-msg').fadeIn();
                                    if(zclipTimer != null)
                                    {
                                        clearTimeout(zclipTimer);
                                    }
                                    zclipTimer = setTimeout(function(){
                                        jQuery('.popup-box:visible .zclipsucces-msg').fadeOut('fast');
                                    }, 2000);
                                }
                            })
                        }
                    })

                }
            })
        })
    </script>
<?php
    exit;
}

function create_agreement_pdf( $agreement_id, $for_another = false ){

        global $wpdb;
        require_once(THE_FUNCTION . '/tcpdf/cppdf.php');
        require_once(THE_FUNCTION . '/tcpdf/config/tcpdf_config.php');
        // Include 2D barcode class
        require_once(THE_FUNCTION . '/tcpdf/tcpdf_barcodes_2d.php');

        // Create new PDF document
        $pdf = new CPPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document meta information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('ComplianceTest');
        $pdf->SetTitle('ComplianceTest Certificate');
        $pdf->SetSubject('ComplianceTest Certificate');

        // Set margins
        $pdf->SetMargins(12, 29, 12, true);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 20);

        // Set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set certificate file
        $certificate = get_option('pdf_certificate');
        $private_key = get_option('pdf_private_key');

        // set additional information
        $info = array(
            'Location' => 'Australia',
            'ContactInfo' => 'http://www.compliancetest.net',
        );

        // set document signature
        $pdf->setSignature($certificate, $private_key, '', '', 2, $info);

        // Set font
        $pdf->SetFont('opensans', '', 13, '', true);

        // Set line-height
        $pdf->setCellHeightRatio(1);

        // Add a page
        // This method has several options, check the source code documentation for more information.
        $pdf->AddPage();

        $title = '<h1 style="color: #000; font-size: 48pt; font-weight: bold; line-height: 42pt; text-transform: uppercase;">CERTIFICATE</h1>';
        $description = '<p style="font-size: 13pt; line-height:16pt;"><br>This certificate confirms that the holder has completed an end-to-end interoperability test between the two parties defined below. Both parties have confirmed that the test was successful for the scope described.<br></p>';

        //Getting Claim Defaults
        $agreement = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM wp_e2e_agreement WHERE id = %d ", $agreement_id ) );

        if( $for_another ){
            $requester_service = new Service( $agreement->responder_service_id );
            $requester_service->load();

            $requester_test_suite = new TestSuite( $requester_service->service_suite_id );
            $requester_test_suite->load();

            $responder_service = new Service( $agreement->requester_service_id );
            $responder_service->load();
        } else{
            $requester_service = new Service( $agreement->requester_service_id );
            $requester_service->load();

            $requester_test_suite = new TestSuite( $requester_service->service_suite_id );
            $requester_test_suite->load();

            $responder_service = new Service( $agreement->responder_service_id );
            $responder_service->load();
        }


         $certificate_data_info = '
            <style>
                table.certificate-info th {
                    border-bottom: 0.2em solid #959595;
                    font-weight: normal;
                    margin-left: 2pt;
                    width: 35%;
                    font-size:13pt;
                    color:#262626;
                }
                table.certificate-info td {
                    border-bottom: 0.2em solid #959595;
                    font-weight: bold;
                    width:63%;
                    font-size:13pt;
                    color:#000;
                }
            </style>
            <br><br>
            <table cellspacing="5" cellpadding="5" class="certificate-info" width="100%">
                <tr>
                    <td colspan="5" style="text-align: right; border-bottom: none;">
                        <table style="font-size: small;">
                            <tr>
                                <td style="text-align: right; border-bottom: none;">Interoperability verified between:</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table style="font-size: small; font-weight: normal;">
                            <tr>
                                <td><b>'. $requester_service->service_owner.'</b></td>
                            </tr>
                             <tr>
                                <td>'. $requester_service->service_type.': '.$requester_service->service_id.'</td>
                             </tr>
                             <tr>
                                <td>Service: '. $requester_service->service_name.'</td>
                            </tr>
                        </table>
                    </td>
                    <td>
                     <table style="font-size: small;font-weight: normal;">
                             <tr>
                                <td><b>'. $responder_service->service_owner.'</b></td>
                            </tr>
                             <tr>
                                <td>'. $responder_service->service_type.': '.$responder_service->service_id.'</td>
                             </tr>
                             <tr>
                                <td>Service: '. $responder_service->service_name.'</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <th>Test Scope</th>
                    <td>' . str_replace( ';;', ', ', $agreement->scope ) . '</td>
                </tr>
                <tr>
                    <th>Issued To</th>
                    <td>' . $requester_service->service_owner . '</td>
                </tr>
                <tr>
                    <th>Service</th>
                    <td><a href="' . get_permalink( $requester_service->id ) .'">' . get_the_title( $requester_service->id ) . '</a></td>
                </tr>
                <tr>
                    <th>Version</th>
                    <td>' . $requester_service->service_version . '</td>
                </tr>
                <tr>
                    <th>Test Suite</th>
                    <td><a href="' . get_permalink( $requester_service->service_suite_id ) .'">' . get_the_title( $requester_service->service_suite_id ) . '</a></td>
                </tr>
                <tr>
                    <th>Specification Issuer</th>
                    <td>' . $requester_test_suite->issuer . '</td>
                </tr>
                <tr>
                    <th>Role(s)</th>
                    <td>' . implode( ', ', $requester_service->service_roles ) . '</td>
                </tr>
                <tr>
                    <th>Agreement ID</th>
                    <td>'. $agreement->claim_id .'</td>
                </tr>
                <tr>
                    <th>Date of Claim</th>
                    <td>' . formatDate( $agreement->claim_date, 'd F Y') . '</td>
                </tr>
            </table>
            ';


        $pdf->SetFont('opensansb', '', 13, '', true);

        $pdf->setHtmlLinksStyle(array(91, 117, 182));

        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $title, 0, 1, 0, false, 'C', true);


        $pdf->SetFont('opensans', '', 13, '', true);
        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $description, 0, 1, 0, false, '', false);

        // Print text using writeHTMLCell()
        $compliance_tested_image = K_PATH_IMAGES . "agreement_logo_large.png";
        $pdf->Image($compliance_tested_image, '', '', 120, '', 'PNG', '', 'N', false, 300, 'C', false, false, 1, false, false, false);

        // define active area for signature appearance
        $pdf->setSignatureAppearance(45, 72, 121, 29);

        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell('', '', '', '', $certificate_data_info, 0, 1, 0, true, '', true);

        // Styles for QR code
        $style = array('border' => false, 'padding' => 0, 'vpadding' => 10, 'fgcolor' => array(0, 0, 0), 'position' => 'C');

        $agreement->token = $for_another ? $agreement->responder_token : $agreement->requester_token;

        // QRCODE,H : QR-CODE Best error correction
        $pdf->write2DBarcode( get_site_url() . '/agreement/' . $agreement->token . ".pdf", 'QRCODE,H', '', '', 40, 40, $style, 'N');

        $link = '<div style="text-align:center;"><a href="'.S3Wrapper::getAgreementClaimLink( $agreement->token ).'" target="_blank" style="font-size:13pt; text-decoration:none;">' . S3Wrapper::getAgreementClaimLink( $agreement->token ) .'</a></div>';

        $pdf->writeHTMLCell(0, 0, '', '', $link, 0, 1, 0, true, '', true);
        // ---------------------------------------------------------


        // ---------------------------------------------------------
        $pdf->SetMargins(3.8, 24.5, 3.8, true);

        // Add a page
        // This method has several options, check the source code documentation for more information.
        $pdf->AddPage();


        $pdf->setTextShadow(array('enabled' => false));

        $services_table_styles = '<style>
                            .test-cases-table th {
                                background-color:#5a75b6;
                                color:#fff;
                                font-size:7pt;
                                vertical-align:middle;
                                line-height:18pt;
                                text-align:center;
                                font-weight:bold;
                            }
                            .test-cases-table th.test-scenario{
                                text-align:left;
                            }
                            .test-cases-table td {
                                font-size:7pt;
                                line-height:7pt;
                                color:#000;
                            }
                            .test-cases-table .even td{
                                background-color:#f3f4f5;
                            }
                            .test-cases-table .odd td{
                                background-color:#ececed;
                            }
                            .test-cases-table td a{
                                font-size:10pt;
                            }
                            .test-cases-table td.test-scenario{
                                background-color:#e2e2e2;
                            }

                            .issued, .test-outcome {
                                text-align:center;
                            }
                        </style>';
        $services_table = '<tr><th colspan="4">Audit Record</th></tr>
                           <tr>
                                <th class="test-scenario" style="width:25%; vertical-align:middle;">Entity</th>
                                <th class="test-case" style="width:25%;">Entity ID</th>
                                <th class="issued" style="width:25%;">Service Name</th>
                                <th class="test-intent" style="width:25%;">Audit Files</th>
                           </tr>';
    $agreement->requestor_audit_log_name = str_replace( array( ' ', ':', '-' ), '', $agreement->requestor_audit_log_name );
    $agreement->responder_audit_log_name = str_replace( array( ' ', ':', '-' ), '', $agreement->responder_audit_log_name );

    $req_files = $res_files = array();

    $req_links = $res_links = array();

    if( $for_another ){
        $temp_service = $requester_service;
        $requester_service = $responder_service;
        $responder_service = $temp_service;
    }
    $requester_file_location = getcwd() . '/wp-content/uploads/' . clean_file_name($requester_service->service_name . '-' . $agreement->requestor_audit_log_name);
    $requestor_file = fopen($requester_file_location, "w");
    fwrite($requestor_file, $agreement->requestor_audit_log);
    fclose($requestor_file);

    $responder_file_location = getcwd() . '/wp-content/uploads/' . clean_file_name($responder_service->service_name . '-' . $agreement->responder_audit_log_name);
    $responder_file = fopen($responder_file_location, "w");
    fwrite($responder_file, $agreement->responder_audit_log);
    fclose($responder_file);

    if( strpos( $agreement->requestor_audit_log_name, '.zip' ) !== false ){
        $req_files = save_sip_files( $requester_file_location, $requester_service->service_name ) ;
    } else{
        $req_files[] = array( 'location' => $requester_file_location, 'name' => clean_file_name( $requester_service->service_name.'-'.$agreement->requestor_audit_log_name ) );
    }

    if( strpos( $agreement->responder_audit_log_name, '.zip' ) !== false ){
        $res_files = save_sip_files( $responder_file_location, $responder_service->service_name ) ;
    } else{
        $res_files[] = array( 'location' => $responder_file_location, 'name' => clean_file_name( $responder_service->service_name.'-'.$agreement->responder_audit_log_name ) );
    }
    foreach( $res_files AS $res_file ){
        $pdf->Annotation(0, 1, 0, 0, 1, array('Subtype' => 'FileAttachment', 'Name' => $res_file['name'], 'FS' => $res_file['location'] ) );
        $pdf->Bookmark( $res_file['name'], 0, 0, 1, 'B', array(128, 0, 255), 0, '*' . $res_file['name'] );
        $res_links[] = '"'.$res_file['name'].'"';
    }
    foreach( $req_files AS $req_file ){
        $pdf->Annotation(0, 1, 0, 0, 1, array('Subtype' => 'FileAttachment', 'Name' => $req_file['name'], 'FS' => $req_file['location'] ) );
        $pdf->Bookmark( $req_file['name'], 0, 0, 1, 'B', array(128, 0, 255), 0, '*' . $req_file['name'] );
        $req_links[] = '"'.$req_file['name'].'"';
    }
    $services_table .= '<tr class="odd">
                                <td class="test-scenario" style="width:25%; font-weight: bold;">'. $requester_service->service_owner.'</td>
                                <td class="test-case" style="width:25%;">'. $requester_service->service_type.':'.$requester_service->service_id.'</td>
                                <td class="issued" style="width:25%;">'. $requester_service->service_name.'</td>
                                <td class="test-intent" style="width:25%;">
                                     Click '.implode( ' OR <br>', $req_links ).'  bookmark to see attachment (offline) <br> OR
                                    <a href="' . S3Wrapper::getAttachmentLink( $agreement->requester_token, $agreement->requestor_audit_log_name, 'agreements' ).'">' . S3Wrapper::getAttachmentLink( $agreement->requester_token, $agreement->requestor_audit_log_name, 'agreements' ) .'</a> link to download attachment on our website
                                </td>
                           </tr>'.
                            '<tr class="even">
                                <td class="test-scenario" style="width:25%; font-weight: bold;">'. $responder_service->service_owner.'</td>
                                <td class="test-case" style="width:25%;">'. $responder_service->service_type.':'.$responder_service->service_id.'</td>
                                <td class="issued" style="width:25%;">'. $responder_service->service_name.'</td>
                                <td class="test-intent" style="width:25%;">
                                     Click '.implode( ' OR <br>', $res_links ).' bookmark to see attachment (offline) <br> OR
                                     <a href="' . S3Wrapper::getAttachmentLink( $agreement->responder_token, $agreement->responder_audit_log_name, 'agreements' ).'">' . S3Wrapper::getAttachmentLink( $agreement->responder_token, $agreement->responder_audit_log_name, 'agreements' ) .'</a> link to download attachment on our website
                                </td>
                           </tr>';

        $pdf->SetFont('opensans', '', 13, '', true);

        $general_cases_table_html =  $services_table_styles. '<table cellspacing="1" cellpadding="3" class="test-cases-table" width="100%">' . $services_table . '</table>';

        $pdf->writeHTMLCell(0, 0, '', '', $general_cases_table_html, 0, 1, 0, true, '', true);

        $pdfString = $pdf->Output('ComplianceTest-certificate.pdf', 'S');

        foreach( $req_files AS $req_file ){
            @unlink( $req_file['location'] );
        }
        foreach( $res_files AS $res_file ){
            @unlink( $res_file['location'] );
        }
        return $pdfString;
}

function save_sip_files( $zip_name, $service_name  ){
    $zip = zip_open( $zip_name );
    $files = array();
    if ($zip)
    {
        while ($zip_entry = zip_read($zip))
        {
            $file_name = clean_file_name( $service_name.'-'.zip_entry_name( $zip_entry ) );
            if( strpos( $file_name, '/' ) !== false ){
                continue;
            }
            if (zip_entry_open($zip, $zip_entry))
            {
                $contents = zip_entry_read( $zip_entry, zip_entry_filesize( $zip_entry ) );
                $file_location = getcwd() . '/wp-content/uploads/' . $file_name;
                $file = fopen( $file_location, "w" );
                fwrite( $file , $contents );
                fclose( $file );
                zip_entry_close($zip_entry);
                $files[] = array( 'location' => $file_location, 'name' => $file_name );
            }
        }
        zip_close($zip);
    }
    @unlink( $zip_name );
    return $files;
}

function clean_file_name( $file_name ){
    $lastDot = strrpos( $file_name, "." );
    $file_name = str_replace(".", "_", substr($file_name, 0, $lastDot)) . substr($file_name, $lastDot);
    return str_replace( array( ' ', ':', ';' ), '_', $file_name );
}