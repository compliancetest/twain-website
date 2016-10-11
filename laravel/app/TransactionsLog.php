<?php

namespace App;

use Aws\Laravel\AwsFacade;
use Illuminate\Database\Eloquent\Model;

class TransactionsLog extends Model
{

    use UuidTrait, TransactionS3LinkTrait;

    public $incrementing = false;

    protected $fillable = [
        'execution_id',
        'transaction_id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transaction()
    {
        return $this->belongsTo('App\Transaction');
    }

    /**
     * Get output data from s3
     * @return array
     */
    public function getOutput()
    {
        if(!empty($this->log_output)) {
            $s3 = AwsFacade::createClient('s3');
            return (array)json_decode((string)$s3->getObject(array(
                'Bucket' => config('env.bucket.transactions'),
                'Key' => $this->log_output,
            ))['Body'], true);
        }
        return false;
    }
}
