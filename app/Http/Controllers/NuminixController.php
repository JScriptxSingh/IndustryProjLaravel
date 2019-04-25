<?php

namespace App\Http\Controllers;
use Charts;
use App\Charts\MonthlyViews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NuminixController extends Controller
{
    public function index () {
        return view('home', [
            'displayChart' => false
        ]);
    }

    public function processData (Request $request) {
        // echo $request;
        
        $priorCustomers = DB::table('orders')
            ->where('date_purchased', '<', $request->startDate)
            ->distinct()
            ->pluck('customers_id')
            ->toArray();
            echo count($priorCustomers);
           
        $dateSplit = explode('-', $request->startDate);

        $oneMonthAfterStart = $dateSplit[0] . '-' . $dateSplit[1] . '-31';
        
        $newCustomers = DB::table('orders')
            ->whereBetween('date_purchased', [$request->startDate, $oneMonthAfterStart])
            ->whereNotIn('customers_id', $priorCustomers)
            ->distinct()
            ->pluck('customers_id')
            ->toArray();
        // $customer = DB:
        $newCustomerOrders = DB::table('orders')
            ->whereBetween('date_purchased', [$request->startDate, $request->endDate])
            ->where('customers_id', $newCustomers)
            ->orderby('date_purchased', 'asc')
            ->distinct()
            ->get();

        // foreach($newCustomers as $newOrder )
        // {
        //     $cust = DB::select('select * from orders where customers_id = ?', [$newOrder => customers_id] );
        //     echo $cust;
        
        // }
       
    //    $oneCust = DB::table('orders')
    //              ->select('customers_id')
    //              ->where('customers_id',$newCustomerOrders)
    //              ->get();
    //     echo   $oneCust ;    
            // foreach($newCustomerOrders as $newOrder )
            // {
            //   echo $newOrder -> 
            // }
      
        echo count($newCustomerOrders);

        $chart = new MonthlyViews;
        $chart->labels(['Jan', 'Feb', 'Mar', 'Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']);
        $chart->dataset('My dataset', 'line', [1, $newCustomerOrders, 3, 4]);
        $chart->dataset('My dataset 2', 'line', [4, 3, 2, 1]);

        return view('sampleChart', ['chart' => $chart]);

        // return view('home', [
        //     'displayChart' => true
        // ]);
    }
}