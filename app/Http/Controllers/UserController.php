<?php

namespace App\Http\Controllers;

use App\Facades\Strava;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::paginate();

        return view('users.index', compact('users'));    
    }

    public function profile()
    {
        $profile = Strava::profile();
        
        return $profile;
    }

    public function activity()
    {
        $activities = Strava::activities();

        return $activities;
    }
}
