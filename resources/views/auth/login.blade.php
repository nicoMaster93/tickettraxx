@extends('layouts.app')

@section('content')
    <div class="container-fluid over-h">
        <div class="row">
            <div class="col-md-9 p-0">
                <div class="img-full-h"></div>
            </div>
            <div class="col-md-3 bg-fondo p-0">
                <div class="text-center">
                    <img src="{{ asset('/imgs/theme/logo.png')}}" class="logo-inicio" />
                </div>
                <div class="contenedor-login">
                    <h2 class="letra-verde titulo">Administrator</h2>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="custom-input-login">
                            <label for="email" class="form-label">{{ __('User') }}</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="custom-input-login">
                            <label for="password" class="form-label">{{ __('Password') }}</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group text-left letra-blanco">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                <label class="form-check-label" for="remember">
                                    {{ __('Remember Me') }}
                                </label>
                            </div>                            
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-verde btn-login">
                                {{ __('Login') }}
                            </button>
                        </div>
                        <div class="form-group letra-blanco forgot-pass">
                            @if (Route::has('password.request'))
                                Forgot your password? 
                                <a class="btn btn-link-verde" href="{{ route('password.request') }}">
                                    {{ __('Click here') }}
                                </a>
                            @endif
                        </div>
                    </form>

                </div>
                

                    
            </div>
        </div>
    </div>

@endsection
