<?php
namespace app\Repositories;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Charts\DefaultChart;
use App\DataObject;

class ProcessRepo
{
    public function processDatas(Request $request)
    {
        // Creating php-Date variable for starting date using form input.
        $startDate = date_create(explode('-', $request->startDate)[0] . '-' . explode('-', $request->startDate)[1] . '-01');

        // Creating start month ending variable by performing php Date calculations.
        $firstMonthEnd = date_add(date_add(date_create(explode('-', $request->startDate)[0] . '-' . explode('-', $request->startDate)[1] . '-01'), date_interval_create_from_date_string('1 month')), date_interval_create_from_date_string('-1 day'));

        // Creating php-Date variable for ending date using form input.
        $tempEndDate = date_create(explode('-', $request->endDate)[0] . '-' . explode('-', $request->endDate)[1] . '-01');
        $endDate = date_add(date_add($tempEndDate, date_interval_create_from_date_string('1 month')), date_interval_create_from_date_string('-1 day'));

        // Defining arrays for startings and endings of years.
        $yearlyStartings = [];
        $yearlyEndings = [];

        // Defining array for labels to be used in charts.
        $chartLabels = [];

        // Logic for getting startings and endings for years.
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

        // Logic for setting country filters.
        $countryFilter = '';
        if ($request->countryFilter == 'all') {
            $countryFilter = '%';
        } else {
            $countryFilter = $request->countryFilter;
        }
    
        // Getting new customer ids from finaltable.
        // *** NOW USING SUB QUERY.
        $newCustomers = DB::table('finaltable')
            ->whereBetween('date_purchased', [date_format($startDate, "Y-m-d"), date_format($firstMonthEnd, "Y-m-d")])
            ->whereNotIn(
                'cust_id', 
                DB::table('finaltable')
                    ->where('date_purchased', '<', date_format($startDate, "Y-m-d"))
                    ->distinct()
                    ->pluck('cust_id')
                    ->toArray()
            )
            ->where('customers_country', 'like', $countryFilter)
            ->distinct()
            ->orderby('cust_id')
            ->pluck('cust_id')
            ->toArray();

        // Defining array for storing annual calculations.
        $yearlyLifetimes = [];
        $totals = 0;

        // Getting annual values from the database.
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

                // Calculating yearly totals.
                $yearlyFinal = collect($yearlyTotals)->sum('ordertotal') - (collect($yearlyTotals)->sum('taxAmount') + collect($yearlyTotals)->sum('shippingAmount'));

                $totals += collect($yearlyTotals)->sum('ordertotal');

                array_push($yearlyLifetimes, round($yearlyFinal / count($newCustomers), 2));
            }
        }

        // Create chart variable.
        $chart = new DefaultChart;

        // Configuring chart object and assigning gathered data to it.
        $chart->labels($chartLabels)
              ->dataset('Average Lifetime Value ($)', $request->chartType, $yearlyLifetimes)
              ->options(['backgroundColor' => 'rgba(107, 185, 240, 0.6)'])
              ->options(['borderColor' => "#228CDB"])
              ->options(['pointHoverBackgroundColor'=> '#7fb800'])
              ->options(['hoverBorderColor' =>'rgba(25, 181, 254, 1)']);

        // Create data object that will be returned with required data to controller
        $dataObject = new DataObject;

        $dataObject->chart = $chart;
        $dataObject->totalValue = $totals;
        $dataObject->newCustomers = count($newCustomers);

        return $dataObject;
    }
}
