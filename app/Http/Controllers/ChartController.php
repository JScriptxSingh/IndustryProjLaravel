<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Charts;

class ChartController extends Controller
{
    public function index()
    {
     $chartt = [1,2,3]; 
      $charts = Charts::new('line','heighcharts')
              ->setTitle("Report----- ")
              ->setLabels(["Customer","product"])
              ->setValues([100,50,25])
              ->setElementLabel("Total User");
                //return ("hello");
              return view('chart', ['chartt' => $chart]);
              // return view('chart', ['chart' => $charts]);
    }
}
