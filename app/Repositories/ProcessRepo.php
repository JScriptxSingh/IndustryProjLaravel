<?php
namespace app\Repositories;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Charts\DefaultChart;

class ProcessRepo
{
    public function processDatas(Request $request)
    {
        $chart = new DefaultChart;

        $startDate = date_create(explode('-', $request->startDate)[0] . '-' . explode('-', $request->startDate)[1] . '-01');

        $firstMonthEnd = date_add(date_add(date_create(explode('-', $request->startDate)[0] . '-' . explode('-', $request->startDate)[1] . '-01'), date_interval_create_from_date_string('1 month')), date_interval_create_from_date_string('-1 day'));

        $tempEndDate = date_create(explode('-', $request->endDate)[0] . '-' . explode('-', $request->endDate)[1] . '-01');
        $endDate = date_add(date_add($tempEndDate, date_interval_create_from_date_string('1 month')), date_interval_create_from_date_string('-1 day'));

        $yearlyStartings = [];
        $yearlyEndings = [];
        $chartLabels = [];

        for ($year = intval(date_format($startDate, "Y")); $year <= intval(date_format($endDate, "Y")); $year ++) {
            $startingMonth = "01";
            $endingMonth = "12";
            $endingMonthDay = "31";

            if ($year == intval(date_format($startDate, "Y"))) {
                $startingMonth = date_format($startDate, "m");
            }

            if ($year == intval(date_format($endDate, "Y"))) {
                $endingMonth = date_format($endDate, "m");
                $endingMonthDay = date_format($endDate, "d");
            }
            
            array_push($yearlyStartings, strval($year) . '-' . $startingMonth . '-01');
            array_push($yearlyEndings, strval($year) . '-' . $endingMonth . '-' . $endingMonthDay);
            array_push($chartLabels, strval($year));
        }
    
        $priorCustomers = DB::table('finaltable')
        ->where('date_purchased', '<', date_format($startDate, "Y-m-d"))
        ->distinct()
        ->pluck('cust_id')
        ->toArray();
    
        $newCustomers = DB::table('finaltable')
        ->whereBetween('date_purchased', [date_format($startDate, "Y-m-d"), date_format($firstMonthEnd, "Y-m-d")])
        ->whereNotIn('cust_id', $priorCustomers)
        ->distinct()
        ->orderby('cust_id')
        ->pluck('cust_id')
        ->toArray();

            // echo count($newCustomers) . '<br>';

        // $newCustomerOrders = DB::table('finaltable')
        //     ->whereBetween('date_purchased', [$startDate, $endDate])
        //     ->whereNotIn('orders_status', [5, 8])
        //     ->whereIn('cust_id', $newCustomers)
        //     ->orderby('date_purchased', 'asc')
        //     ->pluck('orderid')
        //     ->toArray();

        // echo count($newCustomerOrders) . '<br>';

        // $orderskdjghvkd = DB::table('finaltable')
        //     ->whereIn('orderid', $newCustomerOrders)
        //     ->whereNotIn('orders_status', [5, 8])
        //     ->orderby('orderid')
        //     ->get();

        // foreach ($orderskdjghvkd as $aa) {
        //     // echo $aa->orderid . '<br>';
        // }

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

        $yearlyLifetimes = [];

        for ($i = 0 ; $i < count($yearlyStartings) ; $i ++) {
            $yearlyCustomerOrders = DB::table('finaltable')
                ->whereBetween('date_purchased', [$yearlyStartings[$i], $yearlyEndings[$i]])
                ->whereNotIn('orders_status', [5, 8])
                ->whereIn('cust_id', $newCustomers)
                ->orderby('date_purchased', 'asc')
                ->pluck('orderid')
                ->toArray();

            $yearlyTotals = DB::table('finaltable')
                ->select('ordertotal', 'taxAmount', 'shippingAmount')
                ->whereIn('orderid', $yearlyCustomerOrders)
                ->get();

            $yearlyFinal = collect($yearlyTotals)->sum('ordertotal') - (collect($yearlyTotals)->sum('taxAmount') + collect($yearlyTotals)->sum('shippingAmount'));

            array_push($yearlyLifetimes, round($yearlyFinal / count($newCustomers),2));
        }

        $chart->labels($chartLabels)
            ->options(['backgroundColor' => '#7fb800'])
            ->options(['borderColor' => "#01b8aa"])
            ->options(['pointHoverBackgroundColor'=> '#7fb800'])
            ->options(['hoverBorderColor' =>'#d76565'])
            ->dataset('Lifetime Values', 'bar', $yearlyLifetimes);

        return $chart;
    }
}