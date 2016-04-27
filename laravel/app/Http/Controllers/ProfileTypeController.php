<?php

namespace App\Http\Controllers;

use App\Community;
use App\ProfileType;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Response;

class ProfileTypeController extends Controller
{
    public function viewprofiletype($communitySlug, $profileTypeId)
    {
        $community = Community::findBySlug($communitySlug);
        $profileType = ProfileType::find($profileTypeId);
        return view('pages.profiles.viewprofiletype')->with(['profileType' => $profileType, 'content' => base64_decode($profileType->schema), 'community' => $community])->render();
    }

    public function downloadprofiletype($communitySlug, $profileTypeId)
    {
        $profile = ProfileType::find($profileTypeId);
        $headers = [
            'Content-type' => 'application/json',
            'Content-Disposition' => sprintf('attachment; filename="%s.json"', $profile->getTitle())
        ];
        return Response::make(base64_decode($profile->schema), 200, $headers);
    }

    public function edit($communitySlug, $profileTypeId)
    {
        $profile = ProfileType::find($profileTypeId);
        return JsonResponse::create(['status' => 'success', 'schema' => base64_decode($profile->schema), 'id' => $profileTypeId], 200);
    }

    public function store($communitySlug, Request $request)
    {
        $comnunity = Community::findBySlug($communitySlug);
        $profileType = ProfileType::firstOrNew(['id' => $request->get('type_id')]);
        if($request->file('profile_type_file')){
            $profileType->schema = base64_encode(file_get_contents($request->file('profile_type_file')));
        } else {
            $profileType->schema = base64_encode(($request->get('profile_type_text')));
        }
        $profileTypeData = json_decode(base64_decode($profileType->schema), 1);
        if(!$profileType->id){
            $profileType->creator_id = Auth::user()->ID;
            $profileType->created_date = Carbon::now();
            $profileType->community_id = $comnunity->id;
        }
        $profileType->title = $profileTypeData['title'];
        $profileType->save();
        return JsonResponse::create(['status' => 'success']);
    }

    public function destroy($communitySlug, $profileTypeId)
    {
        ProfileType::find($profileTypeId)->delete();
        return JsonResponse::create(['status' => 'success']);
    }
}
