<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommunityInvitation extends Model
{
    use UuidTrait;

    public $incrementing = false;

    protected $fillable = [
        'first_name', 'last_name', 'invitation_email', 'status', 'invited_by_user_id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function community()
    {
        return $this->belongsTo('\App\Community');
    }
}
