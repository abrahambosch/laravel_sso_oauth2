<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return [];
    }

    public function getUserProfile(Request $request)
    {
        $user = $request->user();    // get the logged in user.

        return $user;

        return [    // fields oauth2 profile will try to use.,
            'id'       => $user->id,
            'nickname' => $user->name,
            'name'     => $user->name,
            'avatar'   => null,
        ];
    }
}
