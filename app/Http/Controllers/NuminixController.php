<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Charts\DefaultChart;

class NuminixController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index (Request $request) {
        $request->user()->authorizeRoles(['manager']);

        return view('home', [
            'displayChart' => false
        ]);
    }

    public function processData (Request $request) {
        $request->user()->authorizeRoles(['manager']);

        $chart = new DefaultChart;

        $startDateSplit = explode('-', $request->startDate);
        $startDate = $startDateSplit[0] . '-' . $startDateSplit[1] . '-01';

        $oneMonthAfterStart = $startDateSplit[0] . '-' . $startDateSplit[1] . '-31';

        $endDateSplit = explode('-', $request->endDate);
        $endDate = $endDateSplit[0] . '-' . $endDateSplit[1] . '-31';

        $monthlyStartDates = [];
        $monthlyEndDates = [];
        $chartLabels = [];

        for ($year = intval($startDateSplit[0]); $year <= intval($endDateSplit[0]); $year ++) {
            $monthStart = 0;
            $monthEnd = 0;

            if ($year == intval($startDateSplit[0])) {
                $monthStart = intval($startDateSplit[1]);
            } else {
                $monthStart = 01;
            }

            if ($year == intval($endDateSplit[0])) {
                $monthEnd = intval($endDateSplit[1]);
            } else {
                $monthEnd = 12;
            }

            for ($month = $monthStart; $month <= $monthEnd; $month ++) {
                array_push($monthlyStartDates, strval($year) . '-' . strval($month) . '-01');
                array_push($monthlyEndDates, strval($year) . '-' . strval($month) . '-31');
                array_push($chartLabels, strval($year) . '-' . strval($month));
            }
        }
        
        $priorCustomers = DB::table('finaltable')
            ->where('date_purchased', '<', $startDate)
            ->distinct()
            ->pluck('cust_id')
            ->toArray();
        
        $newCustomers = DB::table('finaltable')
            ->whereBetween('date_purchased', [$startDate, $oneMonthAfterStart])
            ->whereNotIn('cust_id', $priorCustomers)
            ->distinct()
            ->orderby('cust_id')
            ->pluck('cust_id')
            ->toArray();

        //     // echo count($newCustomers) . '<br>';

        // $newCustomerOrders = DB::table('finaltable')
        //     ->whereBetween('date_purchased', [$startDate, $endDate])
        //     ->whereNotIn('orders_status', [5, 8])
        //     ->whereIn('cust_id', $newCustomers)
        //     ->orderby('date_purchased', 'asc')
        //     ->pluck('orderid')
        //     ->toArray();

        // // echo count($newCustomerOrders) . '<br>';

        // // $orderskdjghvkd = DB::table('finaltable')
        // //     ->whereIn('orderid', $newCustomerOrders)
        // //     ->whereNotIn('orders_status', [5, 8])
        // //     ->orderby('orderid')
        // //     ->get();

        // // foreach ($orderskdjghvkd as $aa) {
        // //     // echo $aa->orderid . '<br>';
        // // }

        // $totalAmount = DB::table('finaltable')
        //     ->whereIn('orderid', $newCustomerOrders)
        //     ->sum('ordertotal');

        // $totalTax = DB::table('finaltable')
        //     ->whereIn('orderid', $newCustomerOrders)
        //     ->sum('taxAmount');

        // $totalShipping = DB::table('finaltable')
        //     ->whereIn('orderid', $newCustomerOrders)
        //     ->sum('shippingAmount');

        // $finalAmount = $totalAmount - ($totalTax + $totalShipping);

        // $lifetimeValue = $finalAmount / count($newCustomers);

        $monthlyLifetimeValues = [];

        for( $i = 0 ; $i < count($monthlyStartDates) ; $i ++) {
            $monthlyCustomerOrders = DB::table('finaltable')
            ->whereBetween('date_purchased', [$monthlyStartDates[$i], $monthlyEndDates[$i]])
            ->whereNotIn('orders_status', [5, 8])
            ->whereIn('cust_id', $newCustomers)
            ->orderby('date_purchased', 'asc')
            ->pluck('orderid')
            ->toArray();

        $monthlyAmount = DB::table('finaltable')
            ->whereIn('orderid', $monthlyCustomerOrders)
            ->sum('ordertotal');

        $monthlyTax = DB::table('finaltable')
            ->whereIn('orderid', $monthlyCustomerOrders)
            ->sum('taxAmount');

        $monthlyShipping = DB::table('finaltable')
            ->whereIn('orderid', $monthlyCustomerOrders)
            ->sum('shippingAmount');

        $monthlyFinal = $monthlyAmount - ($monthlyTax + $monthlyShipping);

        array_push($monthlyLifetimeValues, ($monthlyFinal / count($newCustomers)));

        // $lifetimeValue = $monthlyFinal / count($newCustomers);
        }

        // $chart->labels([$startDate . ' to ' . $endDate]);
        // $chart->dataset('Lifetime Value', 'bar', [round($lifetimeValue, 2)]);
        // $chart->labels($chartLabels);
        // $chart->dataset('Lifetime Values', 'line', $monthlyLifetimeValues);

        // $chart->labels([$startDate . ' to ' . $endDate]);
        // $chart->dataset('Lifetime Value', 'bar', [round($lifetimeValue, 2)]);
        $chart->labels($chartLabels);
/* PAT MODIFIED */
        $chart->dataset('Lifetime Values', 'line', $monthlyLifetimeValues)->options(['backgroundColor' => '#7fb800'])
                                                                          ->options(['borderColor' => "#01b8aa"])
                                                                          ->options(['pointHoverBackgroundColor'=> '#7fb800'])
                                                                          ->options(['hoverBorderColor' =>'#d76565']);

                                                                           
        return view('home', [
            'displayChart' => true,
            'chart' => $chart
        ]);



        return view('home', [
            'displayChart' => true,
            'chart' => $chart
        ]);
    }
}