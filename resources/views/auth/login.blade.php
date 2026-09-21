@php
    use App\Helpers\Helper;$configData = Helper::applClasses();

    // Lowest published per-SMS rate (falls back to hiding the callout when no tiers exist)
    $bmMinRate = \App\Models\SmsPricingTier::where('status', true)->min('rate');
@endphp

@extends('layouts/fullLayoutMaster')

@section('title', __('locale.auth.login'))

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/authentication.css')) }}">
    {{-- @if(config('no-captcha.login')) --}}
    {{--     {!! RecaptchaV3::initJs() !!} --}}
    {{-- @endif --}}
@endsection

@section('content')

    <div class="bm-login">

        {{-- Left: form --}}
        <div class="bm-login__form">
            <div class="bm-login__inner">
                <a href="{{ route('login') }}">
                    <img class="bm-login__logo" src="{{ asset(Helper::app_config('app_logo')) }}" alt="{{ config('app.name') }}">
                </a>

                <div>
                    <h1 class="bm-login__title">{{ __('portal.login_title') }}</h1>
                    <div class="bm-page-sub" style="margin-top: 4px;">{{ __('portal.login_sub') }}</div>
                </div>

                @if(config('app.stage') == 'demo')
                    <div class="d-flex justify-content-between" style="cursor: pointer;">
                        <span class="text-primary font-medium-1 admin-login text-uppercase">Admin View</span>
                        <span class="text-success font-medium-1 pull-right customer-login text-uppercase">Campaign View</span>
                        <span class="text-danger font-medium-1 dlt-login text-uppercase">DLT View</span>
                    </div>
                @endif

                <form class="auth-login-form bm-form d-flex flex-column" style="gap: 18px;" method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="bm-field">
                        <label for="email">{{ __('locale.labels.email') }}</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                               name="email" placeholder="you@company.com" value="{{ old('email') }}"
                               style="min-height: 40px;"
                               required autocomplete="email" autofocus>

                        @error('email')
                        <div class="alert alert-danger mt-1 mb-0 alert-validation-msg" role="alert">
                            <div class="alert-body d-flex align-items-center">
                                <i data-feather="info" class="me-50"></i>
                                <span>{{ $message }}</span>
                            </div>
                        </div>
                        @enderror
                    </div>

                    <div class="bm-field">
                        <label for="password">{{ __('locale.labels.password') }}</label>
                        <div class="position-relative">
                            <input id="password" type="password" class="form-control" name="password"
                                   placeholder="••••••••" style="min-height: 40px; padding-right: 64px;"
                                   required autocomplete="password"
                                   @if(config('app.stage') == 'demo') value="12345678" @endif>
                            <a href="#" id="bm-toggle-password" class="bm-link"
                               style="position: absolute; top: 50%; right: 12px; transform: translateY(-50%); font-size: 12px;"
                               data-show="{{ __('portal.show') }}" data-hide="{{ __('portal.hide') }}">{{ __('portal.show') }}</a>
                        </div>
                    </div>

                    <div class="bm-row bm-row--between" style="font-size: 13px;">
                        <div class="form-check m-0">
                            <input class="form-check-input" {{ old('remember') ? 'checked' : '' }} name="remember"
                                   id="remember-me" type="checkbox" tabindex="3"/>
                            <label class="form-check-label" for="remember-me" style="color: var(--bm-ink-2);"> {{ __('locale.auth.remember_me') }}</label>
                        </div>

                        @if (Route::has('password.request'))
                            <a class="bm-link" href="{{ route('password.request') }}">{{ __('locale.auth.forgot_password') }}?</a>
                        @endif
                    </div>

                    <button type="submit" class="bm-btn bm-btn--primary bm-btn--block" style="padding: 11px; font-size: 14px;"
                            tabindex="4">{{ __('locale.auth.login') }}</button>
                </form>

                @if(config('account.can_register'))
                    <div class="bm-muted" style="font-size: 13px; text-align: center;">
                        {{ __('locale.auth.new_on_our_platform') }}?
                        <a class="bm-link" href="{{ route('register') }}">{{ __('locale.auth.register') }}</a>
                    </div>
                @endif

                @if(config('services.facebook.active') || config('services.twitter.active') || config('services.google.active') || config('services.github.active'))
                    <div class="divider my-1">
                        <div class="divider-text">{{ __('locale.auth.or') }}</div>
                    </div>

                    <div class="auth-footer-btn d-flex justify-content-center">
                        @if(config('services.facebook.active'))
                            <a class="btn btn-facebook" href="{{ route('social.login', 'facebook') }}"
                               data-bs-toggle="tooltip" data-bs-placement="top" title="Facebook">
                                <i data-feather="facebook"></i>
                            </a>
                        @endif

                        @if(config('services.twitter.active'))
                            <a class="btn btn-twitter" href="{{ route('social.login', 'twitter') }}"
                               data-bs-toggle="tooltip" data-bs-placement="top" title="Twitter">
                                <i data-feather="twitter"></i>
                            </a>
                        @endif

                        @if(config('services.google.active'))
                            <a class="btn btn-google" href="{{ route('social.login', 'google') }}"
                               data-bs-toggle="tooltip" data-bs-placement="top" title="Google">
                                <i data-feather="mail"></i>
                            </a>
                        @endif

                        @if(config('services.github.active'))
                            <a class="btn btn-github" href="{{ route('social.login', 'github') }}"
                               data-bs-toggle="tooltip" data-bs-placement="top" title="Github">
                                <i data-feather="github"></i>
                            </a>
                        @endif
                    </div>
                @endif

                <div class="bm-row" style="justify-content: center; gap: 16px; font-size: 12px;">
                    <a class="bm-muted" target="_blank" href="{{ route('terms-of-use') }}">{{ __('locale.labels.terms_of_use') }}</a>
                    <a class="bm-muted" target="_blank" href="{{ route('privacy-policy') }}">{{ __('locale.labels.privacy_policy') }}</a>
                </div>
            </div>
        </div>

        {{-- Right: brand panel --}}
        <div class="bm-login__panel">
            <h2>{{ __('portal.login_headline') }}</h2>
            <p>{{ __('portal.login_body') }}</p>
            <div class="bm-login__stats">
                <div><strong>99.2%</strong><span>{{ __('portal.avg_delivery') }}</span></div>
                @if($bmMinRate)
                    <div><strong>{{ number_format($bmMinRate, 2) }}৳</strong><span>{{ __('portal.min_rate') }}</span></div>
                @endif
                <div><strong>v3</strong><span>REST API</span></div>
            </div>
        </div>

    </div>
@endsection


@push('scripts')
    <script>
        $('.admin-login').on('click', function () {
            $('#email').val('admin@bepomsg.com')
        });

        $('.customer-login').on('click', function () {
            $('#email').val('customer@bepomsg.com')
        });

        $('.dlt-login').on('click', function () {
            $('#email').val('dlt@bepomsg.com')
        });

        $('#bm-toggle-password').on('click', function (e) {
            e.preventDefault();
            const $input = $('#password'), show = $input.attr('type') === 'password';
            $input.attr('type', show ? 'text' : 'password');
            $(this).text($(this).data(show ? 'hide' : 'show'));
        });
    </script>
@endpush
