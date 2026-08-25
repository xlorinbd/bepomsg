<div class="col-md-6 col-12">
    <div class="form-body">
        <form class="form form-vertical" action="{{ route('admin.settings.authentication') }}" method="post">
            @csrf
            <div class="row">

                <div class="col-12">
                    <div class="mb-1">
                        <label for="client_registration" class="form-label required">{{__('locale.settings.client_registration')}}</label>
                        <select class="form-select" id="client_registration" name="client_registration">
                            <option value="1" @if(config('account.can_register')) selected @endif>{{__('locale.labels.yes')}}</option>
                            <option value="0" @if(!config('account.can_register')) selected @endif>{{__('locale.labels.no')}}</option>
                        </select>
                    </div>
                    @error('client_registration')
                    <p><small class="text-danger">{{ $message }}</small></p>
                    @enderror
                </div>

                <div class="col-12">
                    <div class="mb-1">
                        <label for="client_can_delete_account" class="form-label required">{{__('locale.settings.client_can_delete_account')}}</label>
                        <select class="form-select" id="client_can_delete_account" name="client_can_delete_account">
                            <option value="1" @if(config('account.can_delete')) selected @endif>{{__('locale.labels.yes')}}</option>
                            <option value="0" @if(!config('account.can_delete')) selected @endif>{{__('locale.labels.no')}}</option>
                        </select>
                    </div>
                    @error('client_can_delete_account')
                    <p><small class="text-danger">{{ $message }}</small></p>
                    @enderror
                </div>

                <div class="col-12">
                    <div class="mb-1">
                        <label for="registration_verification" class="form-label required">{{__('locale.settings.registration_verification')}}</label>
                        <select class="form-select" id="registration_verification" name="registration_verification">
                            <option value="1" @if(config('account.verify_account')) selected @endif>{{__('locale.labels.yes')}}</option>
                            <option value="0" @if(!config('account.verify_account')) selected @endif>{{__('locale.labels.no')}}</option>
                        </select>
                    </div>
                    @error('registration_verification')
                    <p><small class="text-danger">{{ $message }}</small></p>
                    @enderror
                </div>

                <div class="col-12">
                    <div class="mb-1">
                        <label for="registration_default_plan" class="form-label required">{{__('locale.labels.default_registration_plan')}}</label>
                        <select class="form-select select2" id="registration_default_plan" name="registration_default_plan">
                            @foreach($plans as $plan)
                                <option value="{{$plan->id}}" @if(\App\Helpers\Helper::app_config('registration_default_plan') == $plan->id) selected @endif>{{$plan->name}} ({{$plan->price}} {{$plan->currency->code}})</option>
                            @endforeach
                        </select>
                    </div>
                    @error('registration_default_plan')
                    <p><small class="text-danger">{{ $message }}</small></p>
                    @enderror
                </div>

                <div class="col-12">
                    <div class="mb-1">
                        <label for="two_factor" class="form-label required">{{__('locale.settings.two_factor_authentication')}}</label>
                        <select class="form-select" id="two_factor" name="two_factor">
                            <option value="1" @if(config('app.two_factor')) selected @endif>{{__('locale.labels.yes')}}</option>
                            <option value="0" @if(!config('app.two_factor')) selected @endif>{{__('locale.labels.no')}}</option>
                        </select>
                    </div>
                    @error('two_factor')
                    <p><small class="text-danger">{{ $message }}</small></p>
                    @enderror
                </div>

                <div class="col-12 show-two-factor">
                    <input type="hidden" value="email" name="two_factor_send_by">
                    <input type="hidden" value="0" name="captcha_in_login">
                    <input type="hidden" value="0" name="captcha_in_client_registration">
                    <input type="hidden" value="" name="captcha_site_key">
                    <input type="hidden" value="" name="captcha_secret_key">
                </div>

                {{--
                <div class="col-12">
                    <div class="divider divider-left divider-primary mt-3">
                        <div class="divider-text text-uppercase fw-bold text-primary">{{ __('locale.settings.recaptcha_information') }}</div>
                    </div>
                </div>


                <div class="col-12">
                    <p>{!! __('locale.description.captcha') !!} </p>
                </div>

                <div class="col-12">
                    <div class="mb-1">
                        <label for="captcha_site_key" class="form-label">{{ __('locale.settings.recaptcha_site_key') }}</label>
                        <input type="text" id="captcha_site_key" name="captcha_site_key" class="form-control" value="{{ config('no-captcha.sitekey') }}">
                        @error('captcha_site_key')
                        <p><small class="text-danger">{{ $message }}</small></p>
                        @enderror
                    </div>
                </div>


                <div class="col-12">
                    <div class="mb-1">
                        <label for="captcha_secret_key" class="form-label">{{ __('locale.settings.recaptcha_secret_key') }}</label>
                        <input type="text" id="captcha_secret_key" name="captcha_secret_key" class="form-control" value="{{ config('no-captcha.secret') }}">

                        @error('captcha_secret_key')
                        <p><small class="text-danger">{{ $message }}</small></p>
                        @enderror
                    </div>
                </div>

                <div class="col-12">
                    <div class="mb-1">
                        <label for="captcha_in_login" class="form-label required">{{__('locale.settings.captcha_in_login')}}</label>
                        <select class="form-select" id="captcha_in_login" name="captcha_in_login">
                            <option value="1" @if(config('no-captcha.login')) selected @endif>{{__('locale.labels.yes')}}</option>
                            <option value="0" @if(!config('no-captcha.login')) selected @endif>{{__('locale.labels.no')}}</option>
                        </select>
                    </div>
                    @error('captcha_in_login')
                    <p><small class="text-danger">{{ $message }}</small></p>
                    @enderror
                </div>

                <div class="col-12">
                    <div class="mb-1">
                        <label for="captcha_in_client_registration" class="form-label required">{{__('locale.settings.captcha_in_client_registration')}}</label>
                        <select class="form-select" id="captcha_in_client_registration" name="captcha_in_client_registration">
                            <option value="1" @if(config('no-captcha.registration')) selected @endif>{{__('locale.labels.yes')}}</option>
                            <option value="0" @if(!config('no-captcha.registration')) selected @endif>{{__('locale.labels.no')}}</option>
                        </select>
                    </div>
                    @error('captcha_in_client_registration')
                    <p><small class="text-danger">{{ $message }}</small></p>
                    @enderror
                </div>
                --}}
                <div class="col-12">
                    <div class="divider divider-left divider-primary mt-2">
                        <div class="divider-text text-uppercase fw-bold text-primary">Registration Fields (1: Required, 0: Optional, -1: Hidden)</div>
                    </div>
                </div>

                @php
                    $fields = [
                        'req_first_name' => 'First Name',
                        'req_username' => 'Username',
                        'req_gender' => 'Gender',
                        'req_email' => 'Email',
                        'req_password' => 'Password',
                        'req_phone' => 'Phone',
                        'req_nid_number' => 'NID Number',
                        'req_date_of_birth' => 'Date of Birth',
                        'req_address' => 'Address',
                        'req_company_name' => 'Company Name',
                        'req_company_address' => 'Company Address',
                        'req_purpose_of_use' => 'Purpose of Use',
                        'req_nid_upload' => 'NID Upload',
                        'req_trade_license' => 'Trade License',
                        'req_profile_photo' => 'Profile Photo',
                    ];
                @endphp

                @foreach($fields as $fieldName => $fieldLabel)
                    <div class="col-md-6 col-12">
                        <div class="mb-1">
                            <label for="{{ $fieldName }}" class="form-label required">{{ $fieldLabel }}</label>
                            <select class="form-select" id="{{ $fieldName }}" name="{{ $fieldName }}">
                                <option value="1" @if(\App\Helpers\Helper::app_config($fieldName) == '1') selected @endif>Required</option>
                                <option value="0" @if(\App\Helpers\Helper::app_config($fieldName) == '0') selected @endif>Optional</option>
                                <option value="-1" @if(\App\Helpers\Helper::app_config($fieldName) == '-1') selected @endif>Hidden</option>
                            </select>
                        </div>
                        @error($fieldName)
                        <p><small class="text-danger">{{ $message }}</small></p>
                        @enderror
                    </div>
                @endforeach



                <div class="col-12">
                    <div class="divider divider-left divider-primary mt-2">
                        <div class="divider-text text-uppercase fw-bold text-primary">
                            {{ __('locale.settings.facebook_login') }}
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="mb-1">
                        <label for="login_with_facebook" class="form-label">{{__('locale.settings.login_with_facebook')}}</label>
                        <select class="form-select" id="login_with_facebook" name="login_with_facebook">
                            <option value="1" @if(config('services.facebook.active')) selected @endif>{{__('locale.labels.yes')}}</option>
                            <option value="0" @if(!config('services.facebook.active')) selected @endif>{{__('locale.labels.no')}}</option>
                        </select>
                    </div>
                    @error('login_with_facebook')
                    <p><small class="text-danger">{{ $message }}</small></p>
                    @enderror
                </div>

                <div class="col-12 show-facebook">
                    <p>{!! __('locale.description.facebook', ['brandname' => config('app.name'), 'callback_url' => route('social.callback', ['provider' => 'facebook'])] ) !!} </p>
                </div>

                <div class="col-12 show-facebook">
                    <div class="mb-1">
                        <label for="facebook_client_id" class="form-label">{{ __('locale.settings.app_id') }}</label>
                        <input type="text" id="facebook_client_id" name="facebook_client_id" class="form-control" value="{{ config('services.facebook.client_id') }}">
                        @error('facebook_client_id')
                        <p><small class="text-danger">{{ $message }}</small></p>
                        @enderror
                    </div>
                </div>

                <div class="col-12 show-facebook">
                    <div class="mb-1">
                        <label for="facebook_client_secret" class="form-label">{{ __('locale.settings.app_secret') }}</label>
                        <input type="text" id="facebook_client_secret" name="facebook_client_secret" class="form-control" value="{{ config('services.facebook.client_secret') }}">
                        @error('facebook_client_secret')
                        <p><small class="text-danger">{{ $message }}</small></p>
                        @enderror
                    </div>
                </div>


                <div class="col-12">
                    <div class="divider divider-left divider-primary mt-2">
                        <div class="divider-text text-uppercase fw-bold text-primary">{{ __('locale.settings.twitter_login') }}</div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="mb-1">
                        <label for="login_with_twitter">{{__('locale.settings.login_with_twitter')}}</label>
                        <select class="form-select" id="login_with_twitter" name="login_with_twitter">
                            <option value="1" @if(config('services.twitter.active')) selected @endif>{{__('locale.labels.yes')}}</option>
                            <option value="0" @if(!config('services.twitter.active')) selected @endif>{{__('locale.labels.no')}}</option>
                        </select>
                    </div>
                    @error('login_with_twitter')
                    <p><small class="text-danger">{{ $message }}</small></p>
                    @enderror
                </div>

                <div class="col-12 show-twitter">
                    <p>{!! __('locale.description.twitter', ['url' => config('app.url'), 'callback_url' => route('social.callback', ['provider' => 'twitter'])] ) !!} </p>
                </div>

                <div class="col-12 show-twitter">
                    <div class="mb-1">
                        <label for="twitter_client_id" class="form-label">{{ __('locale.labels.api_key') }}</label>
                        <input type="text" id="twitter_client_id" name="twitter_client_id" class="form-control" value="{{ config('services.twitter.client_id') }}">
                        @error('twitter_client_id')
                        <p><small class="text-danger">{{ $message }}</small></p>
                        @enderror
                    </div>
                </div>

                <div class="col-12 show-twitter">
                    <div class="mb-1">
                        <label for="twitter_client_secret" class="form-label">{{ __('locale.labels.secret_key') }}</label>
                        <input type="text" id="twitter_client_secret" name="twitter_client_secret" class="form-control" value="{{ config('services.twitter.client_secret') }}">
                        @error('twitter_client_secret')
                        <p><small class="text-danger">{{ $message }}</small></p>
                        @enderror
                    </div>
                </div>

                <div class="col-12">
                    <div class="divider divider-left divider-primary mt-2">
                        <div class="divider-text text-uppercase fw-bold text-primary">{{ __('locale.settings.google_login') }}</div>
                    </div>
                </div>


                <div class="col-12">
                    <div class="mb-1">
                        <label for="login_with_google" class="form-label">{{__('locale.settings.login_with_google')}}</label>
                        <select class="form-select" id="login_with_google" name="login_with_google">
                            <option value="1" @if(config('services.google.active')) selected @endif>{{__('locale.labels.yes')}}</option>
                            <option value="0" @if(!config('services.google.active')) selected @endif>{{__('locale.labels.no')}}</option>
                        </select>
                    </div>
                    @error('login_with_google')
                    <p><small class="text-danger">{{ $message }}</small></p>
                    @enderror
                </div>

                <div class="col-12 show-google">
                    <p>{!! __('locale.description.google', ['callback_url' => route('social.callback', ['provider' => 'google'])] )!!} </p>
                </div>

                <div class="col-12 show-google">
                    <div class="mb-1">
                        <label for="google_client_id" class="form-label">{{ __('locale.labels.client_id') }}</label>
                        <input type="text" id="google_client_id" name="google_client_id" class="form-control" value="{{ config('services.google.client_id') }}">
                        @error('google_client_id')
                        <p><small class="text-danger">{{ $message }}</small></p>
                        @enderror
                    </div>
                </div>

                <div class="col-12 show-google">
                    <div class="mb-1">
                        <label for="google_client_secret" class="form-label">{{ __('locale.labels.client_secret') }}</label>
                        <input type="text" id="google_client_secret" name="google_client_secret" class="form-control" value="{{ config('services.google.client_secret') }}">
                        @error('google_client_secret')
                        <p><small class="text-danger">{{ $message }}</small></p>
                        @enderror
                    </div>
                </div>


                <div class="col-12">
                    <div class="divider divider-left divider-primary mt-2">
                        <div class="divider-text text-uppercase fw-bold text-primary">{{ __('locale.settings.github_login') }}</div>
                    </div>
                </div>


                <div class="col-12">
                    <div class="mb-1">
                        <label for="login_with_github" class="form-label">{{__('locale.settings.login_with_github')}}</label>
                        <select class="form-select" id="login_with_github" name="login_with_github">
                            <option value="1" @if(config('services.github.active')) selected @endif>{{__('locale.labels.yes')}}</option>
                            <option value="0" @if(!config('services.github.active')) selected @endif>{{__('locale.labels.no')}}</option>
                        </select>
                    </div>
                    @error('login_with_github')
                    <p><small class="text-danger">{{ $message }}</small></p>
                    @enderror
                </div>

                <div class="col-12 show-github">
                    <p>{!! __('locale.description.github', ['url' => config('app.url'), 'callback_url' => route('social.callback', ['provider' => 'github'])] )!!} </p>
                </div>

                <div class="col-12 show-github">
                    <div class="mb-1">
                        <label for="github_client_id" class="form-label">{{ __('locale.labels.client_id') }}</label>
                        <input type="text" id="github_client_id" name="github_client_id" class="form-control" value="{{ config('services.github.client_id') }}">
                        @error('github_client_id')
                        <p><small class="text-danger">{{ $message }}</small></p>
                        @enderror
                    </div>
                </div>

                <div class="col-12 show-github">
                    <div class="mb-1">
                        <label for="github_client_secret" class="form-label">{{ __('locale.labels.client_secret') }}</label>
                        <input type="text" id="github_client_secret" name="github_client_secret" class="form-control" value="{{ config('services.github.client_secret') }}">
                        @error('github_client_secret')
                        <p><small class="text-danger">{{ $message }}</small></p>
                        @enderror
                    </div>
                </div>


                <div class="col-12 mt-2">
                    <button type="submit" class="btn btn-primary mb-1">
                        <i data-feather="save"></i> {{__('locale.buttons.save')}}
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

