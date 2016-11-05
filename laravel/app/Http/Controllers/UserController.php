<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class UserController extends Controller
{

    public function organisation()
    {
        return view('pages.my.organisation.view');
    }

    public function profile()
    {
        return view('pages.my.profile.view');
    }
}
