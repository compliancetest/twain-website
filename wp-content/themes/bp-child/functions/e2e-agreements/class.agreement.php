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
        $insert_id = $wpdb->insert( $this->_table,
            array(
                'str_id'                 => $data['agreement_id'],
                'requester_service_id'   => $data['requester_service'],
                'responder_service_id'   => $data['responder_service'],
                'requestor_profile'      => @$data['requester_profiles'],
                'status'                 => 'Pending',
                'requestor_message'      => $data['agreement_message'],
                'requestor_message_date' => gmmktime()
            ),
            array( '%s', '%d', '%d', '%d', '%s', '%s' )
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
}