<?php

namespace App\Http\Controllers;

use App\Community;
use App\CommunityInvitation;
use App\CommunityMembers;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use User\User;

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

        addMessage('You successfully left the community. ');

        return response()->json(array('success' => true));
    }

    public function requestMembership($slug)
    {
        $userId = get_current_user_id();
        $community = Community::findBySlug($slug);
        $membershipRecord = CommunityMembers::getUserRecord($community->id, $userId);
        $user = get_userdata($userId);
        $user_organisation = get_user_meta($userId, 'user_organisation', true);
        if (!$membershipRecord) {
            if ($community->visibility_status == 'private') {
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
                addMessage('Your membership request sent successfully. ');
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
                addMessage('Your membership request sent successfully. ');
            }
        }
        return Redirect::to(getSiteUrl() . '/communities');
    }

    /**
     * Accept user's membership request
     * @param $communitySlug
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Reject user's membersip request
     * @param $communitySlug
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Update user's role
     * @param $communitySlug
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeRole($communitySlug, Request $request)
    {
        $community = Community::findBySlug($communitySlug);
        $isAdmin = $community->isAdmin();

        if ($request->get('role') == 'remove') {
            if ($isAdmin){
                foreach ($request->get('users') as $user_id) {
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
                $community->members()
                    ->whereIn('user_id', $request->get('users'))
                    ->where(['is_confirmed' => 1, 'community_id' => $community->id])
                    ->where('user_id', '!=', Auth::user()->ID)
                    ->delete();
            }
        } else {
            if ($request->get('role') == 'admin') {
                if ($isAdmin) {
                    foreach ($request->get('users') as $user_id) {
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
                    $updateData = ['is_admin' => true, 'is_mod' => false];
                }

            } elseif ($request->get('role') == 'mod') {
                foreach ($request->get('users') as $user_id) {
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
                $updateData = ['is_mod' => true, 'is_admin' => false];
            } else {
                if ($isAdmin) {
                    foreach ($request->get('users') as $user_id) {
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
                    $updateData = ['is_admin' => false, 'is_mod' => false];
                }
            }
            $community->members()
                ->whereIn('user_id', $request->get('users'))
                ->where(['is_confirmed' => 1, 'community_id' => $community->id])
                ->where('user_id', '!=', Auth::user()->ID)
                ->update($updateData);
        }

        $data = [
            'community' => $community,
            'membershipRequests' => $community->getMembershipRequests(),
            'isAdmin' => $isAdmin,
        ];

        $returnHTML = view('pages.communities.partials.show.admin-members')->with($data)->render();
        return response()->json(array('success' => true, 'html' => $returnHTML));
    }

    /**
     * Invite user to community feature handler
     * @param $communitySlug
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function inviteUser($communitySlug, Request $request)
    {
        $validator = Validator::make($request->all(),
            ['user_email' => 'required|email']
        );
        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        $community = Community::findBySlug($communitySlug);
        $admins = $community->getAdmins();

        $userEmail = $request->get('user_email');
        $user = \App\User::where(['user_email' => $userEmail])->first();

        $emailData = [
            '[email]' => $userEmail,
            '[website_url]' => getSiteUrl(),
            '[env]' => get_option('env'),
            '[community]' => $community->title,
            '[community_url]' => $community->getUrl(),
        ];

        if (!$user) {

            $checkAlreadyInvited = $community->invitations()->where(['invitation_email' => $userEmail])->first();
            if ($checkAlreadyInvited) {
                if($checkAlreadyInvited->status == 1) {
                    return response()->json(array('User already invited, but not registered yet'), 422);
                } else {
                    return response()->json(array('User already invited, but registered with another email'), 422);
                }
            }

            $data = $request->all();
            $data['invited_by_user_id'] = Auth::user()->ID;
            $data['invitation_email'] = $userEmail;
            $data['status'] = 1;
            $invitation = $community->invitations()->create($data);

            //feature for wordpress superadmin - he can invite and register user automatically

            if ($request->get('register_automatically') == 1) {
                $password = Str::quickRandom(12);
                $userId = wp_create_user(explode('@', $userEmail)[0], $password, $userEmail);
                add_user_meta($userId, 'fill_profile_notification', 'yes');
                $community->members()->create(['user_id' => $userId, 'is_confirmed' => true]);

                $emailData['[password]'] = $password;

                $invitation->status = 0;
                $invitation->save();

                sendEmails([['user_id' => $userId]], 'membership_member_invited_registered', $emailData);
                sendEmails($admins, 'membership_member_invited_admin_registered', $emailData);
            } else {
                $userName = $userEmail;
                $emailData['[name]'] = '';
                if (!empty($request->get('first_name')) && !empty($request->get('last_name'))) {
                    $userName = $request->get('first_name') . ' ' . $request->get('last_name');
                    //this used to process 'Hi[name],' if first/last name not provided
                    $emailData['[name]'] = ' ' . $userName;
                }

                $emailData['[registration_link]'] = getSiteUrl() . '?GUID=' . $invitation->id;

                cp_send_email(array('name' => $userName, 'email' => $userEmail), 'membership_member_invited', $emailData);
                sendEmails($admins, 'membership_member_invited_admin', $emailData);

            }
            return response()->json(array('message' => 'User invited successfully!', 'data' => $invitation), 201);

        } else {
            $membershipRecord = CommunityMembers::getUserRecord($community->id, $user->ID);
            if ($membershipRecord) {
                return response()->json(array('User already member'), 422);
            } else {
                $community->members()->create(['user_id' => $user->ID, 'is_confirmed' => true]);

                $emailData['[name]'] = cp_get_user_fullname($user->ID);
                sendEmails([['user_id' => $user->ID]], 'membership_existing_member_invited', $emailData);
                sendEmails($admins, 'membership_existing_member_invited_admin', $emailData);

                return response()->json(array(['message' => 'User was added to community successfully!', 'data' => []]), 201);
            }
        }
    }
}
