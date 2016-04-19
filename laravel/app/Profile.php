<?php

namespace App;


use Aws\Laravel\AwsFacade;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Profile extends Model
{

    protected $table = 'wp_community_profile_instances';

    public $timestamps = [];

    public function meta()
    {
        return $this->hasMany('App\ProfileMeta');
    }
    /**
     * Get Profile content from s3
     * @return array|mixed
     */
    public function getProfileFromS3()
    {
        $s3 = AwsFacade::createClient('s3');
        return json_decode((string) $s3->getObject(array(
            'Bucket' => 'data.twain.gosource.com.au',
            'Key' => 'profiles/user/' . $this->token . '.json',
            'ResponseContentType'        => 'application/json',
        ))['Body']);
    }

    /**
     * Put Profile content to s3
     * @return array|mixed
     */
    public function putToS3($data)
    {
        return Storage::put('profiles/user/' . $this->token . '.json', $data);
    }

    /**
     * Get S3 profile download link
     * @return string
     */
    public function getS3Link()
    {
        $disk = Storage::disk('s3');
        $command = $disk->getDriver()->getAdapter()->getClient()->getCommand('GetObject', [
            'Bucket' => 'data.twain.gosource.com.au',
            'Key' => 'profiles/user/' . $this->token . '.json',
            'ResponseContentDisposition' => 'attachment;'
        ]);

        $request = $disk->getDriver()->getAdapter()->getClient()->createPresignedRequest($command, '+20 minutes');

        return (string)$request->getUri();
    }

}
