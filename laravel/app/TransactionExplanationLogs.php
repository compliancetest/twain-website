<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionExplanationLogs extends Model
{
    use UuidTrait;

    public $incrementing = false;

    protected $fillable = [
        'message', 'user_id', 'is_support'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transaction()
    {
        return $this->belongsTo('App\Transaction');
    }
}
