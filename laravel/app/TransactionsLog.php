<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionsLog extends Model
{

    use UuidTrait;

    public $incrementing = false;


    protected $fillable = [
        'execution_id',
        'transaction_id'
    ];
}
