@extends('layouts.app')

@section('title')
Welcome
@endsection

@section('content')
    <form method="POST" action="/processData" class="col-sm-12 col-md-10 col-lg-8">
                @csrf
        <div class="row">
            <div class="form-group col-md-5">
            <label for="startDate">Start Date</label>
            <input id="startDate" type="date" class="form-control" name="startDate" required autofocus>
            </div>
            
            <div class="form-group col-md-5">
            <label for="endDate">End Date</label>
            <input id="endDate" type="date" class="form-control" name="endDate" required>
            </div>
        
            <div class="form-group col-md-2 align-self-end"><button class="btn btn-outline-primary" type="submit">Submit</button></div>
        </div>
    </form>
    
    @if($displayChart != null)
        <canvas class="myChart" id="myChart"></canvas>
    @endif
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0/dist/Chart.min.js" integrity="sha256-Uv9BNBucvCPipKQ2NS9wYpJmi8DTOEfTA/nH2aoJALw=" crossorigin="anonymous"></script>
@endsection