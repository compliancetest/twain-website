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
        $processedTransactions = [];
        $transactions = Transaction::where([
            'product_id' => $productId,
            'test_suite_id' => $testSuiteId,
            'test_outcome_status_id' => TestOutcomeStatus::getIdByCode('PENDING')
        ])->get();
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
                    'extImageInfo' => $this->logs()->where(['execution_order' => ($log->execution_order + 1), 'data_argument_type' => 'DAT_EXTIMAGEINFO'])->first(),
                    'imageInfo' => $this->logs()->where(['execution_order' => ($log->execution_order + 2), 'data_argument_type' =>  'DAT_IMAGEINFO'])->first(),
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
            $this->whereModel = DB::table('transactions as t')->whereIn('subscription_id', $subscriptions);
        } else {
            $this->whereModel = DB::table('transactions as t')->whereIn('subscription_id', [0]);
        }

        $this->whereModel->join('wp_posts AS p1', function ($join) {
            $join->on('t.test_case_id', '=', 'p1.ID');
        })
        ->join('wp_postmeta AS pm1', function ($join) use ($filters) {
            $join->on('pm1.post_id', '=', 'p1.ID')
                ->where('pm1.meta_key', 'LIKE', 'scenario_%');
        })
         ->join('wp_test_suites_scenarios AS s', function ($join) {
            $join->on('s.id', '=', 'pm1.meta_value');
        });

        if ($filters['organisation_id']) {
            $this->whereModel->whereRaw(sprintf(' subscription_id IN ( SELECT id FROM wp_organisations_subscriptions WHERE organisation_id = %d) ', $filters['organisation_id']));
        }
        if ($filters['product_id']) {
            $this->whereModel->where('product_id', $filters['product_id']);
        }
        if ($filters['test_case_id']) {
            $this->whereModel->where('test_case_id', $filters['test_case_id']);
        }
        if ($filters['test_suite_id']) {
            $this->whereModel->where('test_suite_id', $filters['test_suite_id']);
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
                ->where('pm1.meta_value', '=', filter_var($filters['scenario_id'], FILTER_SANITIZE_STRING));
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
            'product_id' => Post::whereIn('ID', $this->setWhereQuery($subscriptions, $filters)->groupBy('product_id')->pluck('product_id'))->orderBy('post_title')->get(),
            'test_case_id' => Post::whereIn('ID', $this->setWhereQuery($subscriptions, $filters)->groupBy('test_case_id')->pluck('test_case_id'))->orderBy('post_title')->get(),
            'test_suite_id' => Post::whereIn('ID', $this->setWhereQuery($subscriptions, $filters)->groupBy('test_suite_id')->pluck('test_suite_id'))->orderBy('post_title')->get(),
            'audit_record' => $this->setWhereQuery($subscriptions, $filters)->groupBy('audit_record')->pluck('audit_record'),
            'test_outcome_status_id' => TestOutcomeStatus::whereIn('id', $this->setWhereQuery($subscriptions, $filters)->groupBy('test_outcome_status_id')->pluck('test_outcome_status_id'))->orderBy('name')->get(),
            'scenario_id' => $this->setWhereQuery($subscriptions, $filters)
                ->select("s.id", "s.code")
                ->groupBy('s.code')
                ->orderBy('s.code')
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
        return $transaction->setWhereQuery(self::getUserSubscriptions(), $filters)->select("*", "t.id")->groupBy('t.id')->orderBy('created_at', 'desc')->paginate($totalPerPage);
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
