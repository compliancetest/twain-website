<?php

add_action('template_redirect', 'ct_agreement_certification_view');
//Display Agreement Certificate
function ct_agreement_certification_view()
{
    if(get_query_var('pagename') == 'agreement-certificate')
    {
        global $wpdb;
        $token = str_replace( ".pdf", "", get_query_var('claim') );
        $certificate = $wpdb->get_var( $wpdb->prepare( "SELECT certificate FROM wp_e2e_agreement WHERE token=%s ", $token ) );
        header("Content-type: application/pdf");
        echo $certificate;
        exit;
    }
}

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
        $service = new Service( $wpdb->get_var( $wpdb->prepare( "SELECT requester_service_id FROM wp_e2e_agreement WHERE id = %d", $agreement_id ) ) );
        $service->load();
        Agreement::has_access( 'edit-agreement', false, $agreement_id );
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
        //send notifications to sender, receiver and admin
        $sender = get_userdata( get_current_user_id() );
        $receiver = get_userdata( $service->service_user_id );
        $email_data = array(
            '[sender_name]'   => $sender->display_name,
            '[receiver_name]' => $receiver->display_name,
            '[agreement_url]' => home_url( '/agreements/'),
            '[test_suite]'    => get_the_title( $service->service_suite_id )
        );
        //send email to requester
        cp_send_email( array('name' => $sender->display_name, 'email' => $sender->user_email), 'e2e_request_accepted_sender', $email_data );

        //send email to receiver
        cp_send_email( array('name' => $receiver->display_name, 'email' => $receiver->user_email), 'e2e_request_accepted_receiver', $email_data );

        //send email to admin
        cp_send_email_to_admin( 'e2e_request_accepted_admin', $email_data );
        $cloud_search = new CloudSearch();
        $cloud_search->cloud_search_update_agreement( $agreement_id );
        addMessage('Success');
        wp_redirect('/agreements/');
        exit;
    } else if( wp_verify_nonce($action, 'cancel-agreement' ) ) {
        $agreement_id = intval($_REQUEST['id']);
        $service = new Service( $wpdb->get_var( $wpdb->prepare( "SELECT requester_service_id FROM wp_e2e_agreement WHERE id = %d", $agreement_id ) ) );
        $service->load();
        Agreement::has_access( 'edit-agreement', false, $agreement_id );

        $wpdb->query( $wpdb->prepare( "DELETE FROM wp_e2e_agreement WHERE id = %d ", $agreement_id ) );

        $sender = get_userdata( get_current_user_id() );
        $receiver = get_userdata( $service->service_user_id );
        $email_data = array(
            '[sender_name]'   => $sender->display_name,
            '[receiver_name]' => $receiver->display_name,
            '[agreement_url]' => home_url( '/agreements/'),
            '[message_text]'  => $_REQUEST['deny-reason-field']
        );
        //send email to requester
        cp_send_email( array('name' => $sender->display_name, 'email' => $sender->user_email), 'e2e_request_rejected_sender', $email_data );

        //send email to receiver
        cp_send_email( array('name' => $receiver->display_name, 'email' => $receiver->user_email), 'e2e_request_rejected_receiver', $email_data );

        //send email to admin
        cp_send_email_to_admin( 'e2e_request_rejected_admin', $email_data );

        $cloud_search = new CloudSearch();
        $cloud_search->cloud_search_update_agreement( $agreement_id );

        addMessage('Success');
        wp_redirect('/agreements/');
        exit;
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
                $file_field = 'requestor_audit_log';
                $name_field = 'requestor_audit_log_name';
                $type_field = 'requestor_audit_log_type';
                $service_id = $wpdb->get_var( $wpdb->prepare( "SELECT responder_service_id FROM wp_e2e_agreement WHERE id = %d", $agreement_id ) );
            } else{
                $file_field = 'responder_audit_log';
                $name_field = 'responder_audit_log_name';
                $type_field = 'responder_audit_log_type';
                $service_id = $wpdb->get_var( $wpdb->prepare( "SELECT requester_service_id FROM wp_e2e_agreement WHERE id = %d", $agreement_id ) ) ;
            }
            $service = new Service($service_id);
            $service->load();

            $wpdb->update('wp_e2e_agreement',
                array(
                    'status'     => 'Claimed',
                    'scope'      => @implode( ';;', @$_REQUEST['scope'] ),
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

            $service->load();
            $sender = get_userdata( get_current_user_id() );
            $receiver = get_userdata( $service->service_user_id );
            $email_data = array(
                '[sender_name]'   => $sender->display_name,
                '[receiver_name]' => $receiver->display_name,
                '[agreement_url]' => home_url( '/agreements/')
            );
            //send email to requester
            cp_send_email( array('name' => $sender->display_name, 'email' => $sender->user_email), 'e2e_claim_made_sender', $email_data );

            //send email to receiver
            cp_send_email( array('name' => $receiver->display_name, 'email' => $receiver->user_email), 'e2e_claim_made_receiver', $email_data );

            //send email to admin
            cp_send_email_to_admin( 'e2e_claim_made_admin', $email_data );
        }

        $cloud_search = new CloudSearch();
        $cloud_search->cloud_search_update_agreement( $agreement_id );

        addMessage( 'Success' );
        wp_redirect('/agreements/');
        exit;
    } else if( wp_verify_nonce($action, 'confirm-agreement' ) ){
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
                $file_field = 'requestor_audit_log';
                $name_field = 'requestor_audit_log_name';
                $type_field = 'requestor_audit_log_type';
            } else{
                $file_field = 'responder_audit_log';
                $name_field = 'responder_audit_log_name';
                $type_field = 'responder_audit_log_type';
            }
            //generate PDF

            $service = new Service( $wpdb->get_var( $wpdb->prepare( "SELECT requester_service_id FROM wp_e2e_agreement WHERE id = %d", $agreement_id ) ) );
            $service->load();

            $wpdb->update('wp_e2e_agreement',
                array(
                    'status'      => 'Verified',
                    $file_field   => $content,
                    $name_field   => $fileName,
                    $type_field   => $fileType,
                    'claim_date'  => gmmktime(),
                    'claim_id'    => getClaimID( null, $service->service_suite_id ),
                    'token'       => createClaimToken()
                ),
                array(
                    'id' => $agreement_id
                ),
                array( '%s', '%s', '%s', '%s', '%d', '%s', '%s' ),
                array( '%d' )
            );

            $pdfString = create_agreement_pdf( $agreement_id );

            $wpdb->update('wp_e2e_agreement',
                array(
                    'certificate' => $pdfString
                ),
                array(
                    'id' => $agreement_id
                ),
                array( '%s' ),
                array( '%d' )
            );

            if( get_current_user_id() == $service->service_user_id ){
                $service = new Service( $wpdb->get_var( $wpdb->prepare( "SELECT responder_service_id FROM wp_e2e_agreement WHERE id = %d", $agreement_id ) ) );
                $service->load();
            }
            $sender = get_userdata( get_current_user_id() );
            $receiver = get_userdata( $service->service_user_id );
            $email_data = array(
                '[sender_name]'   => $sender->display_name,
                '[receiver_name]' => $receiver->display_name,
                '[agreement_url]' => home_url( '/agreements/')
            );
            //send email to requester
            cp_send_email( array('name' => $sender->display_name, 'email' => $sender->user_email), 'e2e_claim_confirmed_sender', $email_data );

            //send email to receiver
            cp_send_email( array('name' => $receiver->display_name, 'email' => $receiver->user_email), 'e2e_claim_confirmed_receiver', $email_data );

            //send email to admin
            cp_send_email_to_admin( 'e2e_claim_confirmed_admin', $email_data );
        }

        $cloud_search = new CloudSearch();
        $cloud_search->cloud_search_update_agreement( $agreement_id );

        addMessage( 'Success' );
        wp_redirect('/agreements/');
        exit;
    } else if( wp_verify_nonce($action, 'reject-pending-agreement' ) ){
        $agreement_id = intval($_REQUEST['agreement_id']);
        Agreement::has_access( 'edit-agreement', false, $agreement_id );
        
        $service = new Service( $wpdb->get_var( $wpdb->prepare( "SELECT requester_service_id FROM wp_e2e_agreement WHERE id = %d", $agreement_id ) ) );
        $service->load();
        
        $wpdb->query( $wpdb->prepare( "DELETE FROM wp_e2e_agreement WHERE id = %d ", $agreement_id ) );

        
        $sender = get_userdata( get_current_user_id() );
        $receiver = get_userdata( $service->service_user_id );
        $email_data = array(
            '[sender_name]'   => $sender->display_name,
            '[receiver_name]' => $receiver->display_name,
            '[agreement_url]' => home_url( '/agreements/'),
            '[message_text]'  => $_REQUEST['deny-reason-field']
        );
        //send email to requester
        cp_send_email( array('name' => $sender->display_name, 'email' => $sender->user_email), 'e2e_request_rejected_sender', $email_data );

        //send email to receiver
        cp_send_email( array('name' => $receiver->display_name, 'email' => $receiver->user_email), 'e2e_request_rejected_receiver', $email_data );

        //send email to admin
        cp_send_email_to_admin( 'e2e_request_rejected_admin', $email_data );

        $cloud_search = new CloudSearch();
        $cloud_search->cloud_search_update_agreement( $agreement_id );


        addMessage('Success');
        wp_redirect('/agreements/');
        exit;
    }else if( wp_verify_nonce($action, 'reject-claimed-agreement' ) ){
        $agreement_id = intval($_REQUEST['agreement_id']);
        Agreement::has_access( 'edit-agreement', false, $agreement_id );
        $wpdb->query( $wpdb->prepare( "DELETE FROM wp_e2e_agreement WHERE id = %d ", $agreement_id ) );

        $cloud_search = new CloudSearch();
        $cloud_search->cloud_search_delete_item( $agreement_id, 'agreement' );

        addMessage('Success');
        wp_redirect('/agreements/');
        exit;
    }else if( wp_verify_nonce($action, 'reject-failed-agreement' ) ){
        $agreement_id = intval($_REQUEST['agreement_id']);
        Agreement::has_access( 'edit-agreement', false, $agreement_id );
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

        $service = new Service( $wpdb->get_var( $wpdb->prepare( "SELECT requester_service_id FROM wp_e2e_agreement WHERE id = %d", $agreement_id ) ) );
        $service->load();
        if( get_current_user_id() == $service->service_user_id ){
            $service = new Service( $wpdb->get_var( $wpdb->prepare( "SELECT responder_service_id FROM wp_e2e_agreement WHERE id = %d", $agreement_id ) ) );
            $service->load();
        }
        $sender = get_userdata( get_current_user_id() );
        $receiver = get_userdata( $service->service_user_id );
        $email_data = array(
            '[sender_name]'   => $sender->display_name,
            '[receiver_name]' => $receiver->display_name,
            '[agreement_url]' => home_url( '/agreements/'),
            '[message_text]'  => $_REQUEST['deny-reason-field']
        );
        //send email to requester
        cp_send_email( array('name' => $sender->display_name, 'email' => $sender->user_email), 'e2e_claim_failed_sender', $email_data );

        //send email to receiver
        cp_send_email( array('name' => $receiver->display_name, 'email' => $receiver->user_email), 'e2e_claim_failed_receiver', $email_data );

        //send email to admin
        cp_send_email_to_admin( 'e2e_claim_failed_admin', $email_data );

        $cloud_search = new CloudSearch();
        $cloud_search->cloud_search_update_agreement( $agreement_id );

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
        $query = $wpdb->prepare("SELECT count(*) FROM {$wpdb->prefix}e2e_agreement WHERE str_id=%s", $new_id);
        $seq = $wpdb->get_var($query);
        
        if( $seq > 0 )
            $new_id .= "." . str_pad($seq, 2, 0, STR_PAD_LEFT);
        echo $new_id;
        exit;
    } else if( wp_verify_nonce($action, 'get-agreement-pdf' ) ){
            global $wpdb;
            $token = str_replace( ".pdf", "", $_GET['claim'] );
            $certificate = $wpdb->get_var( $wpdb->prepare("SELECT certificate FROM wp_e2e_agreement WHERE token= %s ", $token ) );
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
            $pJSON = json_decode(base64_decode($profile->content));
            ?>
            <dt class="item-title"><?php echo get_post_meta($agreement->requester_service_id, 'service_roles', true) ;?></dt>
            <dt>Owner</dt>
            <dd><?php echo $agreement->requester_service->service_owner;?></dd>
            <dt>User</dt>
            <?php $contacts = get_service_contacts($agreement->requester_service_id) ;?>
            <dd>
                <?php foreach($contacts as $urow): ?>
                <?php echo $urow->display_name; ?><br />
                <?php endforeach; ?>
            </dd>
            <dt>Service</dt>
            <dd><?php echo get_the_title( $agreement->requester_service->id );?></dd>
            <dt>Profile</dt>
            <dd><a href="<?php echo get_site_url()?>?td-action=<?php echo wp_create_nonce('view-profile-instance')?>&id=<?php echo $profile->id?>" class="view-profile-instance-link" ><?php echo $profile->profile_name;?></a></dd>
            <?php if( $agreement->requestor_audit_log_name ):?>
                <dt>Audit Log</dt>
                <dd><a href="?_psnonce=<?php echo wp_create_nonce('get-agreement-file');?>&type=1&agreement_id=<?php echo $agreement->id;?>"><?php echo $agreement->requestor_audit_log_name;?></a></dd>
            <?php endif;?>
        </dl>
        <dl>
            <?php
            $resp_profile = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM wp_community_profile_instances WHERE id = %d ", $agreement->responder_profile ) );
            $resp_pJSON = json_decode(base64_decode($resp_profile->content));
            ?>
            <dt class="item-title"><?php echo get_post_meta($agreement->responder_service_id, 'service_roles', true) ;?></dt>
            <dt>Owner</dt>
            <dd><?php echo $agreement->responder_service->service_owner;?></dd>
            <dt>User</dt>
            <dd><?php echo get_userdata( $agreement->responder_service->service_user_id )->display_name;?></dd>
            <dt>Service</dt>
            <dd><?php echo get_the_title( $agreement->responder_service->id );?></dd>
            <dt>Profile</dt>
            <dd><a href="<?php echo get_site_url()?>?td-action=<?php echo wp_create_nonce('view-profile-instance')?>&id=<?php echo $resp_profile->id?>" class="view-profile-instance-link" ><?php echo $resp_profile->profile_name;?></a></dd>
            <?php if( $agreement->responder_audit_log_name ):?>
                <dt>Audit Log</dt>
                <dd><a href="?_psnonce=<?php echo wp_create_nonce('get-agreement-file');?>&type=2&agreement_id=<?php echo $agreement->id;?>"><?php echo $agreement->responder_audit_log_name;?></a></dd>
            <?php endif;?>
        </dl>
    </div>
    </div>
    <div class="tab-content agreements-message-log" id="tab_message_log_<?php echo $agreement->id;?>" style="display: none;">
        <div class="agreements-message-log-list" style="height: 350px;">
            <ul>
                <li class="employer">
                    <div class="author-name"><?php echo get_post_meta($agreement->requester_service_id, 'service_roles', true) ;?></div>
                    <?php if( $agreement->requestor_message ):?>
                        <div class="message-content">
                            <div class="message-body"><span class="message-box-arrow"></span><?php echo stripcslashes( $agreement->requestor_message );?></div>
                            <div class="message-date"><?php echo formatDate( $agreement->requestor_message_date );?></div>
                        </div>
                    <?php endif;?>
                </li>
                <li class="fund">
                    <div class="author-name"><?php echo get_post_meta($agreement->responder_service_id, 'service_roles', true) ;?></div>
                    <?php if( $agreement->responder_message ):?>
                        <div class="message-content">
                            <div class="message-body"><span class="message-box-arrow"></span><?php echo stripcslashes( $agreement->responder_message );?></div>
                            <div class="message-date"><?php echo formatDate( $agreement->responder_message_date );?></div>
                        </div>
                    <?php endif;?>
                </li>
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

function create_agreement_pdf( $agreement_id ){

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
        $description = '<p style="font-size: 13pt; line-height:16pt;"><br>This certificate confirms that the holder has completed and end-to-end interoperability test between the two parties defined below. Both parties have confirmed that the test was successful for the scope described.<br></p>';

        //Getting Claim Defaults
        $agreement = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM wp_e2e_agreement WHERE id = %d ", $agreement_id ) );

        $requester_service = new Service( $agreement->requester_service_id );
        $requester_service->load();

        $requester_test_suite = new TestSuite( $requester_service->service_suite_id );
        $requester_test_suite->load();

        $responder_service = new Service( $agreement->responder_service_id );
        $responder_service->load();

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
                    <td>' . $responder_service->service_owner . '</td>
                </tr>
                <tr>
                    <th>Service</th>
                    <td><a href="' . get_permalink( $responder_service->id ) .'">' . get_the_title( $responder_service->id ) . '</a></td>
                </tr>
                <tr>
                    <th>Version</th>
                    <td>' . $responder_service->service_version . '</td>
                </tr>
                <tr>
                    <th>Test Suite</th>
                    <td><a href="' . get_permalink( $responder_service->service_suite_id ) .'">' . get_the_title( $responder_service->service_suite_id ) . '</a></td>
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

        // QRCODE,H : QR-CODE Best error correction
        $pdf->write2DBarcode( get_site_url() . '/agreement/' . $agreement->token . ".pdf", 'QRCODE,H', '', '', 40, 40, $style, 'N');

        $link = '<div style="text-align:center;"><a href="' . get_site_url() . '/agreement/' . $agreement->token . ".pdf" . '" target="_blank" style="font-size:13pt; text-decoration:none;">' . get_site_url() . '/agreement/' . $agreement->token . '.pdf</a></div>';

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

    $requester_file_location = getcwd() . '/wp-content/uploads/'.$agreement->requestor_audit_log_name;
    $requestor_file = fopen( $requester_file_location, "w");
    fwrite( $requestor_file , $agreement->requestor_audit_log );
    fclose( $requestor_file );

    $responder_file_location = getcwd() . '/wp-content/uploads/'.$agreement->responder_audit_log_name;
    $responder_file = fopen( $responder_file_location, "w");
    fwrite( $responder_file , $agreement->responder_audit_log );
    fclose( $responder_file );

    $pdf->Annotation(0, 1, 0, 0, 1, array('Subtype' => 'FileAttachment', 'Name' => $agreement->requestor_audit_log_name, 'FS' => $requester_file_location ) );
    $pdf->Bookmark( $agreement->requestor_audit_log_name, 0, 0, 1, 'B', array(128, 0, 255), 0, '*' . $agreement->requestor_audit_log_name );

    $pdf->Annotation(0, 1, 0, 0, 1, array('Subtype' => 'FileAttachment', 'Name' => $agreement->responder_audit_log_name, 'FS' => $responder_file_location ) );
    $pdf->Bookmark( $agreement->responder_audit_log_name, 0, 0, 1, 'B', array(128, 0, 255), 0, '*' . $agreement->responder_audit_log_name );

    $services_table .= '<tr class="odd">
                                <td class="test-scenario" style="width:25%; font-weight: bold;">'. $requester_service->service_owner.'</td>
                                <td class="test-case" style="width:25%;">'. $requester_service->service_type.':'.$requester_service->service_id.'</td>
                                <td class="issued" style="width:25%;">'. $requester_service->service_name.'</td>
                                <td class="test-intent" style="width:25%;">
                                Click "' . $agreement->requestor_audit_log_name . '" bookmark to see attachment (offline) <br> OR
                                    <a href="' . get_site_url() . '/agreement/' . $agreement->token . '.pdf">' . get_site_url() . '/agreement/' . $agreement->token . '.pdf</a> link to download attachment on our website
                                </td>
                           </tr>'.
                            '<tr class="even">
                                <td class="test-scenario" style="width:25%; font-weight: bold;">'. $responder_service->service_owner.'</td>
                                <td class="test-case" style="width:25%;">'. $responder_service->service_type.':'.$responder_service->service_id.'</td>
                                <td class="issued" style="width:25%;">'. $responder_service->service_name.'</td>
                                <td class="test-intent" style="width:25%;">
                                     Click "' . $agreement->responder_audit_log_name . '" bookmark to see attachment (offline) <br> OR
                                    <a href="' . get_site_url() . '/agreement/' . $agreement->token . '.pdf">' . get_site_url() . '/agreement/' . $agreement->token . '.pdf</a> link to download attachment on our website
                                </td>
                           </tr>';


        $pdf->SetFont('opensans', '', 13, '', true);

        $general_cases_table_html =  $services_table_styles. '<table cellspacing="1" cellpadding="3" class="test-cases-table" width="100%">' . $services_table . '</table>';

        $pdf->writeHTMLCell(0, 0, '', '', $general_cases_table_html, 0, 1, 0, true, '', true);

        $pdfString = $pdf->Output('ComplianceTest-certificate.pdf', 'S');

        @unlink( $requester_file_location );
        @unlink( $responder_file_location );

        return $pdfString;
}