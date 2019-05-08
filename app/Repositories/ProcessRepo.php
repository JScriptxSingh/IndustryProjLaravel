<?php
namespace App\Repositories;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\DataObject;

class ProcessRepo
{
    public function processDatas(Request $request)
    {
        // Creating php-Date variable for starting date using form input.
        $startDate = date_create(explode('-', $request->startDate)[0] . '-' . explode('-', $request->startDate)[1] . '-01');

        // Creating start month ending variable by performing php Date calculations.
        $firstMonthEnd = date_add(
            date_create(
                explode('-', $request->startDate)[0] . '-' . explode('-', $request->startDate)[1] . '-01'
                    ),
            date_interval_create_from_date_string('1 month')
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
            date_interval_create_from_date_string('0 days')
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

            if (intval(date_format($endDate, "m")) == 01 && intval(date_format($endDate, "d")) == 01) {
                array_pop($chartLabels);
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

            array_pop($chartLabels);
        }

        // Logic for setting country filters.
        $countryFilter = '%';
        if ($request->countryFilter != 'all') {
            $countryFilter = $request->countryFilter;
        }

        // Logic for setting state filters.
        $stateFilter = '%';
        if ($request->get('stateFilter')) {
            if ($request->stateFilter != 'all') {
                $stateFilter = $request->stateFilter;
            }
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
            ->where('customers_state', 'like', $stateFilter)
            ->distinct()
            ->orderby('cust_id')
            ->pluck('cust_id')
            ->toArray();

        // Defining array for storing calculations.
        $lifetimeValues = [];
        $customers = [];
        $orders = [];
        $totals = 0;

        // Canceled and returned orders
        $statusCanceled = 5;
        $statusReturned = 8;

        // Getting data from the database.
        if (count($newCustomers) > 0) {
            for ($i = 0 ; $i < count($dataStartings) ; $i ++) {
                $orderDetails = DB::table('finaltable')
                    ->select('ordertotal', 'taxAmount', 'shippingAmount', 'cust_id', 'orderid')
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
                array_push($customers, count(collect($orderDetails)->groupBy('cust_id')->pluck('cust_id')));
                array_push($orders, count(collect($orderDetails)->pluck('orderid')));
            }
        }

        // Create chart variable.
        $chart = app()->chartjs
            ->name('LifetimeValues')
            ->labels($chartLabels)
            ->type($request->chartType)
            ->size(['width' => 400, 'height' => 200])
            ->datasets([
                [
                    'label' => 'Customers',
                    'backgroundColor' => 'rgba(0, 255, 0, 0.5)',
                    'borderColor' => 'rgb(0, 255, 0)',
                    'pointHoverBackgroundColor'=> 'rgb(0, 255, 0)',
                    'data' => $customers
                ],
                [
                    'label' => 'Orders',
                    'backgroundColor' => 'rgba(0, 0, 255, 0.5)',
                    'borderColor' => 'rgb(0, 0, 255)',
                    'pointHoverBackgroundColor'=> 'rgb(0, 0, 255)',
                    'data' => $orders
                ],
                [
                    'label' => 'Average Lifetime Values ($)',
                    'backgroundColor' => 'rgba(255, 0, 0, 0.5)',
                    'borderColor' => 'rgb(255, 0, 0)',
                    'pointHoverBackgroundColor'=> 'rgb(255, 0, 0)',
                    'data' => $lifetimeValues
                ],
            ])
            ->options([
                'scales' => [
                    'xAxes' => [
                        [
                            'stacked' => false,
                            'scaleLabel' => [
                                'display' => true,
                                'labelString' => 'Timeline'
                            ]
                        ]
                    ]
                ]
            ]);

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
