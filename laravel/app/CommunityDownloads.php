<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommunityDownloads extends Model
{

    use UuidTrait;

    public $incrementing = false;

    protected $table = 'communities_downloads';

    protected $fillable = ['version', 'description', 'license', 'token', 'size', 'location', 'title', 'version_description'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function community()
    {
        return $this->belongsTo('App\Community');
    }

    public function getLicense()
    {
        if($this->license){
            return $this->license;
        }
        $communityLicenseAgreement = $this->community->meta->keyBy('meta_key')->get('license_agreements')->meta_value;
        if($communityLicenseAgreement){
            return $communityLicenseAgreement;
        }
        return false;
    }

}
