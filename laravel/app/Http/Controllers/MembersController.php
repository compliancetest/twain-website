<?php

namespace App\Http\Controllers;

use App\Http\Requests;

class MembersController extends Controller
{
    public function show($userSlug)
    {
        return view('pages.members.view');
    }
}
