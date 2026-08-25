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
    <section id="api-documentation">
        <div class="row">

            <div class="col-md-12 d-none d-sm-block">
                <p class="row justify-content-center welcome-messages">{{ __('locale.labels.welcome_to_docs', ['brandname' => config('app.name')]) }}</p>
                <p class="row justify-content-center mb-3 welcome-description">
                    {{ __('locale.description.api_docs', ['brandname' => config('app.name')]) }}
                </p>
            </div>

            <div class="col-lg-3 col-md-5 col-12">
                <div class="card">
                    <div class="card-body" id="features">
                        <h5 class="text-success text-uppercase">{{ config('app.name') }} {{ __('locale.labels.api') }}</h5>
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
            <div class="col-lg-9 col-md-7 col-12">
                <div class="card">
                    <div class="card-body features_description">
                        @if(\Helper::api_feature_enabled('api_contacts'))
                            <div class="title mb-2" id="contacts-api-div">
                                @include('customer.Developers._http_contacts_api')
                            </div>
                        @endif

                        @if(\Helper::api_feature_enabled('api_contact_groups'))
                            <div class="title mb-2" id="contact-groups-api-div">
                                @include('customer.Developers._http_contact_groups_api')
                            </div>
                        @endif

                        @if(\Helper::api_feature_enabled('api_sms'))
                            <div class="title mb-2" id="sms-api-div">
                                @include('customer.Developers._http_sms_api')
                            </div>
                        @endif

                        @if(\Helper::api_feature_enabled('api_voice'))
                            <div class="title mb-2" id="voice-api-div">
                                @include('customer.Developers._http_voice_api')
                            </div>
                        @endif

                        @if(\Helper::api_feature_enabled('api_mms'))
                            <div class="title mb-2" id="mms-api-div">
                                @include('customer.Developers._http_mms_api')
                            </div>
                        @endif

                        @if(\Helper::api_feature_enabled('api_whatsapp'))
                            <div class="title mb-2" id="whatsapp-api-div">
                                @include('customer.Developers._http_whatsapp_api')
                            </div>
                        @endif

                        @if(\Helper::api_feature_enabled('api_viber'))
                            <div class="title mb-2" id="viber-api-div">
                                @include('customer.Developers._http_viber_api')
                            </div>
                        @endif

                        @if(\Helper::api_feature_enabled('api_otp'))
                            <div class="title mb-2" id="otp-api-div">
                                @include('customer.Developers._http_otp_api')
                            </div>
                        @endif

                        @if(\Helper::api_feature_enabled('api_profile'))
                            <div class="title mb-2" id="profile-api-div">
                                @include('customer.Developers._http_profile_api')
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
@endsection
