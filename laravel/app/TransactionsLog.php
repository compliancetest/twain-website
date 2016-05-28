<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionsLog extends Model
{

    use UuidTrait, TransactionS3LinkTrait;

    public $incrementing = false;


    protected $fillable = [
        'execution_id',
        'transaction_id'
    ];
}
