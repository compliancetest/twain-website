<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
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

    public function setWhereQuery($subscriptions, $filters)
    {
        if (!empty($subscriptions)) {
            $this->whereModel = DB::table('transactions')->whereIn('subscription_id',  $subscriptions);
        } else {
            $this->whereModel = DB::table('transactions')->whereIn('subscription_id',  [0]);
        }
        if ($filters['organisation_id']) {
            $this->whereModel->whereRaw(sprintf(' subscription_id IN ( SELECT id FROM wp_organisations_subscriptions WHERE organisation_id = %d) ', $filters['organisation_id'])) ;
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
            $this->whereModel->whereRaw(" ( t.updated_at > '" . date('Y-m-d H:i:s', getUTCTimeStamp($filters['date'])) . "' AND t.updated_at <  '" . date('Y-m-d H:i:s', getUTCTimeStamp($filters['date'] . ' 23:59:59')) . "' ) ");
        }
        if ($filters['outcome']) {
            $this->whereModel->where('test_outcome_status_id', $filters['outcome']);
        }
        if ($filters['audit'] == '1' || $filters['audit'] == '0') {
            $this->whereModel->where('audit_record', $filters['audit']);
        }
        if ($filters['scenario']) {
            $this->whereModel
                ->join('wp_posts AS p1', function ($join) {
                    $join->on('t.test_case_id', '=', 'p1.ID');
                })
                ->join('wp_postmeta AS pm1', function ($join) use ($filters){
                    $join->on('pm1.post_id', '=', 'p1.ID')
                        ->where('pm1.meta_key', 'LIKE', 'scenario_%')
                        ->where('pm1.meta_value', '=', filter_var($filters['scenario'], FILTER_SANITIZE_STRING));
                });
        }
        $this->whereModel->orderBy('updated_at', 'desc');
    }

    public static function getUserTransactionLog($filters, $page = 1, $totalPerPage = 10)
    {
        $subscriptions = [];
        array_walk(ct_get_user_viewable_subscriptions(Auth::user()->ID), function ($entry) use (&$subscriptions) {
            return $subscriptions[] = $entry->id;
        });
        $transaction = new Transaction();
        $transaction->setWhereQuery($subscriptions, $filters);
        return $transaction->whereModel->paginate($totalPerPage);
    }

}
