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

        $countries = DB::table('finaltable')
            ->select('customers_country')        
            ->distinct()
            ->pluck('customers_country')
            ->toArray();

        return view('home', [
            'displayChart' => false,
            'displayAnalysis' => false,
            'countries' => $countries,
            'oldStartDate' => '',
            'oldEndDate' => '',
            'totalValue' => '',
            'newCustomers' => '',
            'overallAverage' => '',
            'chartInterval' => 'yearly',
            'chartType' => 'bar',
            'oldCountry' => 'all',
            'oldState' => 'all'
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
        
        return view('home', [
            'displayChart' => true,
            'displayAnalysis' => true,
            'chart' => $data->chart,
            'totalValue' => $data->totalValue,
            'newCustomers' => $data->newCustomers,
            'overallAverage' => $data->overallAverage,
            'countries' => $countries,
            'oldStartDate' => $request->startDate,
            'oldEndDate' => $request->endDate,
            'chartInterval' => $request->chartInterval,
            'chartType' => $request->chartType,
            'oldCountry' => $request->countryFilter,
            'oldState' =>$request->stateFilter
        ]);
    }
}