<?php

class BatchJob
{

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->s3 = new S3Wrapper();
        $logsBucket = get_option('s3_logs_bucket');
        $this->bucket = !empty($logsBucket) ? $logsBucket : get_option('aws_s3_url');
        $this->jobId = false;
    }

    /**
     * @param $jobid jobid - identifier parameter from wp_batch_jobs table
     * @param $key - access_key parameter from wp_batch_jobs table
     */
    public function execute($jobid, $key)
    {
        if (is_super_admin() && isset($_GET['is_active']) && wp_verify_nonce($_GET['is_active'], 'is_active')) {
            $row = $this->db->get_row($this->db->prepare("SELECT * FROM wp_batch_jobs WHERE identifier = %s AND access_key = %s", $jobid, $key));
        } else {
            $row = $this->db->get_row($this->db->prepare("SELECT * FROM wp_batch_jobs WHERE identifier = %s AND access_key = %s AND is_active = 1 ", $jobid, $key));
        }
        if ($row) {
            try {
                if (method_exists($this, $row->function_name)) {
                    $this->jobId = $jobid;
                    $comment = '';
                    $status = call_user_func(array($this, $row->function_name));
                    if (isset($status['message']) && !empty($status['message'])) {
                        $comment = $status['message'];
                    }
                    $this->_sendReportToS3($jobid, $comment, $status['status'] === 'success' ? 'success' : 'error');
                } else {
                    $this->_sendReportToS3($jobid, "System can't process '{$jobid}' identifier - please verify job data in database", 'error');
                }
            } catch (Exception $e) {
                $this->_sendReportToS3($jobid, $e->getMessage(), 'error');
            }
        }
    }

    public function monitorQueue()
    {
        $conjobId = $this->db->get_var($this->db->prepare("SELECT * FROM wp_batch_jobs WHERE identifier = %s ", $_GET['jobid']));
        $options = $this->_getCronjobOptions($conjobId);
        if ($options['condition'] == 'notempty') {
            if (isset($options['emails']) && !empty($options['emails'])) {
                foreach (explode(',', $options['qname']) as $queue) {
                    $queue = trim($queue);
                    $sqsClient = new SqsWrapper($queue);
                    $messagesNumber = $sqsClient->getQueueMessagesCount();
                    if ($messagesNumber == 0) {
                        $emailLogs = 'SQS queue ' . $queue . ' is not empty(' . $messagesNumber . ' messages)';
                        $logs['Email logs'] = array();
                        foreach (explode(',', $options['emails']) AS $email) {
                            $status = wp_mail(trim($email), 'MONITOR_QUEUE', $emailLogs);
                            $logs['Email logs'][] = array('status' => $status == true ? 'Success' : 'Error', 'email' => trim($email));
                        }
                    }
                }
            }
        }
        return array('status' => 'success', 'message' => $logs);
    }

    /**
     * This function used to start / stop AWS EC2 instances
     * example request: ?jobid=SERVER_CONTROL&key=YOUR_KEY&servers=i-3eb5d301,i-3eb5d302
     * action - could be start / stop only
     * servers - function get this values from database
     * @return array
     */
    public function serverControl()
    {
        $action = $this->db->get_var($this->db->prepare("SELECT value FROM wp_batch_jobs_params WHERE name = 'action' AND batch_job_id = (SELECT id FROM wp_batch_jobs WHERE identifier = %s ) ", $this->jobId));
        $servers = explode(',', $this->db->get_var($this->db->prepare("SELECT value FROM wp_batch_jobs_params WHERE name = 'servers' AND batch_job_id = (SELECT id FROM wp_batch_jobs WHERE identifier = %s ) ", $this->jobId)));
        if (!$action || !$servers) {
            return array('status' => 'error', 'message' => 'Cronjob not configured properly');
        }
        $ec2Client = new Ec2Wrapper();
        $response = $ec2Client->changeStatus($action, $servers);
        $conjobId = $this->db->get_var($this->db->prepare("SELECT * FROM wp_batch_jobs WHERE identifier = %s ", $_GET['jobid']));
        $options = $this->_getCronjobOptions($conjobId);
        if ($response['status'] != 'success') {
            if (isset($options['emails']) && !empty($options['emails'])) {
                $emailLogs = '<pre>' . print_r($response, true) . '</pre>';
                $logs['Email logs'] = array();
                foreach (explode(',', $options['emails']) AS $email) {
                    $status = wp_mail(trim($email), $action . ' server action', $emailLogs);
                    $logs['Email logs'][] = array('status' => $status == true ? 'Success' : 'Error', 'email' => trim($email));
                }
            }
        }
        return $response;
    }

    /**
     * This function used to assign IP to AWS instances
     * example request: ?jobid=ASSIGN_IP&key=YOUR_KEY
     * action - could be start / stop only
     * servers - function get this values from database
     * @return array
     */
    public function assignIP()
    {
        $servers = explode(',', $this->db->get_var($this->db->prepare("SELECT value FROM wp_batch_jobs_params WHERE name = 'servers' AND batch_job_id = (SELECT id FROM wp_batch_jobs WHERE identifier = %s ) ", $this->jobId)));
        $ipaddresses = explode(',', $this->db->get_var($this->db->prepare("SELECT value FROM wp_batch_jobs_params WHERE name = 'ipaddresses' AND batch_job_id = (SELECT id FROM wp_batch_jobs WHERE identifier = %s ) ", $this->jobId)));
        if (!$servers || !$ipaddresses) {
            return array('status' => 'error', 'message' => 'Cronjob not configured properly');
        }
        $resp = array();
        $status = 'success';
        foreach ($servers as $key => $server) {
            $ec2Client = new Ec2Wrapper();
            $response = $ec2Client->assignIp($server, $ipaddresses[$key]);
            $resp[] = $response;
            $conjobId = $this->db->get_var($this->db->prepare("SELECT * FROM wp_batch_jobs WHERE identifier = %s ", $_GET['jobid']));
            $options = $this->_getCronjobOptions($conjobId);
            if ($response['status'] != 'success') {
                $status = $response['status'];
                if (isset($options['emails']) && !empty($options['emails'])) {
                    $emailLogs = '<pre>' . print_r($response, true) . '</pre>';
                    $logs['Email logs'] = array();
                    foreach (explode(',', $options['emails']) AS $email) {
                        $status = wp_mail(trim($email), 'Assign IP server action', $emailLogs);
                        $logs['Email logs'][] = array('status' => $status == true ? 'Success' : 'Error', 'email' => trim($email));
                    }
                }
            }
        }
        return array('status' => $status, 'message' => $resp);
    }

    public function chargesProcessing()
    {
        $logs = array();
        /**
         * First we send all local organisations data to Xero
         */
        $organisations_list = $this->db->get_results("SELECT * FROM wp_organisations");
        if ($organisations_list) {
            $counter = 0;
            foreach ($organisations_list AS $organisation) {
                $xero = new CT_Xero();
                unset($organisation->no_billing);
                unset($organisation->invoice_me);
                unset($organisation->id);
                $xeroContact = $xero->upsertContact((array)$organisation);
                if (isset($xeroContact['Contacts']['Contact']['ContactID'])) {
                    $counter++;
                    $this->db->update("wp_organisations",
                        array('contact_id' => $xeroContact['Contacts']['Contact']['ContactID']),
                        array('id' => $organisation->id),
                        array('%s'),
                        array('%d')
                    );
                }
            }
            $logs['Push Organisation details from website to Xero'] = 'Updated ' . $counter . ' organisations';
        }
        /**
         * Second: we should generate charges
         */
        $chargesCounter = generateMonthlyCharges();
        $logs['Generate charges'] = 'Generated ' . $chargesCounter . ' charges';
        /**
         * Generate draft invoices
         */
        $invoicesCounter = generateInvoices();
        $logs['Generate draft invoices'] = 'Created ' . $invoicesCounter['counter'] . ' invoices';
        /**
         * Cancel pending subscriptions
         */
        $subscriptionsCounter = 0;
        $subscriptions = $this->db->get_results("SELECT * FROM wp_organisations_subscriptions
                                WHERE status='Unsubscribing' AND YEAR(last_charge_date) <= YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
                                AND MONTH(last_charge_date) <= MONTH(CURRENT_DATE - INTERVAL 1 MONTH)");
        foreach ($subscriptions AS $subscription) {
            $controller = new CT_Organisation_Controller();
            $controller->delete_organisation_subscription($subscription->id);
            $subscriptionsCounter++;
        }
        $logs['Cancel pending subscriptions'] = 'Cancelled ' . $subscriptionsCounter . ' subscriptions';

        $conjobId = $this->db->get_var($this->db->prepare("SELECT * FROM wp_batch_jobs WHERE identifier = %s ", $_GET['jobid']));
        $options = $this->_getCronjobOptions($conjobId);
        if (isset($options['emails']) && !empty($options['emails'])) {
            $emailLogs = $logs;
            $logs['Email logs'] = array();
            foreach (explode(',', $options['emails']) AS $email) {
                $status = wp_mail(trim($email), 'Beginning of month processing', implode('<br>', $emailLogs));
                $logs['Email logs'][] = array('status' => $status == true ? 'Success' : 'Error', 'email' => trim($email));
            }

        }
        return array('status' => 'success', 'message' => $logs);
    }

    /**
     * This cronjob used to re-generate testing progress report dayly
     * @return array
     */
    public function generateTestingReport()
    {
        send_reports_to_s3();
        return array('status' => 'success', 'message' => 'SuperStream testing progress report was generated successfully');
    }

    /**
     * This cronjob used to send testing progress report to users
     * @return array
     */
    public function notifyUsers()
    {
        $s3 = new S3Wrapper();
        $messages = array();
        $conjobId = $this->db->get_var($this->db->prepare("SELECT * FROM wp_batch_jobs WHERE identifier = %s ", $_GET['jobid']));
        $options = $this->_getCronjobOptions($conjobId);
        $emails = explode(',', $options['emails']);
        $community = $this->db->get_row($this->db->prepare("SELECT * FROM wp_bp_groups WHERE name = %s ", $options['community']));
        $token = get_option('reports_token_' . $community->id);
        $reportFile = $s3->getObject('/reports/' . $community->name . '/' . $token . '/' . $community->name . 'TestProgress.xls');
        $upload = wp_upload_bits($community->name . 'TestProgress.xls', null, $reportFile);
        foreach ($emails AS $email) {
            $status = wp_mail(trim($email), $community->name . ' community testing progress report, generated ' . get_option('reports_generation_date'), '   ', '', array($upload['file']));
            $messages[$email] = $status == true ? 'Success' : 'Error';
        }
        @unlink($upload['file']);
        return array('status' => 'success', 'message' => $messages);
    }

    public function _getCronjobOptions($jobId)
    {
        $options = array();
        $results = $this->db->get_results($this->db->prepare("SELECT * FROM wp_batch_jobs_params WHERE batch_job_id = %d ", $jobId));
        foreach ($results AS $result) {
            $options[$result->name] = $result->value;
        }
        return $options;
    }

    private function _sendReportToS3($jobId, $comment, $status = 'success')
    {
        $message = array(
            'status' => $status,
            'comment' => $comment,
            'date' => date('Y-m-d H:i:s'),
            'timestamp' => time(),
            'jobid' => $jobId
        );
        $this->s3->putObject('logs/batch/' . $jobId . '/' . date('Y-m-d') . '/' . date('H:i:s') . '_' . $status . '_output.log', json_encode($message, JSON_PRETTY_PRINT), 'application/json', $this->bucket);
        if (is_super_admin()) {
            addMessage(json_encode($message, JSON_PRETTY_PRINT), $status);
            wp_redirect(home_url() . '/wp-admin/admin.php?page=manage-batch-jobs');
            exit();
        }
    }
}