@extends('layouts.app')

@section('title')
Register
@endsection

@section('content')
<div class="container">
  <h3 class="display-4">Register</h3>
    <hr />
    <div class="row justify-content-center">
      <form method="POST" action="{{ route('register') }}" class="col-sm-10 col-md-8 col-lg-8 form">
                        @csrf

        <div class="form-row">
          <div class="form-group col-lg-5">
            <label for="firstName">First name</label>
            <input id="firstName" type="text" class="form-control{{ $errors->has('firstName') ? ' is-invalid' : '' }}" name="firstName" value="{{ old('firstName') }}" required autofocus>

            @if ($errors->has('firstName'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('firstName') }}</strong>
                </span>
            @endif
          </div>
          <div class="form-group col-lg-5">
            <label for="lastName">Last Name</label>&nbsp;
            <input id="lastName" type="text" class="form-control{{ $errors->has('lastName') ? ' is-invalid' : '' }}" name="lastName" value="{{ old('lastName') }}">

            @if ($errors->has('lastName'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('lastName') }}</strong>
                </span>
            @endif
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-lg-10">
            <label for="email">Email Address</label>
            <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>

            @if ($errors->has('email'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-lg-5">
            <label for="password">Password</label>&nbsp;
            <small id="passwordHelpInline" class="text-muted">(Must be 8 characters long)</small>
            <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

            @if ($errors->has('password'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif
          </div>
          <div class="form-group col-lg-5">
            <label for="cPassword">Confirm Password</label>
            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
          </div>
        </div>

        <button type="submit" class="btn btn-outline-success">Register</button>
      </form>
    </div>
  </div>
@endsection
