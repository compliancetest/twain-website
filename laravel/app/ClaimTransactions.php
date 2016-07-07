<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClaimTransactions extends Model
{
    use UuidTrait;

    protected $fillable = [
        'transaction_id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function claim()
    {
        return $this->belongsTo('App\Claim');
    }
}
