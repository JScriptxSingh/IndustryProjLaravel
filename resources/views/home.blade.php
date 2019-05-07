@extends('layouts.app')

@section('title')
    Welcome
@endsection

@section('content')

<div class="row">
    <form method="POST" action="/reporting" class="border border-primary rounded-lg col-11 col-sm-12 col-md-10 col-lg-8 mx-auto mb-4 p-4">
                @csrf
        <div class="row">
            <div class="form-group col-md-6">
                <label for="startDate">Start Date</label>
                @if ($oldStartDate != null)
                    <input id="startDate" type="month" class="form-control" name="startDate" min="{{ $minDate }}" max="{{ $maxDate }}" value="{{ $oldStartDate }}" onchange="setMinEndDate();" required autofocus>
                @else
                    <input id="startDate" type="month" class="form-control" min="{{ $minDate }}" max="{{ $maxDate }}" name="startDate" onchange="setMinEndDate();" required autofocus>
                @endif
            </div>
            
            <div class="form-group col-md-6">
                <label for="endDate">End Date</label>
                @if ($oldEndDate != null)
                    <input id="endDate" type="month" class="form-control" name="endDate" min="{{ $minDate }}" max="{{ $maxDate }}" value="{{ $oldEndDate }}" onchange="setMinEndDate();" required>
                @else
                    <input id="endDate" type="month" class="form-control" min="{{ $minDate }}" max="{{ $maxDate }}" name="endDate" onchange="setMinEndDate();" required>
                @endif
            </div>

            <div class="form-group col-md-6">
                <label for="countryFilter">Country</label>
                <select id="countryFilter" name="countryFilter" class="custom-select" onchange="submit();">
                    <option value="all" {{ $oldCountry == "all" ? "selected" : "" }}>All</option>
                    @foreach ($countries as $country)
                        @if (strlen($country) > 0)
                            <option value="{{ $country }}" {{ $oldCountry == $country ? "selected" : "" }}>{{ $country }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            
            <div class="form-group col-md-6">
                <label for="stateFilter">State</label>
                <select id="stateFilter" name="stateFilter" class="custom-select" {{ count($states) < 1 ? "disabled" : "" }}>
                    <option value="all" selected>All</option>
                    @foreach ($states as $state)
                        @if (strlen($state) > 0)
                            <option value="{{ $state }}" {{ $oldState == $state ? "selected" : "" }}>{{ $state }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-6">
                <label for="chartType">Chart Type</label>
                <select id="chartType" name="chartType" class="custom-select">
                    <option value="bar" {{ $chartType == "bar" ? "selected" : "" }}>Bar</option>
                    <option value="line" {{ $chartType == "line" ? "selected" : "" }}>Line</option>
                </select>
            </div>

            <div class="form-group col-md-6">
                <label for="chartInterval">Chart Interval</label>
                <select id="chartInterval" name="chartInterval" class="custom-select">
                    <option value="yearly"  {{ $chartInterval == "yearly" ? "selected" : "" }}>Yearly</option>
                    <option value="monthly" {{ $chartInterval == "monthly" ? "selected" : "" }}>Monthly</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="auto-col mx-auto"><button class="btn btn-outline-success px-5 text-uppercase" type="submit">Submit</button></div>
        </div>
    </form>

    @if($displayAnalysis)
        <div class="col-12 col-sm-12 col-md-10 col-lg-4 mx-auto">
            <div class="jumbotron px-2 py-1 pt-3 bg-white border border-primary mb-5 rounded-lg">
                <div class="container">
                    <h3 class="mb-0">
                        $ {{ number_format( floatval( $totalValue ), 2, '.', ',') }}
                    </h3>
                    <p class="lead">in total</p>

                    <h3 class="mb-0">
                        {{ $newCustomers }}
                    </h3>
                    <p class="lead">customers acquired</p>

                    <h3 class="mb-0">
                        $ {{ number_format( floatval( $overallAverage ), 2, '.', ',') }}
                    </h3>
                    <p class="lead">average per customer</p>
                </div>
            </div>
        </div>
    @endif

    </div>
    
    @if ($displayChart)
        <div class="myChart">{!! $chart->render() !!}</div>
    @endif
    
    @if ($noNewCustomers)
        <div class="col-12 col-sm-12 col-md-10 col-lg-8 mx-auto">
            <div class="jumbotron px-2 py-1 pt-3 bg-white border border-danger mb-5 rounded-lg">
                <div class="container">
                    <h3 class="mb-0">
                        $ {{ number_format( floatval( $totalValue ), 2, '.', ',') }}
                    </h3>
                    <p class="lead">No new customers found based on the dates input and filters provided.</p>
                </div>
            </div>
        </div>
    @endif

@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0/dist/Chart.min.js" integrity="sha256-Uv9BNBucvCPipKQ2NS9wYpJmi8DTOEfTA/nH2aoJALw=" crossorigin="anonymous"></script>

<script src="{{ asset('js/script.js') }}"></script>
@endsection