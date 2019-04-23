<?php

namespace App\Http\Controllers;

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

        $dateSplit = explode('-', $request->startDate);

        $oneMonthAfterStart = $dateSplit[0] . '-' . $dateSplit[1] . '-31';
        
        $newCustomers = DB::table('orders')
            ->whereBetween('date_purchased', [$request->startDate, $oneMonthAfterStart])
            ->whereNotIn('customers_id', $priorCustomers)
            ->distinct()
            ->pluck('customers_id')
            ->toArray();

        $newCustomerOrders = DB::table('orders')
            ->whereBetween('date_purchased', [$request->startDate, $request->endDate])
            ->where('customers_id', $newCustomers)
            ->orderby('date_purchased', 'asc')
            ->distinct()
            ->get();

        echo count($newCustomerOrders);

        return view('home', [
            'displayChart' => true
        ]);
    }
}