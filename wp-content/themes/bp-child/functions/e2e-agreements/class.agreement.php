<?php

class Agreement
{
    public $id = null;

    public $requestor_org = '';

    public $requestor_user_id = '';

    public $responder_org = '';

    public $responder_user = '';

    public $requester_service_id = '';

    public $responder_service_id = '';

    public $requestor_profile = '';

    public $responder_profile = '';

    public $message_log = '';

    public $status = '';

    public $scope = '';

    public $requestor_audit_log = '';

    public $responder_audit_log = '';

    public $claim_date = '';

    private $_table = 'wp_e2e_agreement';

    public function __construct( $id = null )
    {        
        if($id !== null)   
            $this->id = $id;

    }

    public function addEntry( $data ){
        global $wpdb;
        $wpdb->insert( $this->_table,
            array(
                'str_id'                 => $data['agreement_id'],
                'requester_service_id'   => $data['requester_service'],
                'requestor_name'         => get_user_meta( get_current_user_id(), 'first_name', true ).' '.get_user_meta( get_current_user_id(), 'last_name', true ),
                'responder_service_id'   => $data['responder_service'],
                'requestor_profile'      => @$data['requester_profiles'],
                'status'                 => 'Pending',
                'requestor_message'      => $data['agreement_message'],
                'requestor_message_date' => gmmktime()
            ),
            array( '%s', '%d', '%s', '%d', '%d', '%s', '%s' )
        );
        $insert_id = $wpdb->insert_id;
        AgreementLog::add_entry( array(
            'agreement_id' => $insert_id,
            'sent_by'      => 1,
            'message'      => $data['agreement_message'],
            'state'        => 'Request'

        ));

        $service = new Service( $data['responder_service'] );
        $service->load();
        //send notifications to sender, receiver and admin
        Agreement::send_agreement_email( 'request', get_current_user_id(), $service->service_user_id, array( 'text' => $data['agreement_message'], 'sender_service_id' => $data['requester_service'], 'receiver_service_id' => $data['responder_service'] ) );

        if( $wpdb->last_error ){
            addMessage( $wpdb->last_error, 'error' );
            wp_redirect( $_REQUEST['_wp_http_referer'] );
            exit;
        }
        $cloud_search = new CloudSearch();
        $cloud_search->cloud_search_update_agreement( $insert_id );
        return $insert_id;
    }

    public function get_service_agreements( $service_id, $agreement_id = false ){
        global $wpdb;
        if( $agreement_id ){
            $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM wp_e2e_agreement WHERE id = %d ", $agreement_id ) );
        } else {
            $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM wp_e2e_agreement WHERE responder_service_id = %d OR requester_service_id = %d ORDER BY str_id ASC", $service_id, $service_id));
        }
        if( ! $results ){
            return false;
        }
        $agreements = array();
        foreach( $results AS $result ){
            $requester_service = new Service( $result->requester_service_id );
            $requester_service->load();
            $responder_service = new Service( $result->responder_service_id );
            $responder_service->load();
            $result->requester_service = $requester_service;
            $result->responder_service = $responder_service;
            //used to display correct data on the my agreements tab
            $result->entry_status = $result->requester_service_id == $service_id ? 'Requester' : 'Responder';
            if( $agreement_id ){
                return $result;
            }
            array_push( $agreements, $result );
        }
        return $agreements;
    }

    public static function has_access( $action, $service_id = false, $agreement_id = false ){
        global $wpdb;
        $can = true;
        $error_message = 'Permission denied!';
        switch( $action ){
            case 'edit-agreement':
                $agreement = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM wp_e2e_agreement WHERE id = %d ", $agreement_id ) );
                if( $agreement ){
                    $requester_service = new Service( $agreement->requester_service_id );
                    $requester_service->load();
                    $responder_service = new Service( $agreement->responder_service_id );
                    $responder_service->load();

                    if( ! ( ct_get_user_organisation( $requester_service->service_user_id ) == ct_get_user_organisation( get_current_user_id() ) || ct_get_user_organisation( $responder_service->service_user_id ) == ct_get_user_organisation( get_current_user_id() ) ) ){
                        $error_message = 'You don\'t have permissions to perform this action';
                        $can = false;
                    }
                }

                break;
            case 'delete-agreement':

                break;
        }
        if( ! $can ){
            addMessage( $error_message, 'error' );
            wp_redirect( '/' );
            exit();
        }
    }

    public static function send_agreement_email( $template, $sender_id, $receiver_id, $data = array() ){

        global $wpdb;

        $templates = array(
            'request' => array(
                'sender'   => 'e2e_request_sender',
                'receiver' => 'e2e_request_receiver',
                'admin'    => 'e2e_request_admin',
            ),
            'accept' => array(
                'sender'   => 'e2e_request_accepted_sender',
                'receiver' => 'e2e_request_accepted_receiver',
                'admin'    => 'e2e_request_accepted_admin',
            ),
            'reject' => array(
                'sender'   => 'e2e_request_rejected_sender',
                'receiver' => 'e2e_request_rejected_receiver',
                'admin'    => 'e2e_request_rejected_admin',
            ),
            'claim' => array(
                'sender'   => 'e2e_claim_made_sender',
                'receiver' => 'e2e_claim_made_receiver',
                'admin'    => 'e2e_claim_made_admin',
            ),
            'cancel' => array(
                'sender'   => 'e2e_request_rejected_sender',
                'receiver' => 'e2e_request_rejected_receiver',
                'admin'    => 'e2e_request_rejected_admin',
            ),
            'confirm_claim' => array(
                'sender'   => 'e2e_claim_confirmed_sender',
                'receiver' => 'e2e_claim_confirmed_receiver',
                'admin'    => 'e2e_claim_confirmed_admin',
            ),
            'reject_claim' => array(
                'sender'   => 'e2e_claim_failed_sender',
                'receiver' => 'e2e_claim_failed_receiver',
                'admin'    => 'e2e_claim_failed_admin',
            ),
        );

        $sender_service = new Service( $data['sender_service_id'] );
        $sender_service->load();

        $receiver_service = new Service( $data['receiver_service_id'] );
        $receiver_service->load();

        $email_data = array(
            '[env]'              => strpos( get_home_url(), 'test.compliancetest' ) === false ? 'production' : 'test',
            '[sender_owner]'     => $sender_service->service_owner,
            '[sender_service]'   => $sender_service->service_name,
            '[sender_name]'      => cp_get_user_fullname( $sender_id ),
            '[receiver_owner]'   => $receiver_service->service_owner,
            '[receiver_service]' => $receiver_service->service_name,
//            '[receiver_name]'    => cp_get_user_fullname( $receiver_service->service_user_id ),
            '[agreement_url]'    => home_url( '/agreement/'),
            '[message_text]'     => stripslashes( $data['text'] )
        );
        //send email to requesters
        //get sender organisation
        $organisation = ct_get_user_organisation( $sender_id );
        //users with MAKE_AGREEMENT permission
        $sender_users = $wpdb->get_results( $wpdb->prepare("SELECT * FROM wp_users_privileges WHERE organisation_id = %d AND privilege_id = 4 ", $organisation->id ) );
        if( $sender_users ){
            foreach( $sender_users AS $u ){
                $email_data['[receiver_name]'] = cp_get_user_fullname( $u->user_id );
                cp_send_email( array('name' => $email_data['[receiver_name]'], 'email' => get_userdata( $u->user_id )->data->user_email ), $templates[$template]['sender'], $email_data );
            }

        } else{
            //get organisation admin and send him email
            $admin_user = ct_get_organisation_admin( $organisation->id );
            $email_data['[receiver_name]'] = cp_get_user_fullname( $admin_user->user_id );
            cp_send_email( array('name' => $email_data['[receiver_name]'], 'email' => get_userdata( $admin_user->user_id )->data->user_email ), $templates[$template]['sender'], $email_data );
        }

        //send email to requesters

        $receiver_organisation = ct_get_user_organisation( $receiver_id );
        //users with MAKE_AGREEMENT permission
        $receiver_users = $wpdb->get_results( $wpdb->prepare("SELECT * FROM wp_users_privileges WHERE organisation_id = %d AND privilege_id = 4 ", $receiver_organisation->id ) );
        if( $receiver_users ){
            foreach( $receiver_users AS $u ){
                $email_data['[receiver_name]'] = cp_get_user_fullname( $u->user_id );
                cp_send_email( array('name' => $email_data['[receiver_name]'], 'email' => get_userdata( $u->user_id )->data->user_email ), $templates[$template]['receiver'], $email_data );
            }

        } else{
            //get organisation admin and send him email
            $admin_user = ct_get_organisation_admin( $receiver_organisation->id );
            $email_data['[receiver_name]'] = cp_get_user_fullname( $admin_user->user_id );
            cp_send_email( array('name' => $email_data['[receiver_name]'], 'email' => get_userdata( $admin_user->user_id )->data->user_email ), $templates[$template]['receiver'], $email_data );
        }

        //send email to admin
        cp_send_email_to_admin( $templates[$template]['admin'], $email_data );

    }
}