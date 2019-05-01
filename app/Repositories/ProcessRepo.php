<?php
namespace app\Repositories;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Charts\DefaultChart;
use Illuminate\Support\Facades\Config;

class ProcessRepo
{
    public function processDatas(Request $request)
    {
        $chart = new DefaultChart;

        //$startDate = date_create(explode('-', $request->startDate)[0] . '-' . explode('-', $request->startDate)[1] . '-01');

        $startDate = date_create(explode('-', $request->startDate)[0] . '-' . explode('-', $request->startDate)[1] . '-' . explode('-', $request->startDate)[2]);

        //$firstMonthEnd = date_add(date_add(date_create(explode('-', $request->startDate)[0] . '-' . explode('-', $request->startDate)[1] . '-01'), date_interval_create_from_date_string('1 month')), date_interval_create_from_date_string('-1 day'));

        $firstMonthEnd = date_add(date_add(date_create(explode('-', $request->startDate)[0] . '-' . explode('-', $request->startDate)[1] . '-' . explode('-', $request->startDate)[2]), date_interval_create_from_date_string('1 month')), date_interval_create_from_date_string('0 day'));

        // $tempEndDate = date_create(explode('-', $request->endDate)[0] . '-' . explode('-', $request->endDate)[1] . '-01');
        // $endDate = date_add(date_add($tempEndDate, date_interval_create_from_date_string('1 month')), date_interval_create_from_date_string('-1 day'));

        $tempEndDate = date_create(explode('-', $request->endDate)[0] . '-' . explode('-', $request->endDate)[1] . '-' . explode('-', $request->endDate)[2]);
        $endDate = date_add(date_add($tempEndDate, date_interval_create_from_date_string('1 month')), date_interval_create_from_date_string('0 day'));

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

        $countryFilter = '';
        if ($request->countryFilter == 'all') {
            $countryFilter = '%';
        } else {
            $countryFilter = $request->countryFilter;
        }
    
        $newCustomers = DB::table('finaltable')
            ->whereBetween('date_purchased', [date_format($startDate, "Y-m-d"), date_format($firstMonthEnd, "Y-m-d")])
            ->whereNotIn('cust_id', $priorCustomers)
            ->where('customers_country', 'like', $countryFilter)
            ->distinct()
            ->orderby('cust_id')
            ->pluck('cust_id')
            ->toArray();

            //echo implode(",", $newCustomers);
            //echo implode(",", $priorCustomers);

        $yearlyLifetimes = [];

        $totals = 0;

        if (count($newCustomers) > 0) {
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

                //$totals += collect($yearlyTotals)->sum('ordertotal');
                $totals += $yearlyFinal;

                array_push($yearlyLifetimes, round($yearlyFinal / count($newCustomers), 2));
            }
        }

        Config::set('overallAverage.total', $totals);
        Config::set('overallAverage.newCustomers', count($newCustomers));

        $chart->labels($chartLabels)
              ->dataset('Average Lifetime Value ($)', $request->chartType, $yearlyLifetimes)
              ->options(['backgroundColor' => 'rgba(107, 185, 240, 0.6)'])
              ->options(['borderColor' => "#228CDB"])
              ->options(['pointHoverBackgroundColor'=> '#7fb800'])
              ->options(['hoverBorderColor' =>'rgba(25, 181, 254, 1)']);

        return $chart;
    }
}
