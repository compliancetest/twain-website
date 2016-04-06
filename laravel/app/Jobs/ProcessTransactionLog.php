<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\OrganisationMember;
use App\OrganisationSubscription;
use App\Post;
use App\PostMeta;
use App\Transaction;
use App\TransactionsLog;
use Aws\Laravel\AwsFacade;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;

class ProcessTransactionLog extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    public $userId;
    public $fileName;
    public $executionId;

    /**
     * ProcessTransactionLog constructor.
     * @param $fileName
     */
    public function __construct($fileName, $executionId)
    {
        $this->fileName = $fileName;
        $this->executionId = $executionId;
        $this->userId = Auth::user()->ID;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->_process();
    }

    private function getFolderName($path)
    {
        $dirs = [];
        $objects = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
        foreach ($objects as $name => $object) {

            if ($object->getFileName() == '..' || $object->getFileName() == '.' || $object->getFileName() == '.gitignore') {
                continue;
            }
            $dirs[] = $object->getFileName();
        }
        return $dirs;
    }

    private function _process()
    {
        $fileName = basename($this->fileName);
        $filePath = base_path() . '/storage/app/public/transactions/';
        $s3 = AwsFacade::createClient('s3');
        $s3->getObject(array(
            'Bucket' => 'data.twain.gosource.com.au',
            'Key' => $this->fileName,
            'SaveAs' => $filePath . $fileName
        ))['Body'];
        $za = new \ZipArchive();
        $za->open($filePath . $fileName);
        $za->extractTo($filePath);
        $za->close();
        @unlink($filePath . $fileName);

        $rootFolders = $this->getFolderName($filePath);

        foreach ($rootFolders as $rootFolder) {
            $productIdentifier = explode('_', $rootFolder, 2)[1];

            $product = Post::where(['post_name' => $productIdentifier, 'post_type' => 'product-service'])->first();

            $testSuiteFolders = $this->getFolderName($filePath . $rootFolder);
            foreach ($testSuiteFolders as $testSuiteFolder) {
                $testSuite = Post::where(['post_name' => $testSuiteFolder, 'post_type' => 'test-suite'])->first();

                $testCaseFolders = $this->getFolderName($filePath . $rootFolder . '/' . $testSuiteFolder);
                foreach ($testCaseFolders as $testCaseFolder) {
                    $testCase = Post::where(['post_name' => $testCaseFolder, 'post_type' => 'test-case'])->first();

                    $organisationMember = OrganisationMember::where(['user_id' => $this->userId])->first();
                    $organisationSubscription = OrganisationSubscription::where(
                        ['user_id' => $this->userId, 'organisation_id' => $organisationMember->organisation_id]
                    )->first();

                    $transaction = Transaction::firstOrCreate([
                        'execution_id' => $this->executionId,
                        'test_case_id' => $testCase->ID,
                    ]);
                    $transaction->product_id = $product->ID;
                    $transaction->test_suite_id = $testSuite->ID;
                    $transaction->audit_record = false;
                    $transaction->test_outcome_status_id = 1;
                    $transaction->customer_id = $this->userId;
                    $transaction->subscription_id = $organisationSubscription->id;
                    $transaction->organisation_id = $organisationMember->organisation_id;
                    $transaction->save();

                    $executionIdFolders = $this->getFolderName($filePath . $rootFolder . '/' . $testSuiteFolder . '/' . $testCaseFolder);
                    foreach ($executionIdFolders as $executionIdFolder) {
                        $executionId = explode('_', $executionIdFolder, 2)[1];

                        //execution log
                        $executionLogData = json_decode(file_get_contents($filePath . $rootFolder . '/' . $testSuiteFolder . '/' . $testCaseFolder . '/' . $executionIdFolder . '/execution_log/execution_log.json'), true);

                        foreach ($executionLogData as $log) {
                            if (!empty($log['From'])) {
                                $transactionLog = TransactionsLog::firstOrNew([
                                    'execution_id' => $executionId,
                                    'transaction_id' => $transaction->id,
                                ]);
                                $transactionLog->test_step = json_encode($log['States']);
                                $transactionLog->from = $log['From'];
                                $transactionLog->to = $log['To'];
                                $transactionLog->operation_triplet = $log['DataGroup'];
                                $transactionLog->return_code = $log['ReturnCode'];
                                $transactionLog->session_state = $log['State'];
                                $transactionLog->status = $log['ExecutionResult'];
                                $transactionLog->message_data = $log['Messages'];
                                $transactionLog->twain_session_id = '';
                                $transactionLog->screen_captures = json_encode([]);
                                $transactionLog->scan_results = json_encode([]);
                                $transactionLog->save();
                            }
                        }

                    }
                }
            }
        }
        $this->_delTree($filePath . $rootFolder);
    }

    private function _delTree($dir)
    {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->_delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

}
