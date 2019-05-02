<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    // Require user to be authenticated to access any method.
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Redirect user based on their role.
    public function index(Request $request)
    {
        // Logic that determines where to send the user
        if ($request->user()->hasRole('manager')) {
            return redirect('/reporting');
        }
        return view('welcome');
    }

    // Only let managers on this page.
    public function manage(Request $request)
    {
        $request->user()->authorizeRoles(['manager']);
        return view('home');
    }
}
