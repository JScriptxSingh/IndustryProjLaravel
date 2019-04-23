@extends('layouts.app')

@section('title')
Login
@endsection

@section('content')
    <h1 class="display-4">
        Login
    </h1>

    <hr />
    
    <div class="row">
        <form method="POST" action="{{ route('login') }}" class="col-sm-10 col-md-8 col-lg-6 mr-auto form">
            @csrf
            <div class="form-group">
            <label for="email">Email address</label>
            <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus>

                @if ($errors->has('email'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
            <label for="password">Password</label>
            <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                @if ($errors->has('password'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="rememberMe">Remember Me</label>
            </div>

            <button type="submit" class="btn btn-outline-success">Login</button>

            @if (Route::has('password.request'))
                <a class="btn btn-link" href="{{ route('password.request') }}">
                    {{ __('Forgot Your Password?') }}
                </a>
            @endif
        </form>

        <div class="col-lg-4 col-md-8 col-sm-12 mx-auto row">
            <div class="card mt-4 mt-lg-0">
                <h5 class="card-header">Manager Login</h5>
                <div class="card-body">
                    <h5 class="card-title">manager@home.com</h5>
                    <p class="card-text">P@ssw0rd!</p>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <input type="hidden" name="email" id="email" value="manager@home.com">
                        <input type="hidden" name="password" id="password" value="P@ssw0rd!">
                        <button type="submit" class="btn btn-outline-primary">Click to login as Manager</button>
                    </form>
                </div>
            </div>
            <div class="card mt-4">
                <h5 class="card-header">Employee Login</h5>
                <div class="card-body">
                    <h5 class="card-title">employee@home.com</h5>
                    <p class="card-text">P@ssw0rd!</p>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <input type="hidden" name="email" id="email" value="employee@home.com">
                        <input type="hidden" name="password" id="password" value="P@ssw0rd!">
                        <button type="submit" class="btn btn-outline-primary">Click to login as Employee</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection