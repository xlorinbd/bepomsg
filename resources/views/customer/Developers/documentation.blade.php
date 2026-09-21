@extends('layouts/contentLayoutMaster')

@section('title', __('locale.developers.api_documents'))

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/ui/prism.min.css')) }}">
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/page-knowledge-base.css')) }}">
@endsection

@section('content')
    <!-- Knowledge base question Content  -->
    <section id="api-documentation" class="bm-page">
        <div>
            <h1 class="bm-page-title">{{ __('locale.labels.welcome_to_docs', ['brandname' => config('app.name')]) }}</h1>
            <div class="bm-page-sub">{{ __('locale.description.api_docs', ['brandname' => config('app.name')]) }}</div>
        </div>

        @php
            $bmToken = (string) Auth::user()->api_token;
            $bmMasked = strlen($bmToken) > 14 ? substr($bmToken, 0, 8) . str_repeat('·', 24) . substr($bmToken, -4) : $bmToken;
        @endphp

        <div class="bm-grid bm-grid--split" style="--bm-cols: minmax(0, 1fr) minmax(0, 2fr);">

            <div class="d-flex flex-column" style="gap: 16px; min-width: 0;">

                {{-- API key --}}
                <div class="bm-card">
                    <span class="bm-card__title">{{ __('locale.developers.api_token') }}</span>
                    <div class="bm-keybox">
                        <span title="{{ $bmMasked }}">{{ $bmMasked !== '' ? $bmMasked : '—' }}</span>
                        @if($bmToken !== '')
                            <a href="#" class="bm-link" id="bm-copy-token" data-token="{{ $bmToken }}">{{ __('portal.copy') }}</a>
                        @endif
                    </div>
                    <a class="bm-btn" href="{{ route('customer.developer.settings') }}">{{ __('locale.developers.regenerate_token') }}</a>
                </div>

                {{-- Endpoint groups (drives the docs panel on the right) --}}
                <div class="bm-card">
                    <div id="features">
                        <span class="bm-card__title">{{ config('app.name') }} {{ __('locale.labels.api') }}</span>
                        <a href="#" class="knowledge-base-question">
                            <ul class="list-group list-group-flush mt-1">
                                @if(\Helper::api_feature_enabled('api_contacts'))
                                    <li class="list-group-item cursor-pointer contacts-api" id="contacts-api">{{ __('locale.developers.contacts_api') }}</li>
                                @endif

                                @if(\Helper::api_feature_enabled('api_contact_groups'))
                                    <li class="list-group-item cursor-pointer contact-groups-api" id="contact-groups-api">{{ __('locale.developers.contact_groups_api') }}</li>
                                @endif

                                @if(\Helper::api_feature_enabled('api_sms'))
                                    <li class="list-group-item cursor-pointer sms-api" id="sms-api">{{ __('locale.developers.sms_api') }}</li>
                                @endif

                                @if(\Helper::api_feature_enabled('api_voice'))
                                    <li class="list-group-item cursor-pointer voice-api" id="voice-api">{{ __('locale.developers.voice_api') }}</li>
                                @endif

                                @if(\Helper::api_feature_enabled('api_mms'))
                                    <li class="list-group-item cursor-pointer mms-api" id="mms-api">{{ __('locale.developers.mms_api') }}</li>
                                @endif

                                @if(\Helper::api_feature_enabled('api_whatsapp'))
                                    <li class="list-group-item cursor-pointer whatsapp-api" id="whatsapp-api">{{ __('locale.developers.whatsapp_api') }}</li>
                                @endif

                                @if(\Helper::api_feature_enabled('api_viber'))
                                    <li class="list-group-item cursor-pointer viber-api" id="viber-api">{{ __('locale.developers.viber_api') }}</li>
                                @endif

                                @if(\Helper::api_feature_enabled('api_otp'))
                                    <li class="list-group-item cursor-pointer otp-api" id="otp-api">{{ __('locale.developers.otp_api') }}</li>
                                @endif

                                @if(\Helper::api_feature_enabled('api_profile'))
                                    <li class="list-group-item cursor-pointer profile-api" id="profile-api">{{ __('locale.labels.profile') }} {{ __('locale.labels.api') }}</li>
                                @endif
                            </ul>
                        </a>
                    </div>
                </div>
            </div>

            <div class="bm-card bm-docs">
                <div>
                    <div class="features_description">
                        @if(\Helper::api_feature_enabled('api_contacts'))
                            <div class="title mb-2" id="contacts-api-div">
                                @include('customer.Developers._contacts_api')
                            </div>
                        @endif

                        @if(\Helper::api_feature_enabled('api_contact_groups'))
                            <div class="title mb-2" id="contact-groups-api-div">
                                @include('customer.Developers._contact_groups_api')
                            </div>
                        @endif

                        @if(\Helper::api_feature_enabled('api_sms'))
                            <div class="title mb-2" id="sms-api-div">
                                @include('customer.Developers._sms_api')
                            </div>
                        @endif

                        @if(\Helper::api_feature_enabled('api_voice'))
                            <div class="title mb-2" id="voice-api-div">
                                @include('customer.Developers._voice_api')
                            </div>
                        @endif

                        @if(\Helper::api_feature_enabled('api_mms'))
                            <div class="title mb-2" id="mms-api-div">
                                @include('customer.Developers._mms_api')
                            </div>
                        @endif

                        @if(\Helper::api_feature_enabled('api_whatsapp'))
                            <div class="title mb-2" id="whatsapp-api-div">
                                @include('customer.Developers._whatsapp_api')
                            </div>
                        @endif

                        @if(\Helper::api_feature_enabled('api_viber'))
                            <div class="title mb-2" id="viber-api-div">
                                @include('customer.Developers._viber_api')
                            </div>
                        @endif

                        @if(\Helper::api_feature_enabled('api_otp'))
                            <div class="title mb-2" id="otp-api-div">
                                @include('customer.Developers._otp_api')
                            </div>
                        @endif

                        @if(\Helper::api_feature_enabled('api_profile'))
                            <div class="title mb-2" id="profile-api-div">
                                @include('customer.Developers._profile_api')
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Knowledge base question Content ends -->
@endsection

@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/ui/prism.min.js')) }}"></script>
@endsection


@section('page-script')
    {{-- vendor js files --}}
    <script src="{{ asset(mix('js/scripts/pages/api-documentation.js')) }}"></script>
    <script>
        $("#bm-copy-token").on("click", function (e) {
            e.preventDefault();
            const $el = $(this), token = String($el.data("token"));
            const done = () => { const t = $el.text(); $el.text("✓"); setTimeout(() => $el.text(t), 1200); };
            if (navigator.clipboard) { navigator.clipboard.writeText(token).then(done); } else { done(); }
        });
    </script>
@endsection
