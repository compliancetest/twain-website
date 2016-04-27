<?php

namespace App;


use Aws\Laravel\AwsFacade;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Profile extends Model
{

    protected $table = 'wp_community_profile_instances';

    public $timestamps = [];

    protected $fillable = ['profile_name'];

    public function meta()
    {
        return $this->hasMany('App\ProfileMeta');
    }

    public function community()
    {
        return $this->belongsTo('\App\Community');
    }

    /**
     * Get Profile content from s3
     * @return array|mixed
     */
    public function getProfileFromS3()
    {
        $s3 = AwsFacade::createClient('s3');
        return json_decode((string)$s3->getObject(array(
            'Bucket' => 'data.twain.gosource.com.au',
            'Key' => 'profiles/user/' . $this->token . '.json',
            'ResponseContentType' => 'application/json',
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
            'ResponseContentDisposition' => 'attachment;filename="'.$this->profile_name.'"'
        ]);

        $request = $disk->getDriver()->getAdapter()->getClient()->createPresignedRequest($command, '+20 minutes');

        return (string)$request->getUri();
    }

    /**
     * Generate profile name from json data
     * @param $profileData
     * @return string
     */
    public function getVersion($profileData)
    {
        $version = ' v' . $profileData['Profile']['Version']['Major'] . '.' . $profileData['Profile']['Version']['Minor'];
        if (!empty($profileData['Profile']['Version']['Patch'])) {
            $version .= '.' . $profileData['Profile']['Version']['Patch'];
        }
        return $version;
    }

}
