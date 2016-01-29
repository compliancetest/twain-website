<?php

namespace App\Http\Controllers;

use App\Community;
use App\CommunityMembers;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class CommunityMembershipController extends Controller
{

    public function __construct()
    {
        $this->userId = get_current_user_id();
    }

    public function join($slug, $isChecked = false)
    {
        $community = Community::findBySlug($slug);
        $communityMeta = $community->getMeta();
        return view('pages.communities.popups.join', compact('community', 'communityMeta', 'isChecked'));
    }

    /**
     * Show 'leave community' confirmation dialog
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function confirmLeave($slug)
    {
        $community = Community::findBySlug($slug);
        return view('pages.communities.popups.leave', compact('community'));
    }

    /**
     * User confirmed community leave
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function leave($slug)
    {

        $community = Community::findBySlug($slug);
        $community->getMember(get_current_user_id())->delete();

        $user = get_userdata($this->userId);
        $emailData = array(
            '[community]' => $community->title,
            '[community_url]' => $community->getUrl(),
            '[name]' => cp_get_user_fullname($this->userId),
            '[email]' => $user->user_email,
            '[username]' => $user->user_login
        );

        $admins = $community->getAdmins();

        sendEmails($admins, 'member_leave_community_admin', $emailData);
        return Redirect::to('/communities');
    }

    public function requestMembership($slug)
    {
        $userId = get_current_user_id();
        $community = Community::findBySlug($slug);
        $membershipRecord = CommunityMembers::getUserRecord($community->id, $userId);
        if(!$membershipRecord){
            $community->members()->create(['user_id' => get_current_user_id()]);
            $user = get_userdata($userId);
            $user_organisation = get_user_meta ($userId, 'user_organisation', true);
            $emailData = array(
                '[name]' => cp_get_user_fullname($this->userId),
                '[community]' => $community->title,
                '[organisation]' => $user_organisation,
                '[username]' => $user->data->user_login,
                '[email]' => $user->data->user_email,
                '[community_url]' => $community->getUrl()
            );

            $admins = $community->getAdmins();

            sendEmails($admins, 'membership_request_received_admin', $emailData);
        }
        return Redirect::to('/communities');
    }
}
