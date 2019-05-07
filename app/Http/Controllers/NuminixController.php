<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Repositories\ProcessRepo;
use App\DataObject;

class NuminixController extends Controller
{

    public function index(Request $request)
    {
        $data = new DataObject;
        $states = [];

        $displayChart = false;
        $displayAnalysis = false;
        $noNewCustomers = false;

        if (strlen($request->startDate) > 0)
        {
            $processRepo =  new ProcessRepo();
            $data = $processRepo->ProcessDatas($request);
            $noNewCustomers = true;
        }

        $countries = DB::table('finaltable')
            ->select('customers_country')        
            ->distinct()
            ->pluck('customers_country')
            ->toArray();

        $minDate = DB::table('finaltable')
            ->min('date_purchased');

        $maxDate = DB::table('finaltable')
            ->max('date_purchased');

        if ($request->get('countryFilter') && $request->countryFilter != 'all') {
            $states = DB::table('finaltable')
                ->select('customers_state')
                ->where('customers_country', 'like', $request->countryFilter)
                ->distinct()
                ->pluck('customers_state')
                ->toArray();
        }

        if ($data->newCustomers > 0) {
            $displayChart = true;
            $displayAnalysis = true;
            $noNewCustomers = false;
        }

        return view('home', [
            'displayChart'      => $displayChart,
            'displayAnalysis'   => $displayAnalysis,
            'chart'             => $data->chart,
            'totalValue'        => $data->totalValue,
            'newCustomers'      => $data->newCustomers,
            'overallAverage'    => $data->overallAverage,
            'countries'         => $countries,
            'states'            => $states,
            'oldStartDate'      => $request->startDate,
            'oldEndDate'        => $request->endDate,
            'chartInterval'     => $request->chartInterval,
            'chartType'         => $request->chartType,
            'oldCountry'        => $request->countryFilter,
            'oldState'          => $request->stateFilter,
            'noNewCustomers'    => $noNewCustomers,
            'minDate'           => explode('-', $minDate)[0] . '-' . explode('-', $minDate)[1],
            'maxDate'           => explode('-', $maxDate)[0] . '-' . explode('-', $maxDate)[1]
        ]);
    }
}