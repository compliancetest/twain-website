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
        Agreement::has_access( 'add-agreement', $data['responder_service'] );
        $insert_id = $wpdb->insert( $this->_table,
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
        $wpdb->insert( 'wp_e2e_agreement_log',
            array(
                'agreement_id'    => $wpdb->insert_id,
                'sent_by'         => 1,
                'sent_by_user_id' => get_current_user_id(),
                'message'         => $data['agreement_message'],
                'date'            => gmmktime()
            ),
            array( '%d', '%d', '%d', '%s', '%d' )
        );
        $service = new Service( $data['responder_service'] );
        $service->load();
        //send notifications to sender, receiver and admin
        $sender = get_userdata( get_current_user_id() );
        $receiver = get_userdata( $service->service_user_id );
        $email_data = array(
            '[sender_name]'   => $sender->data->display_name,
            '[receiver_name]' => $receiver->data->display_name,
            '[agreement_url]' => home_url( '/service/service/'),
        );
        //send email to requester
        cp_send_email( array('name' => $sender->data->display_name, 'email' => $sender->data->user_email), 'e2e_request_sender', $email_data );

        $email_data['[message_text]'] = $data['agreement_message'];
        //send email to receiver
        cp_send_email( array('name' => $receiver->data->display_name, 'email' => $receiver->data->user_email), 'e2e_request_receiver', $email_data );

        //send email to admin
        cp_send_email_to_admin( 'e2e_request_admin', $email_data );

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
            $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM wp_e2e_agreement WHERE responder_service_id = %d OR requester_service_id = %d ", $service_id, $service_id));
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
            case 'add-agreement':
                $service = new Service( $service_id );
                $service->load();
                if( ! Service::can_request_e2e( get_current_user_id(), $service_id ) ){
                    $error_message = 'You don\'t have permissions to request E2E testing';
                    $can = false;
                }
                break;
            case 'edit-agreement':
                $agreement = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM wp_e2e_agreement WHERE id = %d ", $agreement_id ) );
                if( $agreement ){
                    $requester_service = new Service( $agreement->requester_service_id );
                    $requester_service->load();
                    $responder_service = new Service( $agreement->responder_service_id );
                    $responder_service->load();
                    if( ! ( $requester_service->service_user_id == get_current_user_id() || $responder_service->service_user_id ) ){
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
}