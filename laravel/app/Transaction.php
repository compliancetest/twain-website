<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use UuidTrait;

    public $incrementing = false;

    protected $fillable = [
        'execution_id', 'test_case_id', 'audit_record'
    ];

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

}
