<?php

class BatchJob {

    public function __construct(){
        global $wpdb;
        $this->db = $wpdb;
        $this->s3 = new S3Wrapper();
        $logsBucket = get_option( 's3_logs_bucket' );
        $this->bucket = ! empty( $logsBucket ) ? $logsBucket : get_option( 'aws_s3_url' );
    }

    /**
     * @param $jobid jobid - identifier parameter from wp_batch_jobs table
     * @param $key - access_key parameter from wp_batch_jobs table
     */
    public function execute( $jobid, $key ){
        if( $row = $this->db->get_row( $this->db->prepare( "SELECT * FROM wp_batch_jobs WHERE identifier = %s AND access_key = %s AND is_active = 1 ", $jobid, $key ) ) ){
            try {
                if( method_exists( $this, $row->function_name ) ) {
                    $comment = '';
                    $status = call_user_func(array($this, $row->function_name));
                    if( isset( $status['message'] ) && ! empty( $status['message'] ) ){
                        $comment = $status['message'];
                    }
                    $this->_sendReportToS3( $jobid, $comment, $status['status'] === 'success' ? 'success' : 'error' );
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
        $status = wp_mail( $email, 'test', 'test to: '.$email );
        if( $status ){
            return array( 'status' => 'success', 'message' => 'Email was sent to: '.$email );
        } else{
            return array( 'status' => 'error', 'message' => 'Error occured' );
        }
    }

    /**
     * This cronjob used to re-generate testing progress report dayly
     * @return array
     */
    public function generateTestingReport(){
        send_reports_to_s3();
        return array( 'status' => 'success', 'message' => 'SuperStream testing progress report was generated successfully' );
    }

    /**
     * This cronjob used to send testing progress report to users
     * @return array
     */
    public function notifyUsers(){
        $s3 = new S3Wrapper();
        $messages = array();
        //3 - cronjob id in database
        $options = $this->_getCronjobOptions( 3 );
        $emails = explode( ',', $options['emails'] );
        $community = $this->db->get_row( $this->db->prepare( "SELECT * FROM wp_bp_groups WHERE name = %s ", $options['community'] ) );
        $token = get_option( 'reports_token_' . $community->id );
        $reportFile = $s3->getObject( '/reports/'.$community->name.'/'.$token.'/'.$community->name.'TestProgress.xls' );
        $upload = wp_upload_bits( $community->name.'TestProgress.xls', null, $reportFile );
        foreach( $emails AS $email ){
            $status = wp_mail( trim( $email ), $community->name . ' community testing progress report, generated ' . date( 'Y-m-d'), 'Testing progress report', '', array( $upload['file'] ) );
            $messages[$email] = $status == true ? 'Success' : 'Error';
        }
        @unlink( $upload['file'] );
        return array( 'status' => 'success', 'message' => $messages );
    }

    private function _getCronjobOptions( $jobId ){
        $options = array();
        $results   = $this->db->get_results( $this->db->prepare( "SELECT * FROM wp_batch_jobs_params WHERE batch_job_id = %d ", $jobId ) );
        foreach( $results AS $result ){
            $options[$result->name] = $result->value;
        }
        return $options;
    }
    private function _sendReportToS3( $jobId, $comment, $status = 'success' ){
        $message = array(
            'status'    => $status,
            'comment'   => $comment,
            'date'      => date( 'Y-m-d H:i:s'),
            'timestamp' => time(),
            'jobid'     => $jobId
        );
        $this->s3->putObject( 'logs/batch/' . $jobId . '/' . date( 'Y-m-d' ) .'/' . date( 'H:i:s' ) . '_' . $status . '_output.log', json_encode( $message, JSON_PRETTY_PRINT ), 'application/json', $this->bucket );
    }
}