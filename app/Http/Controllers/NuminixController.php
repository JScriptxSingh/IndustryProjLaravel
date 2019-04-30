<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Charts\DefaultChart;
use App\Repositories\processRepo;

class NuminixController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $request->user()->authorizeRoles(['manager']);

        return view('home', [
            'displayChart' => false
        ]);
    }

    public function processData(Request $request)
    {
        $request->user()->authorizeRoles(['manager']);
        $processRepo =  new ProcessRepo();
        $data = $processRepo-> ProcessDatas($request);
        
        return view('home', [
            'displayChart' => true,
            'chart' => $data
        ]);
    }
}
