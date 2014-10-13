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
        if( $wpdb->last_error ){
            addMessage( $wpdb->last_error, 'error' );
            wp_redirect( $_REQUEST['_wp_http_referer'] );
            exit;
        }
        return $insert_id;
    }

    public function get_service_agreements( $service_id, $status = false ){
        global $wpdb;
        if( $status ){
            $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM wp_e2e_agreement WHERE ( responder_service_id = %d OR requester_service_id = %d ) AND status = %s ", $service_id, $service_id, $status ) );
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
            array_push( $agreements, $result );
        }
        return $agreements;
    }
}