<?php

namespace App\Http\Controllers;

use App\Community;
use App\Profile;
use App\ProfileMeta;
use App\ProfileType;
use App\Tag2Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Requests;

class ProfilesController extends Controller
{
    public function copy()
    {

    }

    /**
     * Delete profile
     * @param $communitySlug
     * @param $profileId
     * @return mixed
     */
    public function destroy($communitySlug, $profileId)
    {
        $community = Community::findBySlug($communitySlug);
        $profile = Profile::find($profileId);

        if($community->isAdmin() || $profile->creator_id == Auth::user()->ID) {

            $profileType = ProfileType::find($profile->type_id);
            $profileType->instances--;
            $profileType->save();

            ProfileMeta::where('profile_id', $profileId)->delete();

            Tag2Item::where(['item_id' => $profileId, 'item_type' => 'PROFILE'])->delete();

            $profile->delete();

            return JsonResponse::create(['status' => 'success'], 200);
        }
        return JsonResponse::create(['status' => 'forbidden'], 403);
    }
}
