<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{

    // Redirect user based on their role.
    public function index(Request $request)
    {
            return redirect('/reporting');
    }
}