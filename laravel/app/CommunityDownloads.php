<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommunityDownloads extends Model
{
    protected $table = 'communities_downloads';

    public $incrementing = false;

    protected $fillable = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function community()
    {
        return $this->belongsTo('App\Community');
    }

}
