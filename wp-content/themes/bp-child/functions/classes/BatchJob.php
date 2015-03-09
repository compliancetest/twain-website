<?php

class BatchJob {

    public function __construct(){
        global $wpdb;
        $this->db = $wpdb;
        $this->s3 = new S3Wrapper();
    }

    public function execute( $jobid, $key ){
        if( $row = $this->db->get_row( $this->db->prepare( "SELECT * FROM wp_batch_jobs WHERE identifier = %s AND access_key = %s AND is_active = 1 ", $jobid, $key ) ) ){
            try {
                if( method_exists( $this, $row->function_name ) ) {
                    $comment = '';
                    $status = call_user_func(array($this, $row->function_name));
                    if( $status !== true ){
                        $comment = $status['message'];
                    }
                    $this->_sendReportToS3( $jobid, $comment, $status === true ? 'success' : 'error' );
                } else{
                    $this->_sendReportToS3( $jobid, "System can't process '{$jobid}' identifier - please verify job data in database", 'error' );
                }
            } catch( Exception $e ){
                $this->_sendReportToS3( $jobid, $e->getMessage(), 'error' );
            }
        }
    }

    public function test( ){
        $email = 'ivansolowjew@gmail.com';
        if( isset( $_GET['email'] ) ){
            if( ! filter_var( $_GET['email'], FILTER_VALIDATE_EMAIL ) ){
                return array( 'status' => 'error', 'message' => 'Invalid email' );
            }
            $email = filter_var( $_GET['email'], FILTER_SANITIZE_EMAIL );
        }
        return wp_mail( $email, 'test', 'test to: '.$email );
    }

    private function _sendReportToS3( $jobId, $comment, $status = 'success' ){
        $message = array(
            'status'    => $status,
            'comment'   => $comment,
            'date'      => date( 'Y-m-d H:i:s'),
            'timestamp' => time(),
            'jobid'     => $jobId
        );
        $this->s3->putObject( 'logs/batch/' . $jobId . '/' . date( 'Y-m-d' ) .'/' . date( 'H:i:s' ) . '_' . $status . '_output.log', json_encode( $message, JSON_PRETTY_PRINT ), 'application/json' );
    }
}