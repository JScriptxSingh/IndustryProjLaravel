<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Charts\DefaultChart;
use App\Repositories\processRepo;
use Illuminate\Support\Facades\Config;

class NuminixController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $request->user()->authorizeRoles(['manager']);

        $countries = DB::table('finaltable')
            ->select('customers_country')        
            ->distinct()
            ->pluck('customers_country')
            ->toArray();

        return view('home', [
            'displayChart' => false,
            'countries' => $countries
        ]);
    }

    public function processData(Request $request)
    {
        $request->user()->authorizeRoles(['manager']);

        $countries = DB::table('finaltable')
            ->select('customers_country')        
            ->distinct()
            ->pluck('customers_country')
            ->toArray();

        $processRepo =  new ProcessRepo();
        $data = $processRepo->ProcessDatas($request);

        echo 'total = ' . config::get('overallAverage.total') . '<br>';
        echo 'newCu = ' . Config::get('overallAverage.newCustomers') . '<br>';
        echo 'avera = ' . config::get('overallAverage.total') / config::get('overallAverage.newCustomers');
        
        return view('home', [
            'displayChart' => true,
            'chart' => $data,
            'overallAverage' => config('overallAverage'),
            'countries' => $countries
        ]);
    }
}