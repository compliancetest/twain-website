<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrganisationMember extends Model
{
    protected $table = 'wp_organisations_members';

    /**
     * Relation to user table
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'ID');
    }

}
