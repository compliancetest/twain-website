<?php

namespace App\Http\Controllers;

use App\Organisation;
use App\User;
use Illuminate\Support\Facades\Storage;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Http\Requests;

class UserController extends Controller
{

    public function organisation()
    {
        return view('pages.my.organisation.view');
    }

    public function view()
    {
        return view('pages.my.profile.view');
    }

    public function update(Requests\UserProfileRequest $request)
    {
        $user = Auth::user();
        $message = 'Changes saved successfully!';

        if ($request->get('password')) {
            if (!wp_check_password($request->get('current_password'), $user->user_pass, $user->ID)) {
                return response()->json(['Your current password is incorrect.'], 422);
            }
            wp_update_user(array('ID' => $user->id, 'user_pass' => $request->get('password')));
            $data = array(
                '[name]' => $request->get('first_name') . " " . $request->get('last_name'),
                '[username]' => $user->user_login,
                '[email]' => $user->user_email,
            );

            cp_send_email(array('name' => $data['[name]'], 'email' => $data['[email]']), 'password_changed', $data);
            cp_send_email_to_admin('password_changed_admin', $data);
        }

        $emailAlreadyUsed = User::where('user_email', $request->get('user_email'))->where('ID', '!=', $user->ID)->first();
        $emailAlreadyUsedInChanged = DB::table('wp_users_changes')->where('email_changed', $request->get('user_email'))->where('user_id', '!=', $user->ID)->first();
        if ($emailAlreadyUsed || $emailAlreadyUsedInChanged) {
            return response()->json(['This email address already exists!'], 422);
        }

        $email = $request->get('email');
        if ($email && $email != $user->user_email) {
            DB::table('wp_users_changes')->where('user_id', $user->ID)->delete();

            $verificationCode = md5($email);
            DB::table('wp_users_changes')->insert([
                'user_id' => $user->ID,
                'email_changed' => $email,
                'verification_code' => $verificationCode,
                'updated_date' => date('Y-m-d H:i:s')
            ]);

            $data = array(
                '[name]' => $user->getFullName(),
                '[username]' => $user->user_login,
                '[email]' => $email,
                '[link]' => getSiteUrl() . '?cp-action=email_activation&token=' . $verificationCode
            );

            cp_send_email(array('name' => $data['[name]'], 'email' => $data['[email]']), 'email_changed', $data);
            cp_send_email_to_admin('email_changed_admin', $data);
            $message = 'A confirmation email has been sent to updated email address. Please confirm your new email address using the link it contains.';
        }

        wp_update_user([
            'ID' => $user->ID,
            'first_name' => $request->get('first_name'),
            'last_name' => $request->get('last_name'),
            'display_name' => $request->get('first_name'),
        ]);
        update_user_meta($user->ID, 'dashboard_page_url', $request->get('first_page'));
        update_user_meta($user->ID, 'description', $request->get('biography'));
        update_user_meta($user->ID, 'phone_number', $request->get('phone_number'));
        update_user_meta($user->ID, 'timezone', $request->get('timezone_settings'));
        return response()->json(['message' => $message], 201);
    }

    public function uploadAvatar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }
        $s3Key = 'avatars/' . md5(get_current_user_id()) . '.' . $request->file('image')->clientExtension();
        Storage::put($s3Key, file_get_contents($request->file('image')->getPathname()));
        Auth::user()->meta()->updateOrCreate(['meta_key' => 'avatar_s3_path'], ['meta_value' => $s3Key]);
        return response()->json(['message' => 'Avatar was uploaded successfully.'], 201);
    }

    /**
     * Create new organisation
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createOrganisation(Request $request)
    {
        $user = Auth::user();
        if (isset($user->organisation[0])) {
            return response()->json(['You already organization member'], 422);
        }
        $validator = Validator::make($request->all(), [
            'organization_name' => 'required|unique:wp_organisations,organisation_name',
            'organization_website' => 'url',
            'organization_description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }


        $organisation = Organisation::create([
            'organisation_name' => $request->get('organization_name'),
            'organisation_website' => $request->get('organization_website'),
            'organisation_description' => $request->get('organization_description'),
            'organisation_key' => md5($request->get('organization_name') . mktime() . mt_rand(1, 100000)),
            'contact_first_name' => $user->getMetaByKey('first_name'),
            'contact_last_name' => $user->getMetaByKey('last_name'),
            'contact_email' => $user->user_email,
        ]);

        DB::table('wp_organisations_members')->insert([
            'organisation_id' => $organisation->id,
            'user_id' => $user->ID,
            'is_admin' => true,
            'created_date' => date('Y:m:d H:i:s'),
        ]);

        $email_data = array(
            '[requester_name]' => $user->getFullName(),
            '[requester_email]' => $user->user_email,
            '[organisation]' => $organisation->organisation_name,
            '[organisation_website]' => $organisation->organisation_website,
            '[organisation_description]' => $organisation->organisation_description,
        );

        cp_send_email_to_admin('send_organisation_signup_request_to_admin', $email_data);
        cp_send_email(['email' => $user->user_email, 'name' => $user->getFullName()], 'send_organisation_signup_request_to_user', $email_data);

        return response()->json(array('message' => 'Organisation successfully created '), 201);
    }

    /**
     * join existing organisation by key
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function joinOrganisation(Request $request)
    {
        $organisation = Organisation::where('organisation_key', $request->get('organization_key'))->first();
        if (!$organisation) {
            return response()->json(['Organization with provided key not found'], 422);
        }
        DB::table('wp_organisations_members')->insert([
            'organisation_id' => $organisation->id,
            'user_id' => Auth::user()->ID,
            'created_date' => date('Y:m:d H:i:s'),
        ]);

        return response()->json(array('message' => 'Successfully joined to organisation'), 201);
    }

    /**
     * leave organisation
     * @return \Illuminate\Http\JsonResponse
     */
    public function leaveOrganisation()
    {
        $user = Auth::user();

        DB::table('wp_organisations_members')->where([
            'organisation_id' => @$user->organisation[0]->id,
            'user_id' => $user->ID,
        ])->delete();
        return response()->json(array('message' => 'Successfully left organisation'), 201);

    }

    /**
     * save organisation
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveOrganisation()
    {
        return response()->json(array('message' => 'Successfully Saved!'), 201);
    }
}
