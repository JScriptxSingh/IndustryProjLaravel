
<!-- @section('js')

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>

@endsection -->


@extends('layouts.app')

@section('title')
Welcome
@endsection

@section('content')



        <div id="app">
            {!! $chart->container()!!}
        </div>


        <script src=https://cdnjs.cloudflare.com/ajax/libs/echarts/4.0.2/echarts-en.min.js charset=utf-8>
        </script>
        {!! $chart->script() !!}

@endsection
<!-- 
@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0/dist/Chart.min.js" integrity="sha256-Uv9BNBucvCPipKQ2NS9wYpJmi8DTOEfTA/nH2aoJALw=" crossorigin="anonymous"></script>
@endsection -->

