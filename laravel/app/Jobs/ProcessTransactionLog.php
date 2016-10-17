<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\OrganisationMember;
use App\OrganisationSubscription;
use App\Post;
use App\PostMeta;
use App\TestOutcomeStatus;
use App\Transaction;
use App\TransactionChangeLog;
use App\TransactionsLog;
use Aws\Laravel\AwsFacade;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Aws\Laravel\AwsFacade as AWS;
use Illuminate\Support\Facades\File;


class ProcessTransactionLog extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    public $userId;
    public $fileName;
    public $executionId;
    public $testSuiteId;
    public $testCaseId;
    public $productId;
    public $rootFolder;
    public $testOutcome;
    public $reason;
    public $transactionKey;

    /**
     * ProcessTransactionLog constructor.
     * @param $fileName
     */
    public function __construct($fileName, $data)
    {
        $this->fileName = $fileName;
        $this->executionId = $data['execution_id'];
        $this->testCaseId = $data['test_case_id'];
        $this->testSuiteId = $data['test_suite_id'];
        $this->productId = $data['product_id'];
        $this->testOutcome = $data['test_outcome'];
        $this->reason = @$data['reason'];
        $this->userId = $data['user_id'];
        $this->transactionKey = $data['transaction_key'];
        $this->rootFolder = base_path() . '/storage/app/public/transactions/' . $this->executionId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $this->_process();
        } catch(\Exception $e){
            error_log($e->getMessage());
            error_log($e->getLine());
            error_log($e->getFile());
            File::deleteDirectory($this->rootFolder);
            $organisationMember = OrganisationMember::where(['user_id' => $this->userId])->first();
            $organisationSubscription = OrganisationSubscription::where(
                ['user_id' => $this->userId, 'organisation_id' => $organisationMember->organisation_id]
            )->first();
            $transaction = Transaction::create([
                'execution_id' => $this->executionId,
                'test_case_id' => 0,
                'customer_id' => $this->userId,
            ]);
            $transaction->product_id = 0;
            $transaction->test_suite_id = 0;
            $transaction->audit_record = false;
            $transaction->test_outcome_status_id = TestOutcomeStatus::getInvalidZipId();
            $transaction->customer_id = $this->userId;
            $transaction->subscription_id = $organisationSubscription->id;
            $transaction->organisation_id = $organisationMember->organisation_id;
            $transaction->s3_link = $transaction->getZipS3Link($this->fileName);
            $transaction->save();
        }
    }

    private function _process()
    {
        $fileName = basename($this->fileName);
        $s3 = AwsFacade::createClient('s3');
        $s3->getObject(array(
            'Bucket' => config('env.bucket.transactions'),
            'Key' => $this->fileName,
            'SaveAs' => $this->rootFolder . $fileName
        ))['Body'];
        $za = new \ZipArchive();
        $za->open($this->rootFolder . $fileName);
        $za->extractTo($this->rootFolder);
        $za->close();
        @unlink($this->rootFolder . $fileName);

        $product = Post::where(['post_name' => $this->productId, 'post_type' => 'product-service'])->first();
        $testSuite = Post::where(['post_name' => $this->testSuiteId, 'post_type' => 'test-suite'])->first();
        $testCase = Post::where(['post_name' => $this->testCaseId, 'post_type' => 'test-case'])->first();

        $organisationMember = OrganisationMember::where(['user_id' => $this->userId])->first();
        $organisationSubscription = OrganisationSubscription::where(
            [
                'user_id' => $this->userId,
                'organisation_id' => $organisationMember->organisation_id,
                'suite_family_mark' => $testSuite->ID,
            ]
        )->first();
        $transaction = Transaction::firstOrCreate([
            'execution_id' => $this->executionId,
            'test_case_id' => $testCase->ID,
            'customer_id' => $this->userId,
        ]);

        if (!$transaction->test_outcome_status_id ||
            ($transaction->test_outcome_status_id && TestOutcomeStatus::find($transaction->test_outcome_status_id)->code != strtoupper($this->testOutcome))) {
            TransactionChangeLog::addLog($transaction, $this->userId, $this->testOutcome);
        }

        $transaction->product_id = $product->ID;
        $transaction->test_suite_id = $testSuite->ID;
        $transaction->audit_record = false;
        $transaction->test_outcome_status_id = $this->testOutcome ? TestOutcomeStatus::getIdByCode($this->testOutcome) : TestOutcomeStatus::getSuccessId();
        $transaction->reason = $this->reason;
        $transaction->customer_id = $this->userId;
        $transaction->subscription_id = $organisationSubscription->id;
        $transaction->organisation_id = $organisationMember->organisation_id;
        $transaction->s3_link = $transaction->getZipS3Link($this->fileName);

        if(file_exists($this->rootFolder . '/execution_config/execution_config.json')) {
            $s3 = Aws::createClient('s3');
            $s3->putObject(array(
                'Bucket' => config('env.bucket.transactions'),
                'Key' => $this->transactionKey . '/execution_config.json',
                'Body' => file_get_contents($this->rootFolder . '/execution_config/execution_config.json'),
                'ContentType' => 'application/json',
            ));
            $transaction->execution_config = $this->transactionKey . '/execution_config.json';
        }
        $transaction->save();

        //execution log
        if(file_exists($this->rootFolder . '/execution_log/execution_log.json')) {
            $executionLogData = json_decode(file_get_contents($this->rootFolder . '/execution_log/execution_log.json'), true);
            $from = $to = '';
            $order = 1;
            TransactionsLog::where('transaction_id', $transaction->id)->delete();
            foreach ($executionLogData as $k => $log) {
                $transactionLog = TransactionsLog::create([
                    'execution_id' => $order,
                    'transaction_id' => $transaction->id,
                ]);
                if (isset($log['DateTime'])) {
                    $transactionLog->created_at = $log['DateTime'];
                }
                $transactionLog->test_step = @$log['Step'];
                if ($log['From']) {
                    $from = $log['From'];
                }
                if ($log['To']) {
                    $to = $log['To'];
                }

                if(!empty($log['Output'])){
                    $env = explode('/', $this->fileName)[0];
                    $fileName =  $env . '/transactions/' .$this->userId . '/' . $this->testCaseId . '/' . $this->executionId . '/' . $order .'.json';
                    $s3 = AwsFacade::createClient('s3');
                    $s3->putObject(array(
                        'Bucket' => config('env.bucket.transactions'),
                        'Key' => $fileName,
                        'Body' => json_encode($log['Output'], JSON_PRETTY_PRINT),
                        'ContentType' => 'application/json',
                    ));
                    $transactionLog->log_output = $fileName;
                }
                $transactionLog->from = $from;
                $transactionLog->to = $to;
                $transactionLog->execution_order = $order++;

                $transactionLog->data_group = $log['DataGroup'];
                $transactionLog->step_outcome = !empty($log['Outcome']) ? $log['Outcome'] : '';
                $transactionLog->data_argument_type = $log['DataArgumentType'];
                $transactionLog->messages = $log['Messages'];
                $transactionLog->return_code = $log['ReturnCode'];
                $transactionLog->session_state = @$log['State'];
                $transactionLog->status = @$log['ExecutionResult'];
                $transactionLog->message_data = $log['Messages'];
                $transactionLog->screen_captures = json_encode([]);
                $transactionLog->reason = @$log['Reason'];
                //process and save images
                $scanResults = $scanResultsMeta = [];
                /**
                 * Save transactionLog entry image
                 */
                if (!empty($log['ImageFileName']) && file_exists($this->rootFolder . '/scan_result/' . $log['ImageFileName'])) {
                    $logImageKey = $this->userId . '/' . $this->testCaseId . '/' . $this->executionId . '/' . $transactionLog->id . '/' . $log['ImageFileName'];

                    $mime = handleMimeTypeForImage($this->rootFolder . '/scan_result/' . $log['ImageFileName']);

                    $s3->putObject(array(
                        'Bucket' => config('env.bucket.transactions'),
                        'Key' => $logImageKey,
                        'ContentType' => $mime,
                        'SourceFile' => $this->rootFolder . '/scan_result/' . $log['ImageFileName'],
                    ));
                    if(glob($this->rootFolder . '/scan_result/' . $log['ImageFileName'] . '.json')){
                        $s3->putObject(array(
                            'Bucket' => config('env.bucket.transactions'),
                            'Key' => file_get_contents($this->rootFolder . '/scan_result/' . $log['ImageFileName'] . '.json'),
                            'ContentType' => 'application/json',
                            'SourceFile' => $this->rootFolder . '/scan_result/' . $log['ImageFileName'] . '.json',
                        ));
                        $scanResultsMeta = $transactionLog->getS3Link($logImageKey) . '.json';
                    }
                    $scanResults = [$transactionLog->getS3Link($logImageKey)];
                }
                /**
                 * Save transactionLog screenshots
                 */
                $screenCaptures = [];
                if (!empty($log['ScreenCaptureFileName'])) {
                    foreach($log['ScreenCaptureFileName'] AS $screenCapture) {
                        if(file_exists($this->rootFolder . '/screen_capture/' . $screenCapture)) {
                            $screenCaptureImageKey = $this->userId . '/' . $this->testCaseId . '/' . $this->executionId . '/' . $transactionLog->id . '/screen_capture/' . $screenCapture;

                            $mime = handleMimeTypeForImage($this->rootFolder . '/screen_capture/' . $screenCapture);

                            $s3->putObject(array(
                                'Bucket' => config('env.bucket.transactions'),
                                'Key' => $screenCaptureImageKey,
                                'ContentType' => $mime,
                                'SourceFile' => $this->rootFolder . '/screen_capture/' . $screenCapture,
                            ));
                            $screenCaptures[] = $transactionLog->getS3Link($screenCaptureImageKey);
                        }
                    }
                }
                $transactionLog->scan_results = json_encode($scanResults);
                $transactionLog->scan_results_meta = json_encode($scanResultsMeta);
                $transactionLog->screen_captures = json_encode($screenCaptures);
                $transactionLog->save();
            }
        }


        if($testSuite->getMetaByKey('ts_tester_role') != 'Application' && ($transaction->test_outcome_status_id == TestOutcomeStatus::getIdByCode('PENDING') ||
            $transaction->test_outcome_status_id == TestOutcomeStatus::getIdByCode('PASS')) && isServerValidationEnabled()) {
            /*
             * Ensure that each TWRC_XFERDONE returs code has image
             */
            if (!$transaction->logs()->where(['return_code' => 'TWRC_XFERDONE', 'scan_results' => '[]'])->get()->isEmpty()) {
                if ($transaction->test_outcome_status_id != TestOutcomeStatus::getIdByCode('FAIL')) {
                    TransactionChangeLog::addLog($transaction, $this->userId, 'FAIL', true);
                }
                $transaction->test_outcome_status_id = TestOutcomeStatus::getIdByCode('FAIL');
                $transaction->reason = 'Condition "Each successful data transfer should have an associated image." was not met.';
            } else {
                if ($testCase->post_name == 'ca-01-v1-0') {
                    $testCaseValidator = new \App\CA01($this->rootFolder, $transaction, $this->userId);
                    $testCaseValidator->validate();
                } else if($testCase->post_name == 'ca-03-v1-0'){
                    $testCaseValidator = new \App\CA03($this->rootFolder, $transaction, $this->userId);
                    $testCaseValidator->validate();
                } else if($testCase->post_name == 'ca-04-v1-0'){
                    $testCaseValidator = new \App\CA04($this->rootFolder, $transaction, $this->userId);
                    $testCaseValidator->validate();
                } else if($testCase->post_name == 'ca-05-v1-0'){
                    $testCaseValidator = new \App\CA05($this->rootFolder, $transaction, $this->userId);
                    $testCaseValidator->validate();
                } else if ($testCase->post_name == 'ca-07-v1-0') {
                    $testCaseValidator = new \App\CA07($this->rootFolder, $transaction, $this->userId);
                    $testCaseValidator->validate();
                } else if ($testCase->post_name == 'ip-01-v1-0') {
                    $testCaseValidator = new \App\IP01($this->rootFolder, $transaction, $this->userId);
                    $testCaseValidator->validate();
                }
            }
            $transaction->save();
        }
        File::deleteDirectory($this->rootFolder);
    }
}
