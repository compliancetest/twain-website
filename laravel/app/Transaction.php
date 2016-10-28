<?php

namespace App;

use Aws\Laravel\AwsFacade;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Transaction extends Model
{
    use UuidTrait;

    public $incrementing = false;

    protected $fillable = [
        'execution_id', 'test_case_id', 'audit_record'
    ];

    private $whereModel = null;

    /**
     * Transaction logs relation
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function logs()
    {
        return $this->hasMany('App\TransactionsLog')->orderBy('execution_order');
    }

    /**
     * Transaction explanation logs relation
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function explanationLogs()
    {
        return $this->hasMany('App\TransactionExplanationLogs')->orderBy('created_at');
    }

    public function usedInClaims()
    {
        return $this->hasMany('App\ClaimTransactions');
    }

    /**
     * Ensure that transaction can be deleted by current user
     * @return bool
     */
    public function canBeDeleted()
    {
        $usedInVerifyRequest = VerifyRequest::where('transactions', 'LIKE', '%' . $this->id . '%')->first();
        $hasAccessToTransaction = Transaction::where('id', $this->id)->whereIn('subscription_id', $this->getUserSubscriptions())->get() || doesUserAdminInAnyCommunity() || doesUserSupportInAnyCommunity() || is_super_admin();
        if (!$usedInVerifyRequest && $hasAccessToTransaction && $this->audit_record == false && $this->usedInClaims->isEmpty()) {
            return true;
        }
        return false;
    }

    /**
     * Ensure that user can update current transaction
     * @return bool
     */
    public function userHasAccess()
    {
        return Transaction::where('id', $this->id)->whereIn('subscription_id', $this->getUserSubscriptions())->get() || doesUserAdminInAnyCommunity() || doesUserSupportInAnyCommunity() || is_super_admin();
    }

    /**
     * Generate s3 link to zip file
     * @param $fileName
     * @return string
     */
    public function getZipS3Link($fileName)
    {
        return 'https://s3-'.config('env.bucket.region').'.amazonaws.com/'.config('env.bucket.transactions').'/' . $fileName;
    }

    /**
     * Get list of transactions which used in VerifyRequest
     * @param $productId
     * @param $testSuiteId
     * @return array
     */
    public static function getTransactionsForVerifyRequest($productId, $testSuiteId)
    {
        $processedTransactions = $processedTransactions1 = [];
        $transactions = DB::table('transactions')
        ->join('wp_test_cases', 'transactions.test_case_id', '=', 'wp_test_cases.case_id')
        ->where([
            'product_id' => $productId,
            'suite_minor_family_mark' => $testSuiteId,
            'test_outcome_status_id' => TestOutcomeStatus::getIdByCode('PENDING')
        ])
        ->orderBy('wp_test_cases.case_name')
        ->get();
        foreach($transactions as $transaction){
            $processedTransactions[$transaction->test_case_id][] = $transaction;
        }
        return $processedTransactions;
    }

    public function getScannedImagesData()
    {
        $executionData = false;
        $executionConfig = $this->getExecutionConfig();
        if($executionConfig){
            if(isset($executionConfig['data']['ExecutionProfile']['UserValidation'])){
                $executionData = $executionConfig['data']['ExecutionProfile']['UserValidation'];
            }
        }
        $result = [];
        $logs = $this->logs()->where('return_code', 'TWRC_XFERDONE')->get();
        foreach($logs as $kk => $log){
            $meta = json_decode($log->scan_results_meta, true);
            foreach(json_decode($log->scan_results, true) as $key => $image){
                $result[] = [
                    'image' => $image,
                    'imageMeta' => $meta,
                    'expectedImage' => isset($executionData[$kk]['ExpectedResult']) ? $executionData[$kk]['ExpectedResult'] : false,
                    'passConditions' => isset($executionData[$kk]['PassConditions']) ? $executionData[$kk]['PassConditions'] : false,
                    'skipConditions' => isset($executionData[$kk]['SkipConditions']) ? $executionData[$kk]['SkipConditions'] : false,
                    'log' => $log,
                    'extImageInfo' => $this->logs()->whereIn('execution_order', [$log->execution_order+1, $log->execution_order+2])->where('data_argument_type', 'DAT_EXTIMAGEINFO')->first(),
                    'imageInfo' => $this->logs()->whereIn('execution_order', [$log->execution_order+1, $log->execution_order+2])->where('data_argument_type', 'DAT_IMAGEINFO')->first(),
                ];
            }
        }
        return $result;
    }

    /**
     * Get transaction execution config file
     * @return array|bool|mixed
     */
    public function getExecutionConfig()
    {
        $s3 = App::make('aws')->createClient('s3');
        if (!empty($this->execution_config) && $s3->doesObjectExist(config('env.bucket.transactions'), $this->execution_config)) {
            return json_decode((string)$s3->getObject(array(
                'Bucket' => config('env.bucket.transactions'),
                'Key' => $this->execution_config,
                'ResponseContentType' => 'application/json',
            ))['Body'], true);
        }
        return false;
    }

    /**
     * Where query based on filters selected by user
     * @param $subscriptions
     * @param $filters
     * @return null
     */
    public function setWhereQuery($subscriptions, $filters)
    {
        if (!empty($subscriptions)) {
            $this->whereModel = Transaction::from('transactions as t')->whereIn('subscription_id', $subscriptions);
        } else {
            $this->whereModel = Transaction::from('transactions as t')->whereIn('subscription_id', [0]);
        }
        $this->whereModel->join('test_cases AS tc', function ($join) {
            $join->on('tc.id', '=', 't.test_case_id');
        });
         $this->whereModel->join('test_cases_scenarios AS tcs', function ($join) {
            $join->on('tc.id', '=', 'tcs.test_case_id');
        });
        $this->whereModel->join('test_suites_scenarios AS tss', function ($join) {
            $join->on('tcs.test_suites_scenario_id', '=', 'tss.id');
        });

        if ($filters['organisation_id']) {
            $this->whereModel->whereRaw(sprintf(' subscription_id IN ( SELECT id FROM wp_organisations_subscriptions WHERE organisation_id = %d) ', $filters['organisation_id']));
        }
        if ($filters['product_id']) {
            $this->whereModel->where('product_id', $filters['product_id']);
        }
        if ($filters['test_case_id']) {
            $this->whereModel->where('tc.test_case_id', $filters['test_case_id']);
        }
        if ($filters['suite_minor_family_mark']) {
            $this->whereModel->where('suite_minor_family_mark', $filters['suite_minor_family_mark']);
        }
        if ($filters['subscription_id']) {
            $this->whereModel->where('subscription_id', $filters['subscription_id']);
        }
        if ($filters['date']) {
            $this->whereModel->whereRaw(" ( created_at > '" . date('Y-m-d H:i:s', getUTCTimeStamp($filters['date'])) . "' AND created_at <  '" . date('Y-m-d H:i:s', getUTCTimeStamp($filters['date'] . ' 23:59:59')) . "' ) ");
        }
        if ($filters['execution_id']) {
            $this->whereModel->where('execution_id', $filters['execution_id']);
        }
        if ($filters['test_outcome_status_id']) {
            $this->whereModel->where('test_outcome_status_id', $filters['test_outcome_status_id']);
        }
        if ($filters['audit_record']) {
            $this->whereModel->where('audit_record', $filters['audit_record'] == 'yes' ? true : false);
        }
        if ($filters['scenario_id']) {
            $this->whereModel
                ->where('tss.code', '=', filter_var($filters['scenario_id'], FILTER_SANITIZE_STRING));
        }
        return $this->whereModel;
    }

    /**
     * Process filters and configure where query
     * @param $subscriptions
     * @param $filters
     * @return array
     */
    public function processFilters($subscriptions, $filters)
    {
        $organisationSubscriptions = OrganisationSubscription::whereIn('ID', $this->setWhereQuery($subscriptions, $filters)->groupBy('subscription_id')->pluck('subscription_id'))->orderBy('nickname');
        $arr = [
            'subscription_id' => $organisationSubscriptions->get(),
            'product_id' => Product::whereIn('id', $this->setWhereQuery($subscriptions, $filters)->groupBy('product_id')->pluck('product_id'))->orderBy('full_name')->get(),
            'test_case_id' => LaravelTestCase::whereIn('id', $this->setWhereQuery($subscriptions, $filters)->groupBy('t.test_case_id')->pluck('t.test_case_id'))->orderBy('full_name')->get(),
            'suite_minor_family_mark' => LaravelTestSuite::whereIn('id', $this->setWhereQuery($subscriptions, $filters)->groupBy('suite_minor_family_mark')->pluck('suite_minor_family_mark'))->orderBy('full_name')->get(),
            'audit_record' => $this->setWhereQuery($subscriptions, $filters)->groupBy('audit_record')->pluck('audit_record'),
            'test_outcome_status_id' => TestOutcomeStatus::whereIn('id', $this->setWhereQuery($subscriptions, $filters)->groupBy('test_outcome_status_id')->pluck('test_outcome_status_id'))->orderBy('name')->get(),
            'scenario_id' => $this->setWhereQuery($subscriptions, $filters)
                ->select("tss.id", "tss.code")
                ->groupBy('tss.code')
                ->orderBy('tss.code')
                ->get(),
            'organisation_id' => Organisation::whereIn('id', $organisationSubscriptions->pluck('organisation_id'))->get(),
        ];
        return $arr;
    }

    /**
     * Get transactions data
     * @param $filters
     * @param int $page
     * @param int $totalPerPage
     * @return mixed
     */
    public static function getUserTransactionLog($filters, $totalPerPage = 25)
    {
        $transaction = new Transaction();
        return $transaction->setWhereQuery(self::getUserSubscriptions(), $filters)->select("*", "t.id")->groupBy('t.id')->orderBy('t.created_at', 'desc')->paginate($totalPerPage);
    }

    /**
     * Get Filters list
     * @param $filters
     */
    public static function getFilters($filters)
    {
        $transaction = new Transaction();
        return $transaction->processFilters(self::getUserSubscriptions(), $filters);
    }

    /**
     * Get array of subscriptions accessible by user
     * @return array
     */
    public static function getUserSubscriptions()
    {
        $subscriptions = [];
        array_walk(ct_get_user_viewable_subscriptions(Auth::user()->ID), function ($entry) use (&$subscriptions) {
            return $subscriptions[] = $entry->id;
        });
        return $subscriptions;
    }
}
