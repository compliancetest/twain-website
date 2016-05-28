<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\OrganisationMember;
use App\OrganisationSubscription;
use App\Post;
use App\PostMeta;
use App\TestOutcomeStatus;
use App\Transaction;
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
        $this->userId = Auth::user()->ID;
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
        $transaction->product_id = $product->ID;
        $transaction->test_suite_id = $testSuite->ID;
        $transaction->audit_record = false;
        $transaction->test_outcome_status_id = $this->testOutcome ? TestOutcomeStatus::getIdByCode($this->testOutcome) : TestOutcomeStatus::getSuccessId();
        $transaction->customer_id = $this->userId;
        $transaction->subscription_id = $organisationSubscription->id;
        $transaction->organisation_id = $organisationMember->organisation_id;
        $transaction->s3_link = $transaction->getZipS3Link($this->fileName);
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
                $transactionLog->data_argument_type = $log['DataArgumentType'];
                $transactionLog->messages = $log['Messages'];
                $transactionLog->return_code = $log['ReturnCode'];
                $transactionLog->session_state = @$log['State'];
                $transactionLog->status = @$log['ExecutionResult'];
                $transactionLog->message_data = $log['Messages'];
                $transactionLog->twain_session_id = '';
                $transactionLog->screen_captures = json_encode([]);
                //process and save images
                $scanResults = [];
                /**
                 * Save transactionLog entry image
                 */
                if (!empty($log['ImageFileName']) && file_exists($this->rootFolder . '/scan_result/' . $log['ImageFileName'])) {
                    $logImageKey = $this->userId . '/' . $this->testCaseId . '/' . $this->executionId . '/' . $transactionLog->id . '/' . $log['ImageFileName'];

                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime = finfo_file($finfo, $this->rootFolder . '/scan_result/' . $log['ImageFileName']);
                    finfo_close($finfo);

                    $s3->putObject(array(
                        'Bucket' => config('env.bucket.transactions'),
                        'Key' => $logImageKey,
                        'ContentType' => $mime,
                        'SourceFile' => $this->rootFolder . '/scan_result/' . $log['ImageFileName'],
                    ));
                    $scanResults = [$transactionLog->getS3Link($logImageKey)];
                }
                $transactionLog->scan_results = json_encode($scanResults);
                $transactionLog->save();
            }
        }
        File::deleteDirectory($this->rootFolder);
    }
}
