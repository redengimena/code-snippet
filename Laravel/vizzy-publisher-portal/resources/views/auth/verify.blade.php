{{-- Extends layout --}}
@extends('layout.fullwidth')



{{-- Content --}}
@section('content')
	<div class="col-md-6">
        <div class="authincation-content">
            <div class="row no-gutters">
                <div class="col-xl-12">
                    <div class="auth-form">
                    <div class="text-center mb-3">
                        <a href="/"><img src="{{ asset('images/logo-full.png') }}"  alt=""></a>
                    </div>
                        <h4 class="text-center mb-4 text-white">{{ __('Verify your email address') }}</h4>
                        @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('A fresh verification link has been sent to your email address.') }}
                        </div>
                        @endif
                        <p class="text-white">
                            {{ __('Please check your email for a verification link. To activate your Vizzy account, please click this link and login. If you did not receive an email, please check your Junk or Spam folders first, else request a new link to be sent below.') }}                            
                        </p>
                        <form method="POST" action="{{ route('verification.resend') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-block">{{ __('Request new verification link') }}</button>
                        </form>                            
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection