@extends('layouts.app')

@section('content')
    <h1 class="display-4">
        Login
    </h1>

    <hr />
    
    <div class="row">
      <form method="POST" action="{{ route('login') }}" class="col-sm-10 col-md-8 col-lg-6 form">
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
    </div>
@endsection
