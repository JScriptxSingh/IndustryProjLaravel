<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Charts\MonthlyViews;
use Charts;

class ChartsController extends Controller
{
    public function index(Request $request)
    {
        // Logic that determines where to send the user

        $chart = new MonthlyViews;
        $chart->labels(['One', 'Two', 'Three', 'Four']);
        $chart->dataset('My dataset', 'line', [1, 2, 3, 4]);
        $chart->dataset('My dataset 2', 'line', [4, 3, 2, 1]);

        return view('sampleChart', ['chart' => $chart]);
    }
}
