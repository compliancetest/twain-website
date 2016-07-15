<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CommunityBackups extends Model
{
    use UuidTrait;

    protected $table = 'communities_profiles_backups';

    public $incrementing = false;

    protected $fillable = ['user_id', 's3_key'];

    /**
     * Relation with community
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function community()
    {
        return $this->belongsTo('App\Community');
    }

    /**
     * Get S3 download link
     * @return string
     */
    public function getS3Link()
    {
        $disk = Storage::disk('s3');
        $command = $disk->getDriver()->getAdapter()->getClient()->getCommand('GetObject', [
            'Bucket' => config('env.bucket.website'),
            'Key' => $this->s3_key,
            'ResponseContentDisposition' => 'attachment;filename="'.pathinfo($this->s3_key, PATHINFO_FILENAME).'"'
        ]);

        $request = $disk->getDriver()->getAdapter()->getClient()->createPresignedRequest($command, '+20 minutes');

        return (string)$request->getUri();
    }
}
