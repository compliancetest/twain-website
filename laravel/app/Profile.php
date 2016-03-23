<?php

namespace App;


use Aws\Laravel\AwsFacade;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{

    protected $table = 'wp_community_profile_instances';

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

}
