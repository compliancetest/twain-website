<?php
namespace App\Api\Transformers;

use App\Profile;
use League\Fractal\TransformerAbstract;

class ProfileTransformer extends TransformerAbstract
{

    /**
     * Turn this item object into a generic array
     *
     * @return array
     */
    public function transform(Profile $profile)
    {
        return [
            'id' => (int)$profile->id,
            'type_name' => $profile->type_name,
            'profile_name' => $profile->profile_name,
            'profile_description' => $profile->profile_description,
            'purpose' => $profile->purpose,
            'token' => $profile->token,
            'content' => \S3Wrapper::getProfile($profile->token),
        ];
    }

}