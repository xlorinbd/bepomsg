@php use App\Helpers\Helper;
use App\Models\AppConfig;
use App\Library\Tool; 
$locale = app()->getLocale();
$setting_key = $locale == 'bn' ? 'registration_agreement_bn' : 'registration_agreement';
$termsOfUse = AppConfig::where('setting', $setting_key)->first();
if ($locale == 'bn' && (empty($termsOfUse) || empty($termsOfUse->value))) {
    $termsOfUse = AppConfig::where('setting', 'registration_agreement')->first();
}
$termsOfUseData = $termsOfUse ? $termsOfUse->value : '';
@endphp
@extends('layouts/fullLayoutMaster')

@section('title', __('locale.auth.register'))

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/wizard/bs-stepper.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection

@section('page-style')
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-wizard.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/authentication.css')) }}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/base/pages/page-pricing.css')}}">

    @include('auth._brand-style')

    <style>
        .modal-body img {
            max-width: 100%;
            height: auto;
        }
    </style>
    {{-- @if(config('no-captcha.registration'))
        {!! RecaptchaV3::initJs() !!}
    @endif --}}
@endsection

@section('content')
    <div class="auth-wrapper auth-cover">
        <div class="auth-inner row m-0">
            <!-- Brand logo-->
            <a class="brand-logo" href="{{route('login')}}">
                <img src="{{asset(Helper::app_config('app_logo'))}}" alt="{{config('app.name')}}" style="max-width: 210px;" />
            </a>
            <!-- /Brand logo-->

            <!-- Left Text-->
            <div class="col-lg-3 d-none d-lg-flex align-items-center p-4 brand-panel">
                <div class="brand-panel-inner">
                    <span class="brand-mark">{{ strtoupper(substr(config('app.name', 'B'), 0, 1)) }}</span>
                    <h3>Create your account</h3>
                    <p>Join {{ config('app.name') }} and start sending bulk SMS &amp; WhatsApp campaigns in minutes.</p>
                    <ul class="brand-highlights">
                        <li>Free plan to get started</li>
                        <li>No setup fees</li>
                        <li>Upgrade any time</li>
                    </ul>
                </div>
            </div>
            <!-- /Left Text-->

            <!-- Register-->
            <div class="col-lg-9 d-flex align-items-center auth-bg px-2 px-sm-3 px-lg-5 pt-3">
                <div class="width-700 mx-auto">
                    <div class="card shadow-none bg-transparent m-0">
                        <div class="card-body px-0">
                            @if ($errors->any())
                                @foreach ($errors->all() as $error)
                                    <div class="alert alert-danger" role="alert">
                                        <div class="alert-body">{{ $error }}</div>
                                    </div>
                                @endforeach
                            @endif

                            <div class="alert alert-info mt-1" role="alert" style="background-color: #17a2b8 !important; color: white !important;">
                                <div class="alert-body font-medium-2 font-weight-bolder">
                                    নিম্নোক্ত তথ্যসমূহ সম্পূর্ণ ও সঠিকভাবে পূরণ না করলে আপনার অ্যাকাউন্ট ভেরিফিকেশন সম্পন্ন করা হবে না
                                </div>
                            </div>

                            <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="auth-register-form mt-2">
                                @csrf
                                <div class="row">

                                @php
                                    $fields = [
                                        'phone' => ['label' => 'Mobile Number', 'placeholder' => 'আপনার মোবাইল নাম্বার', 'type' => 'number', 'wrapper' => 'col-md-6'],
                                        'username' => ['label' => 'Username', 'placeholder' => 'পছন্দ অনুযায়ী লগইন আইডি', 'type' => 'text', 'wrapper' => 'col-md-6'],
                                        'first_name' => ['label' => 'Your Name (ভোটার আইডি কার্ড অনুযায়ী)', 'placeholder' => 'আপনার নাম (ভোটার আইডি কার্ড অনুযায়ী)', 'type' => 'text', 'wrapper' => 'col-md-6'],
                                        'gender' => ['label' => 'Gender', 'type' => 'select', 'wrapper' => 'col-md-6', 'options' => ['Male' => 'Male', 'Female' => 'Female', 'Other' => 'Other']],
                                        'email' => ['label' => 'Email', 'placeholder' => 'আপনার ইমেইল', 'type' => 'email', 'wrapper' => 'col-md-12'],
                                        'password' => ['label' => __('locale.labels.password'), 'type' => 'password', 'wrapper' => 'col-md-6'],
                                        'nid_number' => ['label' => 'NID Number', 'placeholder' => 'আপনার এনআইডি নাম্বার', 'type' => 'text', 'wrapper' => 'col-md-6'],
                                        'date_of_birth' => ['label' => 'Date of Birth', 'type' => 'date', 'wrapper' => 'col-md-6'],
                                        'address' => ['label' => 'Address (ভোটার আইডি কার্ড অনুযায়ী)', 'placeholder' => 'আপনার ঠিকানা (ভোটার আইডি কার্ড অনুযায়ী)', 'type' => 'textarea', 'wrapper' => 'col-12', 'rows' => 3],
                                        'company_name' => ['label' => 'Company Name', 'placeholder' => 'আপনার প্রতিষ্ঠানের নাম', 'type' => 'text', 'wrapper' => 'col-md-6'],
                                        'company_address' => ['label' => 'Company Address', 'placeholder' => 'আপনার প্রতিষ্ঠানের ঠিকানা', 'type' => 'textarea', 'wrapper' => 'col-md-6', 'rows' => 2],
                                        'purpose_of_use' => ['label' => 'Purpose of use', 'placeholder' => 'SMS কি কাজে ব্যবহার করবেন? সংক্ষিপ্ত বিবরণ দিন', 'type' => 'textarea', 'wrapper' => 'col-12', 'rows' => 3],
                                        'nid_upload' => ['label' => 'Nid Upload', 'type' => 'file', 'wrapper' => 'col-md-4'],
                                        'trade_license' => ['label' => 'Trade License', 'type' => 'file', 'wrapper' => 'col-md-4'],
                                        'profile_photo' => ['label' => 'Profile Photo', 'type' => 'file', 'wrapper' => 'col-md-4'],
                                    ];
                                @endphp

                                @foreach($fields as $fieldName => $fieldData)
                                    @php
                                        $settingValue = \App\Helpers\Helper::app_config('req_'.$fieldName);
                                        if ($settingValue == '-1') continue; // Hidden field
                                        
                                        $isRequired = $settingValue == '1';
                                        $labelClass = $isRequired ? 'form-label required' : 'form-label';
                                        $requiredAttr = $isRequired ? 'required' : '';
                                    @endphp

                                    <div class="{{ $fieldData['wrapper'] }} mb-1">
                                        <label class="{{ $labelClass }}" for="{{ $fieldName }}">{{ $fieldData['label'] }}</label>
                                        
                                        @if($fieldData['type'] === 'select')
                                            <select class="select2 w-100" name="{{ $fieldName }}" id="{{ $fieldName }}" {{ $requiredAttr }}>
                                                @foreach($fieldData['options'] as $val => $text)
                                                    <option value="{{ $val }}" {{ old($fieldName) == $val ? 'selected' : '' }}>{{ $text }}</option>
                                                @endforeach
                                            </select>
                                        @elseif($fieldData['type'] === 'textarea')
                                            <textarea id="{{ $fieldName }}" class="form-control @error($fieldName) is-invalid @enderror" name="{{ $fieldName }}" rows="{{ $fieldData['rows'] }}" {{ $requiredAttr }} placeholder="{{ $fieldData['placeholder'] }}">{{ old($fieldName) }}</textarea>
                                        @elseif($fieldData['type'] === 'password')
                                            <div class="input-group input-group-merge form-password-toggle">
                                                <input type="{{ $fieldData['type'] }}" id="{{ $fieldName }}" class="form-control @error($fieldName) is-invalid @enderror" name="{{ $fieldName }}" {{ $requiredAttr }} />
                                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                            </div>
                                            @if($fieldName === 'password')
                                            </div>
                                            <div class="col-md-6 mb-1">
                                                <label class="{{ $labelClass }}" for="password_confirmation">{{ __('locale.labels.password_confirmation') }}</label>
                                                <div class="input-group input-group-merge form-password-toggle">
                                                    <input type="password" id="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" {{ $requiredAttr }} />
                                                    <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                                </div>
                                            @endif
                                        @elseif($fieldData['type'] === 'file')
                                            <input type="{{ $fieldData['type'] }}" id="{{ $fieldName }}" class="form-control @error($fieldName) is-invalid @enderror" name="{{ $fieldName }}" {{ $requiredAttr }}>
                                        @else
                                            <input type="{{ $fieldData['type'] }}" id="{{ $fieldName }}" class="form-control @error($fieldName) is-invalid @enderror" name="{{ $fieldName }}" {{ $requiredAttr }} placeholder="{{ $fieldData['placeholder'] ?? '' }}" value="{{ old($fieldName) }}">
                                        @endif
                                    </div>
                                @endforeach
                                    
                                    {{-- @if(config('no-captcha.registration'))
                                        <div class="col-12 mb-1 text-center">
                                            <fieldset class="form-label-group position-relative">
                                                {!! RecaptchaV3::field('register') !!}
                                            </fieldset>
                                            @error('g-recaptcha-response')
                                                <span class="text-danger">{{ __('locale.labels.g-recaptcha-response') }}</span>
                                            @enderror
                                        </div>
                                    @endif --}}

                                    <div class="col-12 mt-2">
                                        <button class="btn btn-primary w-100 btn-submit" type="submit">Submit</button>
                                    </div>
                                    
                                    <div class="col-12 mt-2 text-center">
                                        <p class="mt-2">
                                            <a href="{{url('login')}}">
                                                <i data-feather="chevron-left"></i> {{ __('locale.auth.back_to_login') }}
                                            </a>
                                        </p>
                                    </div>

                                </div>
                                <input type="hidden" name="plans" value="{{ \App\Helpers\Helper::app_config('registration_default_plan') ?? 1 }}" />
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Registration Agreement Modal -->
    <div class="modal fade" id="registrationAgreementModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="registrationAgreementModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="registrationAgreementModalLabel">{{ __('locale.labels.terms_of_use') }}</h5>
                </div>
                <div class="modal-body p-2" style="max-height: 400px; overflow-y: auto;">
                    {!! $termsOfUseData !!}
                </div>
                <div class="modal-footer justify-content-between">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agreeCheckbox">
                        <label class="form-check-label fw-bolder" for="agreeCheckbox">
                            {{ __('locale.labels.i_agree') }}
                        </label>
                    </div>
                    <button type="button" class="btn btn-primary" id="proceedBtn" disabled>{{ __('locale.labels.proceed') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('vendor-script')
    <script src="{{asset(mix('vendors/js/forms/wizard/bs-stepper.min.js'))}}"></script>
    <script src="{{asset(mix('vendors/js/forms/select/select2.full.min.js'))}}"></script>
    <script src="{{asset(mix('vendors/js/forms/validation/jquery.validate.min.js'))}}"></script>
@endsection

@section('page-script')

    <script>
        let registerMultiStepsWizard = document.querySelector('.register-multi-steps-wizard'),
            pageResetForm = $('.auth-register-form'),
            numberedStepper,
            priceOption = $('.pricing-data'),
            select = $('.select2');

        priceOption.delegate(".planPrice", "click", function (e) {
            e.stopPropagation();
            if ($(this).data('value') === '0.00') {
                $('.hide-for-free').hide();
            } else {
                $('.hide-for-free').show();
            }
        });

        // Stepper removed since we are using single step form.

        // select2
        select.each(function () {
            let $this = $(this);
            $this.wrap('<div class="position-relative"></div>');
            $this.select2({
                // the following code is used to disable x-scrollbar when click in select input and
                // take 100% width in responsive also
                dropdownAutoWidth: true,
                width: '100%',
                dropdownParent: $this.parent()
            });
        });

        $(window).on('load', function() {
            $('#registrationAgreementModal').modal('show');
        });

        $('#agreeCheckbox').on('change', function() {
            $('#proceedBtn').prop('disabled', !$(this).is(':checked'));
        });

        $('#proceedBtn').on('click', function() {
            $('#registrationAgreementModal').modal('hide');
        });

    </script>
@endsection