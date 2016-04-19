<?php

namespace App\Http\Controllers;

use App\Community;
use App\CommunityMeta;
use App\Profile;
use App\ProfileMeta;
use App\ProfileType;
use App\Tag2Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Requests;
use Response;
use Illuminate\Support\Facades\Auth;

class ProfilesController extends Controller
{


    public function viewprofile($communitySlug, $profileId)
    {
        $community = Community::findBySlug($communitySlug);
        $profile = Profile::find($profileId);
        return view('pages.profiles.view')->with(['profile' => $profile, 'content' => json_encode($profile->getProfileFromS3()), 'community' => $community])->render();
    }

    public function viewprofiletype($communitySlug, $profileTypeId)
    {
        $community = Community::findBySlug($communitySlug);
        $profileType = ProfileType::find($profileTypeId);
        return view('pages.profiles.viewprofiletype')->with(['profileType' => $profileType, 'content' => base64_decode($profileType->schema), 'community' => $community])->render();
    }

    public function edit($communitySlug, $profileId, $profileTypeId)
    {
        $community = Community::findBySlug($communitySlug);
        $profile = Profile::find($profileId);
        $profileType = ProfileType::find($profileTypeId);
        return view('pages.profiles.editprofile')->with(['profileType' => $profileType, 'profile' => $profile, 'community' => $community])->render();
    }

    public function update($communitySlug, $profileId, Request $request)
    {
        $community = Community::findBySlug($communitySlug);
        $profile = Profile::find($profileId);
        if($community->isAdmin() || $profile->creator_id = Auth::user()->ID){

            $profileData = json_decode(stripslashes(urldecode($request->get('data'))),1);
            $profile->type_id = $request->get('profile_type_id');
            $profile->purpose = $profileData['Profile']['Purpose'];
            $profile->profile_description = $profileData['Profile']['Description'];
            $profile->profile_name = $profileData['Profile']['Title'];
            $profile->putToS3(json_encode($profileData));
            $profile->save();
            return JsonResponse::create(['status' => 'success'], 200);
        }
        return JsonResponse::create(['status' => 'error'], 403);
    }

    public function downloadprofiletype($communitySlug, $profileTypeId)
    {
        $profile = ProfileType::find($profileTypeId);
        $headers = [
            'Content-type' => 'application/json',
            'Content-Disposition' => sprintf('attachment; filename="%s"', $profile->title)
        ];
        return Response::make(base64_decode($profile->schema), 200, $headers);
    }

    /**
     * Copy profile
     * @param $communitySlug
     * @param $profileId
     * @return mixed
     */
    public function copy($communitySlug, $profileId)
    {
        $initialProfile = Profile::find($profileId);

        $newProfile = $initialProfile->replicate();

        $newProfile->token_original = $initialProfile->token;
        $newProfile->type = 'tester';
        $newProfile->token = sha1(time() . rand(0, 9999) . $initialProfile->type_id . $initialProfile->community_id);
        $s3Content = $initialProfile->getProfileFromS3();
        $newProfile->creator_id = Auth::user()->ID;
        $newProfile->putToS3(json_encode($s3Content));
        $newProfile->save();

        $profileType = ProfileType::find($initialProfile->type_id);
        $profileType->instances++;
        $profileType->save();


        $profile_meta = getProfileMetaData($s3Content);
        foreach ($profile_meta as $meta_key => $meta_value) {
            if (is_array($meta_key) || is_array($meta_value)) {
                continue;
            }

            $newProfile->meta()->create([
                    'meta_key'   => $meta_key,
                    'meta_value' => $meta_value
            ]);
        }

        return JsonResponse::create(['status' => 'success'], 200);
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
