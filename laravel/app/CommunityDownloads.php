<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Get download license.
     * Community license will be returned in case if download license is empty
     * @return bool|mixed
     */
    public function getLicense()
    {
        if ($this->license) {
            return $this->license;
        }
        $communityLicenseAgreement = $this->community->meta->keyBy('meta_key')->get('license_agreements')->meta_value;
        if ($communityLicenseAgreement) {
            return $communityLicenseAgreement;
        }
        return false;
    }

    /**
     * Get S3 file link
     * @return string
     */
    public function getS3Link()
    {
        $disk = Storage::disk('s3');
        $command = $disk->getDriver()->getAdapter()->getClient()->getCommand('GetObject', [
            'Bucket' => config('env.bucket.website'),
            'Key' => $this->location,
            'ResponseContentDisposition' => 'attachment;filename="'.$this->title.'"'
        ]);

        $request = $disk->getDriver()->getAdapter()->getClient()->createPresignedRequest($command, '+20 minutes');

        return (string)$request->getUri();
    }

}
