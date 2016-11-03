<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrganisationSubscription extends Model
{
    protected $table = 'wp_organisations_subscriptions';

    public $timestamps = false;

    protected $fillable = [
        'nickname', 'organisation_id', 'purchaser_id', 'status', 'user_id', 'suite_minor_family_mark', 'purchased_date'
    ];

    /**
     * Relation with organisation record
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function organisation()
    {
        return $this->belongsTo('\App\Organisation', 'wp_organisations');
    }

    public static function getUniqueSlug($value)
    {
        $slug = \Illuminate\Support\Str::slug($value);
        $slugCount = count(self::whereRaw("nickname REGEXP '^{$slug}(-[0-9]+)?$'")->get());

        return ($slugCount > 0) ? "{$slug}-{$slugCount}" : $slug;
    }
}
