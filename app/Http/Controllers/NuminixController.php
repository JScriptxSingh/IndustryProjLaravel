<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Charts\DefaultChart;

class NuminixController extends Controller
{
    public function index () {
        return view('home', [
            'displayChart' => false
        ]);
    }

    public function processData (Request $request) {
        $chart = new DefaultChart;

        $startDateSplit = explode('-', $request->startDate);
        $startDate = $startDateSplit[0] . '-' . $startDateSplit[1] . '-01';
        // echo $startDate . '<br>';

        $oneMonthAfterStart = $startDateSplit[0] . '-' . $startDateSplit[1] . '-31';

        $endDateSplit = explode('-', $request->endDate);
        $endDate = $endDateSplit[0] . '-' . $endDateSplit[1] . '-31';

        $startDates = [];
        $endDates = [];

        // for ($year = $startDateSplit[0]; $year <= $endDateSplit[0]; $year ++) {
        //     // for ($month = $startDateSplit)
        // }
        
        $priorCustomers = DB::table('finaltable')
            ->where('date_purchased', '<', $startDate)
            ->distinct()
            ->pluck('cust_id')
            ->toArray();
        
        $newCustomers = DB::table('finaltable')
            ->whereBetween('date_purchased', [$startDate, $oneMonthAfterStart])
            ->whereNotIn('cust_id', $priorCustomers)
            ->whereNotIn('orders_status', ['5', '8'])
            ->distinct()
            ->orderby('cust_id')
            ->pluck('cust_id')
            ->toArray();

        $newCustomerOrders = DB::table('finaltable')
            ->whereBetween('date_purchased', [$startDate, $endDate])
            ->whereIn('cust_id', $newCustomers)
            ->whereNotIn('orders_status', ['5', '8'])
            ->orderby('date_purchased', 'asc')
            ->pluck('orderid')
            ->toArray();

        // $orderskdjghvkd = DB::table('finaltable')
        //     ->whereIn('orderid', $newCustomerOrders)
        //     ->get();

        // foreach ($newCustomers as $aa) {
        //     echo $aa . '<br>';
        // }

        $totalAmount = DB::table('finaltable')
            ->whereIn('orderid', $newCustomerOrders)
            ->sum('ordertotal');

            echo $totalAmount;

        $totalTax = DB::table('finaltable')
            ->whereIn('orderid', $newCustomerOrders)
            ->sum('taxAmount');

        $totalShipping = DB::table('finaltable')
            ->whereIn('orderid', $newCustomerOrders)
            ->sum('shippingAmount');

        $finalAmount = $totalAmount - ($totalTax + $totalShipping);

        $lifetimeValue = $finalAmount / count($newCustomers);

        $chart->labels([$startDate . ' to ' . $endDate]);
        $chart->dataset('Lifetime Value', 'bar', [round($lifetimeValue, 2)]);

        return view('home', [
            'displayChart' => true,
            'chart' => $chart
        ]);
    }
}