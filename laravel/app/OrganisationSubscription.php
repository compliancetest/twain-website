<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrganisationSubscription extends Model
{
    protected $table = 'wp_organisations_subscriptions';

    /**
     * Relation with organisation record
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function organisation()
    {
        return $this->belongsTo('\App\Organisation', 'wp_organisations');
    }
}
