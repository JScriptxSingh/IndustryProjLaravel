@extends('layouts.app')

@section('title')
Welcome
@endsection
@section('content')
<div> 

{!! $chart ->render() !!}
</div>
<div>
<!-- {!! Charts::scripts() !!} -->
<!-- 
{!! $chart->script() !!} -->
</div>

<!-- <script src=""> -->