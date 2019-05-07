@extends('layouts.landingpage')

@section('title')
Welcome
@endsection

@section('content')
<div class="banner">
</div>

<div class="content-wrapper">
  <img class="logo" src="../logo.svg"/>
  <h1 class="landingTitle focus-in-contract-bc">Numinix</h1>
  <div class="startButton">
  <div class="row">
        <div class="col-sm-3">
            <a href="/reporting" type="button" class="startBtn btn-lg blue">
                <span class="fa fa-home"></span>Get Started
            </a>
        </div>
  </div>
</div>

@endsection