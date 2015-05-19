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

    public function chargesProcessing(){
        $logs = array();
        /**
         * First we send all local organisations data to Xero
         */
//        $organisations_list = $this->db->get_results("SELECT * FROM wp_organisations");
//        if( $organisations_list ){
//            $counter = 0;
//            foreach( $organisations_list AS $organisation ){
//                $xero = new CT_Xero();
//                unset( $organisation->no_billing );
//                unset( $organisation->invoice_me );
//                unset( $organisation->id );
//                $xeroContact = $xero->upsertContact( (array) $organisation );
//                if( isset( $xeroContact['Contacts']['Contact']['ContactID'] ) ){
//                    $counter++;
//                    $this->db->update("wp_organisations",
//                        array( 'contact_id' => $xeroContact['Contacts']['Contact']['ContactID'] ),
//                        array( 'id' => $organisation->id ),
//                        array( '%s' ),
//                        array( '%d' )
//                    );
//                }
//            }
//            $logs[] = 'Updated '.$counter.' organisations';
//        }
        /**
         * Second: we should generate charges
         */
//        $chargesCounter = generateMonthlyCharges();
//        $logs[] = 'Generated '.$chargesCounter.' charges';
        /**
         * Generate draft invoices
         */
        $invoicesCounter = generateInvoices();
        $logs[] = 'Created '.$invoicesCounter.' invoices';
        /**
         * Cancel pending subscriptions
         */
        $subscriptionsCounter = 0;
        $subscriptions = $this->db->get_results( "SELECT * FROM wp_organisations_subscriptions
                                WHERE status='Unsubscribing' AND YEAR(last_charge_date) <= YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
                                AND MONTH(last_charge_date) <= MONTH(CURRENT_DATE - INTERVAL 1 MONTH)");
        foreach( $subscriptions AS $subscription ) {
            $controller = new CT_Organisation_Controller();
            $controller->delete_organisation_subscription( $subscription->id );
            $subscriptionsCounter++;
        }
        $logs[] = 'Cancelled '.$subscriptionsCounter.' subscriptions';
        return array( 'status' => 'success', 'message' => $logs );
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
        $conjobId = $this->db->get_var( $this->db->prepare( "SELECT * FROM wp_batch_jobs WHERE identifier = %s ", $_GET['jobid'] ) );
        $options = $this->_getCronjobOptions( $conjobId );
        $emails = explode( ',', $options['emails'] );
        $community = $this->db->get_row( $this->db->prepare( "SELECT * FROM wp_bp_groups WHERE name = %s ", $options['community'] ) );
        $token = get_option( 'reports_token_' . $community->id );
        $reportFile = $s3->getObject( '/reports/'.$community->name.'/'.$token.'/'.$community->name.'TestProgress.xls' );
        $upload = wp_upload_bits( $community->name.'TestProgress.xls', null, $reportFile );
        foreach( $emails AS $email ){
            $status = wp_mail( trim( $email ), $community->name . ' community testing progress report, generated ' . get_option( 'reports_generation_date' ), '   ', '', array( $upload['file'] ) );
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