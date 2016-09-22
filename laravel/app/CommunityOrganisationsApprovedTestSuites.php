<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommunityOrganisationsApprovedTestSuites extends Model
{
    use UuidTrait;

    protected $table = 'communities_organisations_approved_test_suites';

    public $incrementing = false;

    protected $fillable = [
        'organisation_id', 'approved_by', 'community_id', 'test_suite_id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function community()
    {
        return $this->belongsTo('App\Community');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function organisation()
    {
        return $this->belongsTo('App\Organisation');
    }
}
