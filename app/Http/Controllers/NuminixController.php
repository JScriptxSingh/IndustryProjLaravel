<?php

namespace App\Http\Controllers;
use Charts;
use App\Charts\MonthlyViews;
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
        // $chart->labels(['One', 'Two', 'Three', 'Four']);
        // $chart->dataset('My dataset', 'bar', [1, 2, 3, 4]);

        $startDateSplit = explode('-', $request->startDate);
        $startDate = $startDateSplit[0] . '-' . $startDateSplit[1] . '-01';

        $oneMonthAfterStart = $startDateSplit[0] . '-' . $startDateSplit[1] . '-31';

        $endDateSplit = explode('-', $request->endDate);
        $endDate = $endDateSplit[0] . '-' . $endDateSplit[1] . '-31';
        
        $priorCustomers = DB::table('finaltable')
            ->where('date_purchased', '<', $startDate)
            ->distinct()
            ->pluck('cust_id')
            ->toArray();
        
        $newCustomers = DB::table('finaltable')
            ->whereBetween('date_purchased', [$startDate, $oneMonthAfterStart])
            ->whereNotIn('cust_id', $priorCustomers)
            ->distinct()
            ->pluck('cust_id')
            ->toArray();

        $newCustomerOrders = DB::table('finaltable')
            ->whereBetween('date_purchased', [$startDate, $endDate])
            ->whereIn('cust_id', $newCustomers)
            ->orderby('date_purchased', 'asc')
            ->pluck('orderid')
            ->toArray();

        $totalAmount = DB::table('finaltable')
            ->whereIn('orderid', $newCustomerOrders)
            ->sum('ordertotal');

        $totalTax = DB::table('finaltable')
            ->whereIn('orderid', $newCustomerOrders)
            ->sum('taxAmount');

        $totalShipping = DB::table('finaltable')
            ->whereIn('orderid', $newCustomerOrders)
            ->sum('shippingAmount');

        $finalAmount = $totalAmount - ($totalTax + $totalShipping);

        $lifetimeValue = $finalAmount / count($newCustomers);

        echo $totalAmount . '<br />';
        echo $totalTax . '<br />';
        echo $totalShipping . '<br />';
        echo $finalAmount . '<br />';

        $chart->labels([$startDate . ' to ' . $endDate]);
        $chart->dataset('Lifetime Value', 'bar', [round($lifetimeValue, 2)]);

        return view('home', [
            'displayChart' => true,
            'chart' => $chart
        ]);
    }
}