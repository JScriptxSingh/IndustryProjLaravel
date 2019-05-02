<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Repositories\processRepo;
use App\DataObject;

class NuminixController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $displayChart = false;
        $displayAnalysis = false;

        $data = new DataObject;

        $states = [];

        if (strlen($request->startDate) > 0)
        {
            $processRepo =  new ProcessRepo();
            $data = $processRepo->ProcessDatas($request);
            $displayChart = true;
            $displayAnalysis = true;
        }

        $request->user()->authorizeRoles(['manager']);

        $countries = DB::table('finaltable')
            ->select('customers_country')        
            ->distinct()
            ->pluck('customers_country')
            ->toArray();

        if ($request->get('countryFilter') && $request->countryFilter != 'all') {
            $states = DB::table('finaltable')
                ->select('customers_state')
                ->where('customers_country', 'like', $request->countryFilter)
                ->distinct()
                ->pluck('customers_state')
                ->toArray();
        }

        return view('home', [
            'displayChart' => $displayChart,
            'displayAnalysis' => $displayAnalysis,
            'chart' => $data->chart,
            'totalValue' => $data->totalValue,
            'newCustomers' => $data->newCustomers,
            'overallAverage' => $data->overallAverage,
            'countries' => $countries,
            'states' => $states,
            'oldStartDate' => $request->startDate,
            'oldEndDate' => $request->endDate,
            'chartInterval' => $request->chartInterval,
            'chartType' => $request->chartType,
            'oldCountry' => $request->countryFilter,
            'oldState' =>$request->stateFilter
        ]);
    }
}