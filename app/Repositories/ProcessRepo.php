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
        $firstMonthEnd = date_add(
            date_add(
                date_create(
                    explode('-', $request->startDate)[0] . '-' . explode('-', $request->startDate)[1] . '-01'
                    ),
                date_interval_create_from_date_string('1 month')
                ),
            date_interval_create_from_date_string('-1 day')
            );

        // Creating php-Date variable for ending date using form input.
        $tempEndDate = date_create(
            explode('-', $request->endDate)[0] . '-' . explode('-', $request->endDate)[1] . '-01'
        );
        $endDate = date_add(
            date_add(
                $tempEndDate,
                date_interval_create_from_date_string('1 month')
            ),
            date_interval_create_from_date_string('-1 day')
        );

        // Defining arrays for startings and endings of years.
        $dataStartings = [];
        $dataEndings = [];

        // Defining array for labels to be used in charts.
        $chartLabels = [];

        if ($request->chartInterval == 'yearly') {
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
            
                array_push($dataStartings, strval($year) . '-' . $startingMonth . '-01');
                array_push($dataEndings, strval($year) . '-' . $endingMonth . '-' . $endingMonthDay);
                array_push($chartLabels, strval($year));
            }
        } elseif ($request->chartInterval == 'monthly') {
            // Logic for getting startings and endings for months.
            for ($year = intval(date_format($startDate, "Y")); $year <= intval(date_format($endDate, "Y")); $year ++) {
                $startingMonth = 01;
                $endingMonth = 12;

                if ($year == intval(date_format($startDate, "Y"))) {
                    $startingMonth = intval(date_format($startDate, "m"));
                }

                if ($year == intval(date_format($endDate, "Y"))) {
                    $endingMonth = intval(date_format($endDate, "m"));
                }

                for ($month = $startingMonth; $month <= $endingMonth; $month ++) {
                    $tempDate = date_create($year . '-' . $month . '-01');

                    $monthEnd = date_add(
                        date_add(
                            $tempDate,
                            date_interval_create_from_date_string('1 month')
                        ),
                        date_interval_create_from_date_string('-1 day')
                    );

                    array_push($dataStartings, strval($year) . '-' . strval($month) . '-01');
                    array_push($dataEndings, strval($year) . '-' . strval($month) . '-' . date_format($monthEnd, "d"));
                    array_push($chartLabels, strval($year) . '-' . strval($month));
                }
            }
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
        $lifetimeValues = [];
        $totals = 0;

        // Canceled and returned orders
        $statusCanceled = 5;
        $statusReturned = 8;

        // Getting data from the database.
        if (count($newCustomers) > 0) {
            for ($i = 0 ; $i < count($dataStartings) ; $i ++) {
                $orderDetails = DB::table('finaltable')
                    ->select('ordertotal', 'taxAmount', 'shippingAmount', 'cust_id')
                    ->whereIn(
                        'orderid',
                        DB::table('finaltable')
                            ->whereBetween('date_purchased', [$dataStartings[$i], $dataEndings[$i]])
                            ->whereIn('cust_id', $newCustomers)
                            ->orderby('date_purchased', 'asc')
                            ->whereNotIn('orders_status', [$statusCanceled, $statusReturned])
                            ->pluck('orderid')
                            ->toArray()
                    )
                    ->get();

                // Calculating chart datas.
                $total = collect($orderDetails)->sum('ordertotal') - (collect($orderDetails)->sum('taxAmount') + collect($orderDetails)->sum('shippingAmount'));
                $totals += $total;
                array_push($lifetimeValues, round($total / count($newCustomers), 2));
            }
        }

        // Create chart variable.
        $chart = new DefaultChart;

        // Configuring chart object and assigning gathered data to it.
        $chart->labels($chartLabels)
              ->dataset('Average Lifetime Value ($)', $request->chartType, $lifetimeValues)
              ->options(['backgroundColor' => 'rgba(107, 185, 240, 0.6)'])
              ->options(['borderColor' => "#228CDB"])
              ->options(['pointHoverBackgroundColor'=> '#7fb800'])
              ->options(['hoverBorderColor' =>'rgba(25, 181, 254, 1)']);

        $overallAverage = 0;
        if (count($newCustomers) > 0) {
            $overallAverage = $totals / count($newCustomers);
        }

        // Create data object that will be returned with required data to controller
        $dataObject = new DataObject;

        $dataObject->chart = $chart;
        $dataObject->totalValue = $totals;
        $dataObject->newCustomers = count($newCustomers);
        $dataObject->overallAverage = $overallAverage;

        return $dataObject;
    }
}
