{{-- Extends layout --}}
@extends('layout.fullwidth')


@section('content')
<div class="col-md-6">
    <div class="authincation-content">
        <div class="row no-gutters">
            <div class="col-xl-12">
                <div class="auth-form">
                <div class="text-center mb-3">
                    <a href="/"><img src="{{ asset('images/logo-full.png') }}"  alt=""></a>
                </div>
                    <h4 class="text-center mb-4 text-white">{{ __('Confirm Password') }}</h4>
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    <p class="text-white">{{ __('Please confirm your password before continuing.') }}</p>
                    <form method="POST" action="{{ route('password.confirm') }}">
                        @csrf
                        <div class="form-group">
                            <label class="text-white"><strong>{{ __('Password') }}</strong></label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-row d-flex justify-content-end mb-2">                            
                            <div class="form-group">
                                <a class="text-white" href="{{ route('password.request') }}">Forgot Password?</a>
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-block">{{ __('Confirm Password') }}</button>
                        </div>                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
