<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommunityMembers extends Model
{

    use UuidTrait;

    public $incrementing = false;

    protected $table = 'communities_members';

    protected $fillable = ['user_id', 'is_admin', 'is_confirmed'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function community()
    {
        return $this->belongsTo('App\Community');
    }

    /**
     * @param $communityId
     * @param $userId
     * @return mixed
     */
    public static function getUserRecord($communityId, $userId)
    {
        return CommunityMembers::where(['community_id' => $communityId, 'user_id' => $userId])->first();
    }

    public function users()
    {
        return $this->belongsTo('App\User');
    }
}
