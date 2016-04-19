<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProfileType extends Model
{
    protected $table = 'wp_community_profile_types';

    public $timestamps = [];

    public function getTitle()
    {
        $array = json_decode(base64_decode($this->schema), true);
        $title = $array['title'] .' v'.$array['Version']['Major'].'.'.$array['Version']['Minor'] ;
        if(!empty($array['Version']['Patch'])){
            $title .= '.'.$array['Version']['Patch'];
        }
        return $title;
    }
}
