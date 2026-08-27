@php
    use App\Helpers\Helper;$configData = Helper::applClasses();
@endphp

@extends('layouts/fullLayoutMaster')

@section('title', __('locale.auth.forgot_password'))

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/authentication.css')) }}">
    {{-- @if(config('no-captcha.login'))
        {!! RecaptchaV3::initJs() !!}
    @endif --}}

    @include('auth._brand-style')
@endsection

@section('content')

    <div class="auth-wrapper auth-cover">
        <div class="auth-inner row m-0">
            <!-- Brand logo-->
            <a class="brand-logo" href="{{route('login')}}">
                <img src="{{asset(Helper::app_config('app_logo'))}}" alt="{{config('app.name')}}" style="max-width: 210px;"/>
            </a>
            <!-- /Brand logo-->


            <!-- Left Text-->
            <div class="d-none d-lg-flex col-lg-8 align-items-center p-5 brand-panel">
                <div class="brand-panel-inner">
                    <span class="brand-mark">{{ strtoupper(substr(config('app.name', 'B'), 0, 1)) }}</span>
                    <h3>Reset your password</h3>
                    <p>No worries — enter the email on your {{ config('app.name') }} account and we'll send you a link to get back in.</p>
                </div>
            </div>
            <!-- /Left Text-->

            <!-- Forgot password-->
            <div class="d-flex col-lg-4 align-items-center auth-bg px-2 p-lg-5">
                <div class="col-12 col-sm-8 col-md-6 col-lg-12 px-xl-2 mx-auto">
                    <h2 class="card-title fw-bold mb-1">{{ __('locale.auth.recover_your_password') }}</h2>
                    <p class="card-text mb-2">{{ __('locale.auth.recover_password_instructions') }}</p>
                    <form class="auth-forgot-password-form mt-2" method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <div class="mb-1">
                            <label class="form-label" for="email">{{ __('locale.labels.email') }}</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="{{ __('locale.labels.email') }}" required autocomplete="email" autofocus>


                            @error('email')
                            <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>


                        {{-- @if(config('no-captcha.login'))
                            <div class="mb-1">
                                {!! RecaptchaV3::field('email') !!}
                            </div>
                        @endif --}}

                        <button type="submit" class="btn btn-primary w-100" tabindex="2">{{ __('locale.auth.recover_password') }}</button>
                    </form>
                    <p class="text-center mt-2">
                        <a href="{{url('login')}}">
                            <i data-feather="chevron-left"></i> {{ __('locale.auth.back_to_login') }}
                        </a>
                    </p>
                </div>
            </div>
            <!-- /Forgot password-->

        </div>
    </div>
@endsection
