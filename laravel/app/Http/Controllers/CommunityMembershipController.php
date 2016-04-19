<?php

namespace App\Http\Controllers;

use App\Community;
use App\CommunityMembers;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Laracasts\Flash\Flash;

class CommunityMembershipController extends Controller
{

    /**
     * User confirmed community leave
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function leave($slug)
    {

        $community = Community::findBySlug($slug);
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
        $community->getMember(get_current_user_id())->delete();

        Flash::success('You successfully left the community. ');

        return response()->json(array('success' => true));
    }

    public function requestMembership($slug)
    {
        $userId = get_current_user_id();
        $community = Community::findBySlug($slug);
        $membershipRecord = CommunityMembers::getUserRecord($community->id, $userId);
        $user = get_userdata($userId);
        $user_organisation = get_user_meta($userId, 'user_organisation', true);
        if(!$membershipRecord){
            if($community->visibility_status == 'private') {
                $community->members()->create(['user_id' => $userId]);
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
                Flash::success('Your membership request sent successfully. ');
            } else {
                $emailData = array(
                    '[community]' => $community->title,
                    '[community_url]' => $community->getUrl(),
                    '[name]' => cp_get_user_fullname($userId),
                    '[email]' => $user->data->user_email,
                    '[env]' => get_option('env'),
                    '[website_url]' => get_site_url(),
                    '[username]' => $user->data->user_login
                );
                sendEmails([['user_id' => $userId]], 'membership_request_approved', $emailData);
                $community->members()->create(['user_id' => $userId, 'is_confirmed' => 1]);
                Flash::success('Your membership request sent successfully. ');
            }
        }
        return Redirect::to('/communities');
    }

    public function acceptUser($communitySlug, Request $request)
    {
        $userId = $request->get('user_id');
        $community = Community::findBySlug($communitySlug);
        $community->members()->where(['user_id' => $request->get('user_id'), 'is_confirmed' => 0, 'community_id' => $community->id])->update(['is_confirmed' => true]);
        $data = [
            'community' => $community,
            'membershipRequests' => $community->getMembershipRequests()
        ];

        $user = get_userdata($userId);
        $emailData = array(
            '[community]' => $community->title,
            '[community_url]' => $community->getUrl(),
            '[name]' => cp_get_user_fullname($userId),
            '[email]' => $user->data->user_email,
            '[env]' => get_option('env'),
            '[website_url]' => get_site_url(),
            '[username]' => $user->data->user_login
        );

        $admins = $community->getAdmins();
        sendEmails([['user_id' => $userId]], 'membership_request_approved', $emailData);
        sendEmails($admins, 'membership_request_approved_admin', $emailData);

        $returnHTML = view('pages.communities.partials.show.admin-members')->with($data)->render();
        return response()->json(array('success' => true, 'html' => $returnHTML));
    }

    public function rejectUser($communitySlug, Request $request)
    {
        $community = Community::findBySlug($communitySlug);
        $community->members()->where(['user_id' => $request->get('user_id'), 'is_confirmed' => 0, 'community_id' => $community->id])->delete();
        $data = [
            'community' => $community,
            'membershipRequests' => $community->getMembershipRequests()
        ];

        $user = get_userdata($request->get('user_id'));
        $emailData = array(
            '[community]' => $community->title,
            '[community_url]' => $community->getUrl(),
            '[name]' => cp_get_user_fullname($request->get('user_id')),
            '[email]' => $user->data->user_email,
            '[env]' => get_option('env'),
            '[website_url]' => get_site_url(),
            '[username]' => $user->data->user_login
        );

        $admins = $community->getAdmins();
        sendEmails([['user_id' => $request->get('user_id')]], 'membership_request_rejected', $emailData);
        sendEmails($admins, 'membership_request_rejected_admin', $emailData);

        $returnHTML = view('pages.communities.partials.show.admin-members')->with($data)->render();
        return response()->json(array('success' => true, 'html' => $returnHTML));
    }

    public function changeRole($communitySlug, Request $request)
    {
        $community = Community::findBySlug($communitySlug);
        if($request->get('role') == 'remove'){
            foreach($request->get('users') as $user_id) {
                $user = get_userdata($user_id);
                $emailData = array(
                    '[name]' => cp_get_user_fullname($user_id),
                    '[email]' => $user->user_email,
                    '[website_url]' => get_site_url(),
                    '[env]' => get_option('env'),
                    '[username]' => $user->user_login,
                    '[community]' => $community->title,
                    '[community_url]' => $community->getUrl(),
                    '[settings_link]' => $community->getUrl(),
                    '[member_type]' => $request->get('role'),
                );
                $admins = $community->getAdmins();
                sendEmails([['user_id' => $user_id]], 'remove_member', $emailData);
                sendEmails($admins, 'remove_member_admin', $emailData);
            }
            $community->members()->whereIn('user_id',  $request->get('users'))->where(['is_confirmed' => 1, 'community_id' => $community->id])->delete();
        } else {

            if($request->get('role') == 'admin'){
                 foreach($request->get('users') as $user_id) {
                    $user = get_userdata($user_id);
                    $emailData = array(
                        '[name]' => cp_get_user_fullname($user_id),
                        '[email]' => $user->user_email,
                        '[website_url]' => get_site_url(),
                        '[env]' => get_option('env'),
                        '[username]' => $user->user_login,
                        '[community]' => $community->title,
                        '[community_url]' => $community->getUrl(),
                        '[settings_link]' => $community->getUrl(),
                        '[member_type]' => $request->get('role'),
                    );
                    $admins = $community->getAdmins();
                    sendEmails([['user_id' => $user_id]], 'member_promoted', $emailData);
                    sendEmails($admins, 'member_promoted_admin', $emailData);
                }
                $updateData = ['is_admin' => true];
            } else {
                foreach($request->get('users') as $user_id) {
                    $user = get_userdata($user_id);
                    $emailData = array(
                        '[name]' => cp_get_user_fullname($user_id),
                        '[email]' => $user->user_email,
                        '[website_url]' => get_site_url(),
                        '[env]' => get_option('env'),
                        '[username]' => $user->user_login,
                        '[community]' => $community->title,
                        '[community_url]' => $community->getUrl(),
                        '[settings_link]' => $community->getUrl(),
                        '[member_type]' => $request->get('role'),
                    );
                    $admins = $community->getAdmins();
                    sendEmails([['user_id' => $user_id]], 'member_demoted', $emailData);
                    sendEmails($admins, 'member_demoted_admin', $emailData);
                }
                $updateData = ['is_admin' => false];
            }
            $community->members()->whereIn('user_id',  $request->get('users'))->where(['is_confirmed' => 1, 'community_id' => $community->id])->update($updateData);
        }

        $data = [
            'community' => $community,
            'membershipRequests' => $community->getMembershipRequests()
        ];

        $returnHTML = view('pages.communities.partials.show.admin-members')->with($data)->render();
        return response()->json(array('success' => true, 'html' => $returnHTML));
    }
}
