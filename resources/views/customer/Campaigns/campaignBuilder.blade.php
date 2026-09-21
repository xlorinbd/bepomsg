@php use App\Library\Tool; @endphp
@extends('layouts/contentLayoutMaster')

@section('title', __('locale.menu.Campaign Builder'))

@section('vendor-style')
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
@endsection

@section('page-style')

    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/ui/iphone.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/pickers/form-flat-pickr.css')) }}">

    <style>
        .customized_select2 .select2-selection--multiple {
            border-left: 0;
            border-radius: 0 4px 4px 0;
            min-height: 2.75rem !important;
        }

        .customized_select2 .select2-selection--single, .input_sender_id {
            border-left: 0;
            border-radius: 0 4px 4px 0;
            min-height: 2.75rem !important;
        }

        .input-group > div.position-relative {
            flex-grow: 1;
        }
    </style>

@endsection

@section('content')

    <!-- Basic Vertical form layout section start -->
    <section id="basic-vertical-layouts campaign_builder" class="bm-page">

        {{-- Stepper: Recipients → Message → Schedule → Review (client-side sections of ONE form) --}}
        <div class="bm-stepper" id="bmStepper">
            @foreach([1 => 'recipients', 2 => 'message', 3 => 'schedule', 4 => 'review'] as $n => $key)
                <button type="button" class="bm-step {{ $n === 1 ? 'is-active' : '' }}" data-step="{{ $n }}">
                    <span class="bm-step__num">{{ $n }}</span>{{ __('portal.step_' . $key) }}
                </button>
            @endforeach
        </div>

        <form id="campaign-builder-form" class="form form-vertical bm-form"
              action="{{ route('customer.sms.campaign_builder') }}"
              method="post">
            @csrf

            <div class="bm-page">

                {{-- Step 1 · Recipients --}}
                <div data-step-panel="1" class="bm-grid bm-grid--split" style="--bm-cols: minmax(0, 1.5fr) minmax(0, 1fr);">
                    <div class="bm-card" style="padding: 18px;">
                        <div class="row">
                                    <div class="col-12">
                                        <div class="mb-1">
                                            <label for="name"
                                                   class="required form-label">{{ __('locale.campaigns.campaign_reference') }}</label>
                                            <input type="text"
                                                   id="name"
                                                   class="form-control @error('name') is-invalid @enderror"
                                                   value="{{ old('name') }}"
                                                   name="name" required
                                                   placeholder="{{ __('locale.campaigns.campaign_name_hint') }}"
                                                   autofocus>
                                            @error('name')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>
                                    </div>

                                    @if($sendingServers->count() > 0)
                                        <div class="col-12">
                                            <div class="mb-1">
                                                <label for="sending_server"
                                                       class="form-label required">{{ __('locale.labels.sending_server') }}</label>
                                                <select class="select2 form-select" name="sending_server"
                                                        id="sending_server">
                                                    @foreach($sendingServers as $server)
                                                        @if(isset($server->sendingServer) && $server->sendingServer->status == 1  && $server->sendingServer->plain)
                                                            <option value="{{$server->sendingServer->id}}"> {{ $server->sendingServer->name }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>

                                                @error('sending_server')
                                                <p><small class="text-danger">{{ $message }}</small></p>
                                                @enderror
                                            </div>
                                        </div>

                                    @endif

                                    @can('view_sender_id')
                                        @if(auth()->user()->customer->getOption('sender_id_verification') == 'yes')
                                            <div class="col-12">
                                                <p class="text-uppercase">{{ __('locale.labels.originator') }}</p>
                                            </div>
                                            <div class="col-md-6 col-12 customized_select2">
                                                <div class="mb-1">
                                                    <label for="sender_id_check"
                                                           class="form-label">{{ __('locale.labels.sender_id') }}
                                                        <a class="text-success text-decoration-underline mx-1 text-uppercase cursor-pointer text"
                                                           href="{{ route('customer.senderid.request') }}"
                                                           target="__blank">{{ __('locale.labels.request_new') }}</a>
                                                    </label>
                                                    <div class="input-group">
                                                        <div class="input-group-text">
                                                            <div class="form-check">
                                                                <input type="radio" class="form-check-input sender_id"
                                                                       name="originator" checked value="sender_id"
                                                                       id="sender_id_check" />
                                                                <label class="form-check-label"
                                                                       for="sender_id_check"></label>
                                                            </div>
                                                        </div>

                                                        <div style="width: 17rem">
                                                            <select class="form-select select2" id="sender_id"
                                                                    name="sender_id[]">
                                                                @foreach($sender_ids as $sender_id)
                                                                    <option value="{{$sender_id->sender_id}}"> {{ $sender_id->sender_id }} </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            @can('view_numbers')
                                                <div class="col-md-6 col-12 customized_select2">

                                                    <div class="mb-1">
                                                        <label for="sender_id"
                                                               class="form-label">{{ __('locale.labels.sender_id') }}
                                                            <span class="text-success font-small-1 text-uppercase text">
                                                                ({{ __('locale.labels.select_one_or_insert_your_own') }})
                                                            </span>
                                                        </label>
                                                        <div class="input-group">
                                                            <div class="input-group-text">
                                                                <div class="form-check">
                                                                    <input type="radio"
                                                                           class="form-check-input sender_id"
                                                                           name="originator" checked value="sender_id"
                                                                           id="sender_id_check" />
                                                                    <label class="form-check-label"
                                                                           for="sender_id_check"></label>
                                                                </div>
                                                            </div>

                                                            <div style="width: 17rem">
                                                                <select class="form-select sender_id_custom"
                                                                        id="sender_id"
                                                                        name="sender_id[]">
                                                                    @if(isset($sender_ids))
                                                                        @foreach($sender_ids as $sender_id)
                                                                            <option value="{{$sender_id->sender_id}}"> {{ $sender_id->sender_id }} </option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            @else
                                                <div class="col-12">
                                                    <div class="mb-1">
                                                        <label for="sender_id"
                                                               class="form-label">{{__('locale.labels.sender_id')}}

                                                            <span class="text-success font-small-1 text-uppercase text">
                                                                ({{ __('locale.labels.select_one_or_insert_your_own') }})
                                                            </span>

                                                        </label>
                                                        <select class="form-select sender_id_custom" id="sender_id"
                                                                name="sender_id[]">
                                                            @if(isset($sender_ids))
                                                                @foreach($sender_ids as $sender_id)
                                                                    <option value="{{$sender_id->sender_id}}"> {{ $sender_id->sender_id }} </option>
                                                                @endforeach
                                                            @endif

                                                        </select>
                                                        @error('sender_id')
                                                        <p><small class="text-danger">{{ $message }}</small></p>
                                                        @enderror
                                                    </div>
                                                </div>
                                            @endcan
                                        @endif
                                    @endcan


                                    <div class="col-12">
                                        <div class="mb-1">
                                            <label for="contact_groups"
                                                   class="form-label required">{{ __('locale.contacts.contact_groups') }}</label>
                                            <select class="select2 form-select" required name="contact_groups[]"
                                                    multiple="multiple" id="contact_groups">
                                                @foreach($contact_groups as $group)
                                                    <option value="{{$group->id}}"> {{ $group->name }}
                                                        ({{Tool::number_with_delimiter($group->subscribersCount($group->cache))}} {{__('locale.menu.Contacts')}}
                                                        )
                                                    </option>
                                                @endforeach
                                            </select>

                                            @error('contact_groups')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>
                                    </div>
                        </div>
                    </div>

                    <div class="bm-card" style="padding: 18px;">
                        <span class="bm-card__title">{{ __('portal.final_recipients') }}</span>
                        <div class="bm-card" style="background: var(--bm-bg); box-shadow: none; padding: 14px; gap: 4px;">
                            <span class="bm-stat__label">{{ __('portal.final_recipients') }}</span>
                            <span class="bm-mono" id="bmFinalCount" style="font-size: 24px; font-weight: 700;">0</span>
                        </div>
                        <span class="bm-muted" style="font-size: 12px; line-height: 1.6;">{{ __('portal.recipients_note') }}</span>
                    </div>
                </div>

                {{-- Step 2 · Message --}}
                <div data-step-panel="2" class="bm-grid bm-grid--split" style="--bm-cols: minmax(0, 1.6fr) minmax(0, 1fr); display: none;">
                    <div class="bm-card" style="padding: 18px;">
                        <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="mb-1">
                                            <label class="sms_template form-label"
                                                   for="sms_template">{{__('locale.permission.sms_template')}}</label>
                                            <select class="form-select select2" id="sms_template">
                                                <option>{{ __('locale.labels.select_one') }}</option>
                                                @foreach($templates as $template)
                                                    <option value="{{$template->id}}">{{ $template->name }}</option>
                                                @endforeach

                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="mb-1">
                                            <label class="form-label"
                                                   for="available_tag">{{__('locale.labels.available_tag')}}</label>
                                            <select class="form-select select2" id="available_tag"></select>
                                        </div>
                                    </div>

                                    @if(config('app.trai_dlt') && Auth::user()->customer->activeSubscription()->plan->is_dlt)
                                        <div class="col-12">
                                            <div class="mb-1">
                                                <label for="dlt_template_id"
                                                       class="form-label required">{{ __('locale.templates.dlt_template_id') }}</label>
                                                <input type="text"
                                                       id="dlt_template_id"
                                                       class="form-control @error('dlt_template_id') is-invalid @enderror"
                                                       name="dlt_template_id"
                                                       required>
                                                @error('dlt_template_id')
                                                <p><small class="text-danger">{{ $message }}</small></p>
                                                @enderror
                                            </div>
                                        </div>
                                    @endif


                                    <div class="col-12">
                                        <div class="mb-1">
                                            <label for="message"
                                                   class="required form-label">{{__('locale.labels.message')}}</label>
                                            <textarea class="form-control" name="message" rows="5"
                                                      placeholder="{{ __('locale.labels.type_message') }}"
                                                      id="message"></textarea>

                                            <div class="d-flex justify-content-between">
                                                <small class="text-primary text-uppercase">
                                                    {{ __('locale.labels.remaining') }} : <span
                                                            id="remaining">160</span>
                                                    ( <span class="text-success"
                                                            id="charCount"> 0 </span>&nbsp;{{ __('locale.labels.characters') }}
                                                    )
                                                </small>
                                                <small class="text-primary text-uppercase">
                                                    {{ __('locale.labels.message') }}(s) : <span id="messages">1</span>
                                                    ({{ __('locale.labels.encoding') }} : <span class="text-success"
                                                                                                id="encoding">GSM_7BIT</span>)
                                                </small>
                                            </div>

                                            @error('message')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>
                                    </div>
                        </div>
                    </div>

                    <div class="bm-card" style="padding: 18px;">
                        <span class="bm-card__title">{{ __('portal.preview') }}</span>
                        <div class="bm-phone">
                            <div class="bm-phone__time bm-time">--:--</div>
                            <div class="bm-phone__bubble bmPreviewText" data-placeholder="{{ __('portal.preview_placeholder') }}">{{ __('portal.preview_placeholder') }}</div>
                            <div class="bm-phone__from bmPreviewFrom"></div>
                        </div>
                    </div>
                </div>

                {{-- Step 3 · Schedule --}}
                <div data-step-panel="3" style="display: none;">
                    <div class="bm-card" style="padding: 18px;">
                        <div class="row">
                                    <div class="col-12">
                                        <div class="mb-1">
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" id="schedule" class="form-check-input schedule"
                                                       value="true"
                                                       name="schedule" {{ old('schedule') ? "checked" : null }}
                                                >
                                                <label class="form-check-label"
                                                       for="schedule">{{__('locale.campaigns.schedule_campaign')}}
                                                    ?</label>
                                            </div>
                                            <p>
                                                <small class="text-primary px-2">{{__('locale.campaigns.schedule_campaign_note')}}</small>
                                            </p>
                                        </div>
                                    </div>
                        </div>
                                <div class="row schedule_time">
                                    <div class="col-md-6">
                                        <div class="mb-1">
                                            <label for="schedule_date"
                                                   class="form-label">{{ __('locale.labels.date') }}</label>
                                            <input type="text" id="schedule_date" name="schedule_date"
                                                   class="form-control schedule_date" placeholder="YYYY-MM-DD" />
                                            @error('schedule_date')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-1">
                                            <label for="time" class="form-label">{{ __('locale.labels.time') }}</label>
                                            <input type="text" id="time" class="form-control flatpickr-time text-start"
                                                   name="schedule_time" placeholder="HH:MM" />
                                            @error('schedule_time')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-1">
                                            <label for="timezone"
                                                   class="form-label">{{__('locale.labels.timezone')}}</label>
                                            <select class="form-select select2" id="timezone" name="timezone">
                                                @foreach(Tool::allTimeZones() as $timezone)
                                                    <option value="{{$timezone['zone']}}" {{ Auth::user()->timezone == $timezone['zone'] ? 'selected': null }}> {{ $timezone['text'] }}</option>
                                                @endforeach
                                            </select>
                                            @error('timezone')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-1">
                                            <label for="frequency_cycle"
                                                   class="form-label">{{__('locale.labels.frequency')}}</label>
                                            <select class="form-select" id="frequency_cycle" name="frequency_cycle">
                                                <option value="onetime" {{old('frequency_cycle')}}> {{__('locale.labels.one_time')}}</option>
                                                <option value="daily" {{old('frequency_cycle')}}> {{__('locale.labels.daily')}}</option>
                                                <option value="monthly" {{old('frequency_cycle')}}> {{__('locale.labels.monthly')}}</option>
                                                <option value="yearly" {{old('frequency_cycle')}}> {{__('locale.labels.yearly')}}</option>
                                                <option value="custom" {{old('frequency_cycle')}}> {{__('locale.labels.custom')}}</option>
                                            </select>
                                        </div>
                                        @error('frequency_cycle')
                                        <p><small class="text-danger">{{ $message }}</small></p>
                                        @enderror
                                    </div>

                                    <div class="col-sm-6 col-12 show-custom">
                                        <div class="mb-1">
                                            <label for="frequency_amount"
                                                   class="form-label">{{__('locale.plans.frequency_amount')}}</label>
                                            <input type="text"
                                                   id="frequency_amount"
                                                   class="form-control text-right @error('frequency_amount') is-invalid @enderror"
                                                   name="frequency_amount"
                                                   value="{{ old('frequency_amount') }}"
                                            >
                                            @error('frequency_amount')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-12 show-custom">
                                        <div class="mb-1">
                                            <label for="frequency_unit"
                                                   class="form-label">{{__('locale.plans.frequency_unit')}}</label>
                                            <select class="form-select" id="frequency_unit" name="frequency_unit">
                                                <option value="day" {{old('frequency_unit')}}> {{__('locale.labels.day')}}</option>
                                                <option value="week" {{old('frequency_unit')}}> {{__('locale.labels.week')}}</option>
                                                <option value="month" {{old('frequency_unit')}}> {{__('locale.labels.month')}}</option>
                                                <option value="year" {{old('frequency_unit')}}> {{__('locale.labels.year')}}</option>
                                            </select>
                                        </div>
                                        @error('frequency_unit')
                                        <p><small class="text-danger">{{ $message }}</small></p>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 show-recurring">
                                        <div class="mb-1">
                                            <label for="recurring_date"
                                                   class="form-label"> {{ __('locale.labels.end_date') }}</label>
                                            <input type="text" id="recurring_date" name="recurring_date"
                                                   class="form-control schedule_date" placeholder="YYYY-MM-DD" />
                                            @error('recurring_date')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6 show-recurring">
                                        <div class="mb-1">
                                            <label for="recurring_time"
                                                   class="form-label">{{ __('locale.labels.end_time') }}</label>
                                            <input type="text" id="recurring_time"
                                                   class="form-control flatpickr-time text-start" name="recurring_time"
                                                   placeholder="HH:MM" />
                                            @error('recurring_time')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-1">
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" id="advanced" name="advanced"
                                                       class="form-check-input advanced" value="true">
                                                <label class="form-check-label"
                                                       for="advanced">{{ __('locale.labels.advanced') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row advanced_div">
                                    <div class="col-12">
                                        <div class="mb-1">
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" id="send_copy" value="true" name="send_copy"
                                                       class="form-check-input">
                                                <label class="form-check-label"
                                                       for="send_copy">{{__('locale.campaigns.send_copy_via_email')}}</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-1">
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" id="create_template" value="true"
                                                       name="create_template" class="form-check-input">
                                                <label class="form-check-label"
                                                       for="create_template">{{__('locale.campaigns.create_template_based_message')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                    </div>
                </div>

                {{-- Step 4 · Review --}}
                <div data-step-panel="4" class="bm-grid bm-grid--split" style="--bm-cols: minmax(0, 1.4fr) minmax(0, 1fr); display: none;">
                    <div class="bm-card" style="padding: 18px;">
                        <span class="bm-card__title">{{ __('portal.step_review') }}</span>
                        <div class="d-flex flex-column" style="gap: 10px;">
                            <div class="bm-kv"><span>{{ __('locale.campaigns.campaign_reference') }}</span><span class="bm-trunc" id="rvName">—</span></div>
                            <div class="bm-kv"><span>{{ __('locale.labels.sender_id') }}</span><span class="bm-trunc" id="rvSender">—</span></div>
                            <div class="bm-kv"><span>{{ __('locale.contacts.contact_groups') }}</span><span class="bm-trunc" id="rvGroups">—</span></div>
                            <div class="bm-kv"><span>{{ __('portal.final_recipients') }}</span><span class="bm-mono" id="rvRecipients">0</span></div>
                            <div class="bm-kv"><span>{{ __('portal.sms_per_recipient') }}</span><span class="bm-mono" id="rvParts">1</span></div>
                            <div class="bm-kv"><span>{{ __('portal.step_schedule') }}</span><span id="rvSchedule">—</span></div>
                            <div class="bm-kv bm-kv--total"><span>{{ __('portal.total_cost') }}</span><span class="bm-mono" id="rvCost">0</span></div>
                        </div>
                    </div>
                    <div class="bm-card" style="padding: 18px;">
                        <span class="bm-card__title">{{ __('portal.preview') }}</span>
                        <div class="bm-phone">
                            <div class="bm-phone__time bm-time">--:--</div>
                            <div class="bm-phone__bubble bmPreviewText" data-placeholder="{{ __('portal.preview_placeholder') }}">{{ __('portal.preview_placeholder') }}</div>
                            <div class="bm-phone__from bmPreviewFrom"></div>
                        </div>
                    </div>
                </div>

                <div class="bm-row" style="justify-content: flex-end;">
                    <input type="hidden" value="plain" name="sms_type" id="sms_type">
                    <input type="hidden" value="{{ $plan_id }}" name="plan_id">
                    <button type="button" id="bmPrev" class="bm-btn bm-btn--lg" style="display: none;">{{ __('portal.back') }}</button>
                    <button type="button" id="bmNext" class="bm-btn bm-btn--primary bm-btn--lg"></button>
                    <button type="button" id="sendMessagePreview" class="bm-btn bm-btn--primary bm-btn--lg" style="display: none;">
                        <i data-feather="send"></i>
                        {{ __('locale.buttons.send') }}
                    </button>
                </div>

            </div>
        </form>
    </section>
    <!-- // Basic Vertical form layout section end -->

    <!-- message preview Modal -->
    @include('customer.Campaigns._messagePreviewModal')
    <!-- // Basic Vertical form layout section end -->

@endsection

@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>
    <script src="{{ asset(mix('js/scripts/dom-rules.js')) }}"></script>
@endsection


@section('page-script')

    <script src="{{ asset(mix('js/scripts/sms-counter.js')) }}"></script>

    <script>
      $(document).ready(function() {

        $(".schedule_date").flatpickr({
          minDate: "today",
          dateFormat: "Y-m-d",
          defaultDate: "{{ date('Y-m-d') }}"
        });

        $(".flatpickr-time").flatpickr({
          enableTime: true,
          noCalendar: true,
          dateFormat: "H:i",
          defaultDate: "{{ \Carbon\Carbon::now()->setTimezone(config('app.timezone'))->format('H:i') }}"
        });

        $(".sender_id").on("click", function() {
          $("#sender_id").prop("disabled", !this.checked);
});
let schedule = $(".schedule"),
          scheduleTime = $(".schedule_time"),
          finalSend = $("#finalSend");

        if (schedule.prop("checked") === true) {
          scheduleTime.show();
        } else {
          scheduleTime.hide();
        }

        $(".advanced_div").hide();

        schedule.change(function() {
          scheduleTime.fadeToggle();
        });

        $(".advanced").change(function() {
          $(".advanced_div").fadeToggle();
        });

        $.createDomRules({

          parentSelector: "body",
          scopeSelector: "form",
          showTargets: function(rule, $controller, condition, $targets) {
            $targets.fadeIn();
          },
          hideTargets: function(rule, $controller, condition, $targets) {
            $targets.fadeOut();
          },

          rules: [
            {
              controller: "#frequency_cycle",
              value: "custom",
              condition: "==",
              targets: ".show-custom"
            },
            {
              controller: "#frequency_cycle",
              value: "onetime",
              condition: "!=",
              targets: ".show-recurring"
            },
            {
              controller: ".message_type",
              value: "mms",
              condition: "==",
              targets: ".send-mms"
            }
          ]
        });


        $(".select2").each(function() {
          let $this = $(this);
          $this.wrap("<div class=\"position-relative\"></div>");
          $this.select2({
            // the following code is used to disable x-scrollbar when click in select input and
            // take 100% width in responsive also
            dropdownAutoWidth: true,
            width: "100%",
            dropdownParent: $this.parent()
          });
        });


        $(".sender_id_custom").select2({
          tags: true,  // Allows new values
          placeholder: "{{ __('locale.labels.select_one_or_insert_your_own') }}",
          allowClear: true
        });


        let $remaining = $("#remaining"),
          $char_count = $("#charCount"),
          $encoding = $("#encoding"),
          $messages = $("#messages"),
          $get_msg = $("#message"),
          merge_state = $("#available_tag"),
          firstInvalid = $("form").find(".is-invalid").eq(0);

        if (firstInvalid.length) {
          $("body, html").stop(true, true).animate({
            "scrollTop": firstInvalid.offset().top - 200 + "px"
          }, 200);
        }

        function isArabic(text) {
          let pattern = /[\u0600-\u06FF\u0750-\u077F]/;
          return pattern.test(text);
        }

        function get_character() {
          if ($get_msg[0].value !== null) {

            let data = SmsCounter.count($get_msg[0].value, true);

            if (data.encoding === "UTF16") {
              $("#sms_type").val("unicode").trigger("change");
              if (isArabic($(this).val())) {
                $get_msg.css("direction", "rtl");
              }
            } else {
              $("#sms_type").val("plain").trigger("change");
              $get_msg.css("direction", "ltr");
            }

            $char_count.text(data.length);
            $remaining.text(data.remaining + " / " + data.per_message);
            $messages.text(data.messages);
            $encoding.text(data.encoding);

          }
        }


        merge_state.on("change", function() {
          const caretPos = $get_msg[0].selectionStart;
          const textAreaTxt = $get_msg.val();
          let txtToAdd = this.value;
          if (txtToAdd) {
            txtToAdd = "{" + txtToAdd + "}";
          }

          $get_msg.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos));
        });


        $("#sms_template").on("change", function() {

          let template_id = $(this).val();
          $get_msg.val(""); // Clear the textarea content

          $.ajax({
            url: "{{ url('templates/show-data') }}" + "/" + template_id,
            type: "POST",
            data: {
              _token: "{{ csrf_token() }}"
            },
            cache: false,
            success: function(data) {
              if (data.status === "success") {
                const caretPos = $get_msg[0].selectionStart;
                const textAreaTxt = $get_msg.val();
                let txtToAdd = data.message;

                $("#dlt_template_id").val(data.dlt_template_id);

                $get_msg.val(textAreaTxt.substring(0, caretPos) + txtToAdd +
                  textAreaTxt.substring(caretPos)).val().length;

                get_character();

              } else {
                toastr["warning"](data.message,
                  "{{ __('locale.labels.attention') }}", {
                    closeButton: true,
                    positionClass: "toast-top-right",
                    progressBar: true,
                    newestOnTop: true,
                    rtl: isRtl
                  });
              }
            },
            error: function(reject) {
              if (reject.status === 422) {
                let errors = reject.responseJSON.errors;
                $.each(errors, function(key, value) {
                  toastr["warning"](value[0],
                    "{{ __('locale.labels.attention') }}", {
                      closeButton: true,
                      positionClass: "toast-top-right",
                      progressBar: true,
                      newestOnTop: true,
                      rtl: isRtl
                    });
                });
              } else {
                toastr["warning"](reject.responseJSON.message,
                  "{{ __('locale.labels.attention') }}", {
                    closeButton: true,
                    positionClass: "toast-top-right",
                    progressBar: true,
                    newestOnTop: true,
                    rtl: isRtl
                  });
              }
            }
          });
        });

        $get_msg.on("change keyup paste", get_character);

        $("#contact_groups").on("change", function() {

          let contact_id = $(this).val();

          if (contact_id === 0) {
            return false;
          }

          $.ajax({
            url: "{{ url('tags/get-data') }}" + "/" + contact_id,
            type: "POST",
            data: {
              _token: "{{ csrf_token() }}"
            },
            cache: false,
            success: function(data) {
              if (data.status === "success") {
                merge_state.empty();
                $.each(data.contactFields, function(index, field) {
                  merge_state.append("<option value=\"" + field.tag +
                    "\">" + field.label + "</option>");
                });

                // Trigger select2 to update the UI
                merge_state.select2();

              } else {
                toastr["warning"](data.message,
                  "{{ __('locale.labels.attention') }}", {
                    closeButton: true,
                    positionClass: "toast-top-right",
                    progressBar: true,
                    newestOnTop: true,
                    rtl: isRtl
                  });
              }
            },
            error: function(reject) {
              if (reject.status === 422) {
                let errors = reject.responseJSON.errors;
                $.each(errors, function(key, value) {
                  toastr["warning"](value[0],
                    "{{ __('locale.labels.attention') }}", {
                      closeButton: true,
                      positionClass: "toast-top-right",
                      progressBar: true,
                      newestOnTop: true,
                      rtl: isRtl
                    });
                });
              } else {

              }
            }
          });
        });

        $("#sendMessagePreview").on("click", function(e) {
          e.preventDefault();

          let msgData = SmsCounter.count($get_msg.val(), true),
            campaignName = $("#name"),
            senderId = $("#sender_id"),
            message = $get_msg,

            msgCount = msgData.messages,
            msgLength = msgData.length,
            msgRecipients = $("#msgRecepients"),
            msg = $("#message").val();
          $("#msgLength").html(msgLength);
          $("#msgCost").html(msgCount);
          msgRecipients.html(msgRecipients.data("loading-text"));

          $("#msg").html(msg);

          //show message count
          const contact_group_ids = $("#contact_groups").val();

          // validate fields
          if (contact_group_ids.length < 1 || campaignName.val().length < 1 || message.val().length < 1) {
            toastr["warning"]("{{ __('locale.auth.insert_required_fields') }}",
              "{{ __('locale.labels.attention') }}", {
                closeButton: true,
                positionClass: "toast-top-right",
                progressBar: true,
                newestOnTop: true,
                rtl: isRtl
              });
            return;
          }

          $.ajax({
            url: "{{ route('customer.contacts.count_contact') }}",
            type: "POST",
            data: {
              _token: "{{ csrf_token() }}",
              contact_group_ids: contact_group_ids
            },
            cache: false,
            success: function(data) {
              msgRecipients.html((Number(data)));
            }
          });

          $("#messagePreview").modal("show");
        });

        finalSend.on("click", function(e) {
          e.preventDefault();

          const form = $("#campaign-builder-form");

          // Disable the button to prevent multiple clicks
          finalSend.attr("disabled", true);

          // Optionally, show a loading spinner or change button text
          finalSend.text("{{ __('locale.labels.processing') }}");

          // Submit the form
          form.submit();

          // The button should stay disabled during form submission
          // If it's a traditional form submission, the page will reload, so no need to re-enable it here
        });


        $("#phoneMessagePreview").on("click", function() {
          const msg = $("#message").val();
          $("#senderid").html($("#sender_id").val());
          $("#messageto").html(msg);
          $("#phonePreview").modal("show");
        });

        // ---- Stepper: Recipients → Message → Schedule → Review ----
        const stepLabels = {
          1: @json(__('portal.step_recipients')),
          2: @json(__('portal.step_message')),
          3: @json(__('portal.step_schedule')),
          4: @json(__('portal.step_review'))
        };
        let currentStep = 1;

        function refreshReview() {
          const parts = Number($messages.text()) || 1;
          const total = Number($("#bmFinalCount").text().replace(/[^0-9]/g, "")) || 0;
          const groups = $("#contact_groups option:selected").map(function() {
            return $.trim($(this).text()).replace(/\s*\([^)]*\)\s*$/, "");
          }).get().join(", ");
          const msg = $get_msg.val();
          const $bubble = $(".bmPreviewText");
          $bubble.text(msg.length ? msg : $bubble.first().data("placeholder"));
          $(".bmPreviewFrom").text($("#sender_id").val() || "");
          $("#rvName").text($("#name").val() || "—");
          $("#rvSender").text($("#sender_id").val() || "—");
          $("#rvGroups").text(groups || "—");
          $("#rvRecipients").text(total.toLocaleString());
          $("#rvParts").text(parts);
          $("#rvCost").text((total * parts).toLocaleString());
          $("#rvSchedule").text($(".schedule").prop("checked")
            ? ($("#schedule_date").val() + " " + $("#time").val())
            : @json(__('portal.send_now')));
        }

        function goStep(n) {
          currentStep = n;
          $("[data-step-panel]").hide().filter("[data-step-panel='" + n + "']").show();
          $(".bm-step").removeClass("is-active").filter("[data-step='" + n + "']").addClass("is-active");
          $("#bmPrev").toggle(n > 1);
          $("#bmNext").toggle(n < 4).text(n < 4 ? @json(__('portal.next')) + ": " + stepLabels[n + 1] : "");
          $("#sendMessagePreview").toggle(n === 4);
          if (n === 2 || n === 4) {
            refreshReview();
          }
        }

        $(".bm-step").on("click", function() {
          goStep(Number($(this).data("step")));
        });
        $("#bmNext").on("click", function() {
          goStep(Math.min(4, currentStep + 1));
        });
        $("#bmPrev").on("click", function() {
          goStep(Math.max(1, currentStep - 1));
        });

        // Live final-recipient count for the selected groups
        $("#contact_groups").on("change", function() {
          const ids = $(this).val() || [];
          if (!ids.length) {
            $("#bmFinalCount").text("0");
            return;
          }
          $.ajax({
            url: "{{ route('customer.contacts.count_contact') }}",
            type: "POST",
            data: {_token: "{{ csrf_token() }}", contact_group_ids: ids},
            cache: false,
            success: function(data) {
              $("#bmFinalCount").text(Number(data).toLocaleString());
            }
          });
        });

        $get_msg.on("change keyup paste", refreshReview);
        $("#sms_template").on("change", function() {
          setTimeout(refreshReview, 600);
        });

        // Land on the step that holds the first server-side validation error
        const invalidPanel = firstInvalid.closest("[data-step-panel]");
        goStep(invalidPanel.length ? Number(invalidPanel.data("step-panel")) : 1);

        //Make mobile preview time lively
        setInterval(function() {
          let date = new Date();
          let hours = date.getHours() < 10 ? "0" + date.getHours() : date.getHours();
          let minutes = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes();
          let seconds = date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds();
          $(".top-section-time").html(
            hours + ":" + minutes + ":" + seconds
          );
          $(".bm-time").text(hours + ":" + minutes);
        }, 500);

      });
    </script>
@endsection
