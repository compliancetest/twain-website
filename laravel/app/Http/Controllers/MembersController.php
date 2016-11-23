<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Requests;

class MembersController extends Controller
{
    public function show($userSlug)
    {
        $user = User::where('user_nicename', $userSlug)->firstOrFail();
        $phoneNumber = $user->getMetaByKey('phone_number');
        $organisation = @$user->organisation[0];
        $isSupport = cp_is_customer_support_or_admin($user->ID);
        return view('pages.members.view', compact('user', 'phoneNumber', 'organisation', 'isSupport'));
    }
}
