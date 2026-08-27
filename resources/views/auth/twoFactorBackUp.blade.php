@php
  $configData = Helper::applClasses();
@endphp
@extends('layouts/fullLayoutMaster')

@section('title', __('locale.auth.verify_with_backup_code'))

@section('page-style')
  <link rel="stylesheet" href="{{ asset(mix('css/base/pages/authentication.css')) }}">

  @include('auth._brand-style')
@endsection

@section('content')
  <div class="auth-wrapper auth-cover">
    <div class="auth-inner row m-0">
      <!-- Brand logo-->
      <a class="brand-logo" href="{{route('login')}}">
        <img src="{{asset(config('app.logo'))}}" alt="{{config('app.name')}}"/>
      </a>
      <!-- /Brand logo-->

      <!-- Left Text-->
      <div class="d-none d-lg-flex col-lg-8 align-items-center p-5 brand-panel">
        <div class="brand-panel-inner">
          <span class="brand-mark"><img src="{{ asset(Helper::app_config('app_logo')) }}" alt="{{ config('app.name') }}"></span>
          <h3>Use a backup code</h3>
          <p>Lost access to your device? Enter one of your saved backup codes to get back into {{ config('app.name') }}.</p>
        </div>
      </div>
      <!-- /Left Text-->

      <!-- two steps verification v2-->
      <div class="d-flex col-lg-4 align-items-center auth-bg px-2 p-lg-5">
        <div class="col-12 col-sm-8 col-md-6 col-lg-12 px-xl-2 mx-auto">
          <h2 class="card-title fw-bolder mb-1">{{ __('locale.auth.verify_with_backup_code') }}</h2>
          <p class="card-text mb-75">If you lost your device or lose your email account then try with the backup code which was provided when you enable the Two-Factor Authentication option.</p>

          <form method="POST" action="{{ route('verify.backup') }}">
            @csrf
            <h6>Type your 6 digit security code</h6>
            <div class="auth-input-wrapper d-flex align-items-center justify-content-between">
              <input id="two_factor_code" type="number" class="form-control  text-center numeral-mask mb-1 @error('two_factor_code') is-invalid @enderror" name="two_factor_code" value="{{ old('two_factor_code') }}" required autocomplete="two_factor_code" maxlength="6" autofocus>

              @error('two_factor_code')
              <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
              @enderror

            </div>
            <button class="btn btn-primary w-100" type="submit" tabindex="4">{{ __('locale.auth.verify_with_backup_code') }}</button>
          </form>
          <p class="text-center mt-2">
            <span>or</span>
            <a href="{{ route('verify.index')}}" class="btn btn-outline-primary btn-block px-75">{{ __('locale.auth.verify') }}</a>

          </p>
        </div>
      </div>
    </div>
  </div>
@endsection
