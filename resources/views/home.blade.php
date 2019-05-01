@extends('layouts.app')

@section('title')
    Welcome
@endsection

@section('content')

<div class="row">
    <form method="POST" action="/processData" class="border border-primary rounded-lg col-11 col-sm-12 col-md-10 col-lg-7 mx-auto mb-4 p-4">
                @csrf
        <div class="row">
            <div class="form-group col-md-6">
                <label for="startDate">Start Date</label>
                @if ($oldStartDate != null)
                    <input id="startDate" type="date" class="form-control" name="startDate" value="{{ $oldStartDate }}" required autofocus>
                @else
                    <input id="startDate" type="date" class="form-control" name="startDate" required autofocus>
                @endif
            </div>
            
            <div class="form-group col-md-6">
                <label for="endDate">End Date</label>
                @if ($oldEndDate != null)
                    <input id="endDate" type="date" class="form-control" name="endDate" value="{{ $oldEndDate }}" required>
                @else
                    <input id="endDate" type="date" class="form-control" name="endDate" required>
                @endif
            </div>

            <div class="form-group col-md-6">
                <label for="countryFilter">Filter by Country</label>
                <select id="countryFilter" name="countryFilter" class="custom-select">
                    <option value="all" selected>All</option>

                    @foreach ($countries as $country)
                        @if (strlen($country) > 0)
                            <option value="{{ $country }}">{{ $country }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-6">
                <label for="chartType">Chart Type</label>
                <select id="chartType" name="chartType" class="custom-select">
                    <option value="bar" selected>Bar</option>
                    <option value="line">Line</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="auto-col mx-auto"><button class="btn btn-outline-success px-5 text-uppercase" type="submit">Submit</button></div>
        </div>
    </form>

    @if($totalValue != null)
        <div class="col-12 col-sm-12 col-md-10 col-lg-4 mx-auto">
            <div class="jumbotron px-2 py-1 pt-3 mb-4 bg-white border border-primary rounded-lg">
                <div class="container d-flex flex-column">
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
        <div class="myChart">{!! $chart->container() !!}</div>
        {!! $chart->script() !!}
    @endif
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0/dist/Chart.min.js" integrity="sha256-Uv9BNBucvCPipKQ2NS9wYpJmi8DTOEfTA/nH2aoJALw=" crossorigin="anonymous"></script>
@endsection