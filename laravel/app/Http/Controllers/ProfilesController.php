<?php

namespace App\Http\Controllers;

use App\Community;
use App\CommunityMeta;
use App\Profile;
use App\ProfileMeta;
use App\ProfileType;
use App\Tag2Item;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Requests;
use Ramsey\Uuid\Uuid;
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

    public function create($communitySlug, Request $request)
    {
        $community = Community::findBySlug($communitySlug);
        $profileType = false;
        if ($request->has('profile-type-id')) {
            $profileType = ProfileType::find($request->get('profile-type-id'));
        }
        return view('pages.profiles.create')->with(['community' => $community, 'profileType' => $profileType])->render();
    }

    public function edit($communitySlug, $profileId, $profileTypeId)
    {
        $community = Community::findBySlug($communitySlug);
        $profile = Profile::find($profileId);
        $profileType = ProfileType::find($profileTypeId);
        return view('pages.profiles.editprofile')->with(['profileType' => $profileType, 'profile' => $profile, 'community' => $community])->render();
    }

    public function save($communitySlug, Request $request)
    {
        $community = Community::findBySlug($communitySlug);
        if ($community->isAdmin()) {
            if ($request->file('create_profile_instance_file')) {
                $profileData = json_decode(file_get_contents($request->file('create_profile_instance_file')), 1);
            } else {
                $profileData = json_decode(stripslashes(urldecode($request->get('data'))), 1);
            }

            if (!$profileData) {
                return JsonResponse::create(['status' => 'error', 'message' => 'Invalid JSON!'], 422);
            }

            $profiletype = ProfileType::find($request->get('profile-type-id'));
            $profiletype->instances++;
            $profiletype->save();
            $profile = $community->profiles()->create(['profile_name' => $profileData['Profile']['Title']]);
            $profile->type = 'harness';
            $profile->type_id = $profiletype->id;
            $profile->type_name = $profiletype->getTitle();
            $profile->purpose = $profileData['Profile']['Purpose'];
            $profile->profile_description = $profileData['Profile']['Description'];
            $profile->profile_name = $profileData['Profile']['Title'] . $profile->getVersion($profileData);
            $profile->created_date = Carbon::now();
            $profile->creator_id = Auth::user()->ID;
            $profile->token = Uuid::uuid4();
            $profile->validation_status = 'valid';
            $profile->content_length = strlen(json_encode($profileData));
            $profile->profile_role = $profiletype->title;

            $profile->putToS3(json_encode($profileData, JSON_PRETTY_PRINT));
            $profile->save();
            return JsonResponse::create(['status' => 'success'], 200);
        }
        return JsonResponse::create(['status' => 'error'], 403);
    }

    public function update($communitySlug, $profileId, Request $request)
    {
        $community = Community::findBySlug($communitySlug);
        $profile = Profile::find($profileId);
        if ($community->isAdmin() || $profile->creator_id = Auth::user()->ID) {

            if ($request->file('profile_instance_file')) {
                $profileData = json_decode(file_get_contents($request->file('profile_instance_file')), 1);
            } else {
                $profileData = json_decode(stripslashes(urldecode($request->get('data'))), 1);
            }

            if (!$profileData) {
                return JsonResponse::create(['status' => 'error', 'message' => 'Invalid JSON!'], 422);
            }
            $profiletype = ProfileType::find($request->get('profile-type-id'));
            $profiletype->instances++;
            $profiletype->save();
            $profile->type_id = $profiletype->id;
            $profile->type_name = $profiletype->getTitle();
            $profile->profile_role = $profiletype->title;
            $profile->purpose = $profileData['Profile']['Purpose'];
            $profile->profile_description = $profileData['Profile']['Description'];
            $profile->profile_name = $profileData['Profile']['Title'] . $profile->getVersion($profileData);
            $profile->content_length = strlen(json_encode($profileData));
            $profile->putToS3(json_encode($profileData, JSON_PRETTY_PRINT));
            $profile->save();
            return JsonResponse::create(['status' => 'success'], 200);
        }
        return JsonResponse::create(['status' => 'error'], 403);
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

        if ($community->isAdmin() || $profile->creator_id == Auth::user()->ID) {

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
