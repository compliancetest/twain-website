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

    public function createOrganisation()
    {
        return response()->json(array('message' => 'Organisation successfully created '), 201);
    }

    public function joinOrganisation()
    {
        return response()->json(array('message' => 'Successfully joined to organisation'), 201);
    }

    public function leaveOrganisation()
    {
        return response()->json(array('message' => 'Successfully left organisation'), 201);
    }
}
