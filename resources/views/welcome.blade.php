@extends('layouts.app')

@section('title')
Welcome
@endsection

@section('content')
<head>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="../js/animation.js"></script>


</head>
<!------ Include the above in your HEAD tag ---------->
<div class="banner">
</div>


<div class="content-wrapper">
  <img class="logo" src="../logo.svg"/>
  <div class="startButton">
  <div class="row">
        <div class="col-sm-3">
            <a href="{{ route('login') }}" type="button" class="startBtn btn-lg blue">
                <span class="fa fa-home"></span>Get Started
            </a>
        </div>
  </div>
</div>

@endsection