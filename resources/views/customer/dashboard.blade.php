@php
    use App\Library\Tool;

    // Secondary channels (voice/MMS/WhatsApp/Viber/OTP): permissions + label key, used by cards and charts
    $bmExtra = [
        'voice'    => [['voice_campaign_builder', 'voice_quick_send', 'voice_bulk_messages'], 'voice_sms'],
        'mms'      => [['mms_campaign_builder', 'mms_quick_send', 'mms_bulk_messages'], 'mms_sms'],
        'whatsapp' => [['whatsapp_campaign_builder', 'whatsapp_quick_send', 'whatsapp_bulk_messages'], 'whatsapp_sms'],
        'viber'    => [['viber_campaign_builder', 'viber_quick_send', 'viber_bulk_messages'], 'viber_sms'],
        'otp'      => [['otp_campaign_builder', 'otp_quick_send', 'otp_bulk_messages'], 'otp_sms'],
    ];
@endphp
@extends('layouts/contentLayoutMaster')

@section('title', __('locale.menu.Dashboard'))

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/charts/apexcharts.css')) }}">
@endsection
@section('page-style')
    {{-- Page css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/dashboard-ecommerce.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/charts/chart-apex.css')) }}">
@endsection

@section('content')
    {{-- Dashboard Analytics Start --}}
    <section>
        @if(Auth::user()->isPendingVerification())
            <div class="row">
                <div class="col-12">
                    <div class="card bg-light-warning">
                        <div class="card-body text-center p-5">
                            <i data-feather="clock" class="font-large-2 mb-1 text-warning"></i>
                            <h2 class="text-warning font-weight-bolder">Your account verification is pending</h2>
                            <p class="font-medium-2">Our team will review your documents soon. You will receive an email once your account is verified.</p>
                            <p>You currently have limited access to the platform.</p>
                        </div>
                    </div>
                </div>
            </div>
        @elseif(Auth::user()->isRejected())
            <div class="row">
                <div class="col-12">
                    <div class="card bg-light-danger">
                        <div class="card-body text-center p-5">
                            <i data-feather="alert-octagon" class="font-large-2 mb-1 text-danger"></i>
                            <h2 class="text-danger font-weight-bolder">Document verification was rejected</h2>
                            <div class="alert alert-danger mt-2 mb-2">
                                <h4 class="alert-heading">Reason for rejection:</h4>
                                <p class="mb-0 font-medium-1">{{ Auth::user()->latestVerification->admin_notes ?? 'No reason provided.' }}</p>
                            </div>
                            <p class="font-medium-2">Please re-upload your valid documents for verification.</p>
                            <button type="button" class="btn btn-danger mt-1" data-bs-toggle="modal" data-bs-target="#resubmitVerificationModal">
                                <i data-feather="upload-cloud"></i> Resubmit Documents
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resubmit Modal -->
            <div class="modal fade" id="resubmitVerificationModal" tabindex="-1" aria-labelledby="resubmitVerificationModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-danger">
                            <h5 class="modal-title text-white" id="resubmitVerificationModalLabel">Resubmit Verification Documents</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('customer.verification.resubmit') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body p-2">
                                <div class="mb-1">
                                    <label class="form-label required" for="nid_upload">National ID (NID)</label>
                                    <input type="file" name="nid_upload" id="nid_upload" class="form-control" required />
                                </div>
                                <div class="mb-1">
                                    <label class="form-label" for="trade_license">Trade License (Optional)</label>
                                    <input type="file" name="trade_license" id="trade_license" class="form-control" />
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger">Submit for Review</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        @if(Auth::user()->isVerified())

        @unless($userAnnouncements->isEmpty())
            <div class="row match-height">
                <div class="col-12 announcement-card">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <h4 class="card-title">{{ __('locale.menu.Announcements') }}</h4>
                            </div>
                            <a href="#" class="mark_read h5 text-muted text-uppercase"><i data-feather="x-circle"
                                                                                          class="font-medium-3 cursor-pointer"></i> {{ __('locale.buttons.close') }}
                            </a>
                        </div>
                        <hr class="my-0">
                        <div class="card-body alert-primary">
                            <ul class="timeline ms-50">
                                @foreach($userAnnouncements as $announcement)
                                    @if($announcement->pivot->read_at === null)
                                        <li class="timeline-item">
                                            <span class="timeline-point timeline-point-indicator"></span>
                                            <div class="timeline-event">
                                                <div class="d-flex justify-content-between flex-sm-row flex-column mb-sm-0 mb-1">
                                                    <a href="{{ route('user.account.announcement.view', $announcement->uid) }}">
                                                        <h6>{{ $announcement->title }}</h6></a>
                                                    <span class="timeline-event-time me-1">{{ $announcement->created_at->diffForHumans() }}</span>
                                                </div>
                                                <p>{!! $announcement->description !!}</p>

                                            </div>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endunless

        @php
            $bmTotal     = max((int) $total_sms_sent, 0);
            $bmDelivered = (int) $deliveredCount;
            $bmFailed    = (int) $undeliveredCount;
            $bmRate      = $bmTotal > 0 ? round($bmDelivered / $bmTotal * 100, 1) : 0;
            $bmFailRate  = $bmTotal > 0 ? round($bmFailed / $bmTotal * 100, 1) : 0;
            $bmWeekTotal = $weekStats->sum(fn ($d) => $d['plain'] + $d['unicode']);
            $bmWeekMax   = max(1, $weekStats->max(fn ($d) => $d['plain'] + $d['unicode']));
            $bmCustomer  = Auth::user()->customer;
            $bmContacts  = $bmCustomer ? (int) $bmCustomer->subscriberCounts() : 0;
            $bmGroups    = $bmCustomer ? (int) $bmCustomer->listsCount() : 0;
        @endphp

        <div class="bm-page">

            <div>
                <h1 class="bm-page-title">{{ \App\Helpers\Helper::greetingMessage() }}</h1>
                <div class="bm-page-sub">{{ __('portal.week_sent', ['count' => Tool::format_number($bmWeekTotal)]) }}</div>
            </div>

            {{-- Stat tiles --}}
            <div class="bm-grid bm-grid--stats">
                <div class="bm-stat">
                    <span class="bm-stat__label">SMS Credits Balance</span>
                    <span class="bm-stat__value">{{ number_format(Auth::user()->sms_balance) }}</span>
                    <a class="bm-stat__sub bm-stat__sub--link" href="{{ route('customer.buy_sms.index') }}">Buy SMS →</a>
                </div>
                <div class="bm-stat">
                    <span class="bm-stat__label">{{ __('locale.labels.delivered') }}</span>
                    <span class="bm-stat__value">{{ number_format($bmDelivered) }}</span>
                    <span class="bm-stat__sub bm-stat__sub--ok">{{ __('portal.delivery_rate', ['rate' => $bmRate]) }}</span>
                </div>
                <div class="bm-stat">
                    <span class="bm-stat__label">{{ __('locale.labels.failed') }}</span>
                    <span class="bm-stat__value">{{ number_format($bmFailed) }}</span>
                    <a class="bm-stat__sub bm-stat__sub--bad" href="{{ route('customer.reports.all') }}">{{ $bmFailRate }}% · {{ __('portal.view_report') }}</a>
                </div>
                <div class="bm-stat">
                    <span class="bm-stat__label">{{ __('locale.menu.Contacts') }}</span>
                    <span class="bm-stat__value">{{ number_format($bmContacts) }}</span>
                    <span class="bm-stat__sub">{{ __('portal.in_groups', ['count' => $bmGroups]) }}</span>
                </div>
            </div>

            <div class="bm-grid bm-grid--split" style="--bm-cols: minmax(0, 2fr) minmax(0, 1fr);">

                {{-- SMS statistics: last 7 days, Plain vs Unicode --}}
                <div class="bm-card">
                    <div class="bm-row bm-row--between">
                        <span class="bm-card__title">SMS Statistics</span>
                        <div class="bm-legend">
                            <span><i style="background: var(--bm-primary)"></i>Plain</span>
                            <span><i style="background: #c7d2fe"></i>Unicode</span>
                        </div>
                    </div>
                    <div class="bm-bars" @if($bmWeekTotal === 0) style="align-items: center; justify-content: center;" @endif>
                        @if($bmWeekTotal === 0)
                            <span class="bm-muted" style="font-size: 13px;">{{ __('portal.no_data') }}</span>
                        @endif
                        @foreach($weekStats as $day)
                            <div class="bm-bars__col" title="{{ $day['label'] }} — Plain {{ $day['plain'] }} · Unicode {{ $day['unicode'] }}">
                                @if($day['unicode'] > 0)
                                    <div class="bm-bars__bar bm-bars__bar--uni" style="height: {{ max(2, round($day['unicode'] / $bmWeekMax * 100, 1)) }}%"></div>
                                @endif
                                @if($day['plain'] > 0)
                                    <div class="bm-bars__bar" style="height: {{ max(2, round($day['plain'] / $bmWeekMax * 100, 1)) }}%"></div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="bm-bars-axis">
                        @foreach($weekStats as $day)
                            <span>{{ $day['label'] }}</span>
                        @endforeach
                    </div>
                </div>

                {{-- Sender IDs --}}
                <div class="bm-card">
                    <span class="bm-card__title">Sender IDs</span>
                    @forelse($senderIds as $sid)
                        <div class="bm-row bm-row--between" style="flex-wrap: nowrap; {{ $loop->last ? '' : 'padding-bottom: 10px; border-bottom: 1px solid var(--bm-line-soft);' }}">
                            <span class="bm-trunc" style="font-size: 13px; font-weight: 500;">{{ $sid->sender_id }}</span>
                            <x-bm.status :value="$sid->status" />
                        </div>
                    @empty
                        <span class="bm-muted" style="font-size: 13px;">{{ __('portal.no_sender_ids') }}</span>
                    @endforelse
                    <a class="bm-btn bm-btn--block" style="margin-top: auto;" href="{{ route('customer.senderid.request') }}">{{ __('portal.request_sender_id') }}</a>
                </div>
            </div>

            {{-- Recent campaigns --}}
            <div class="bm-card bm-card--flush">
                <div class="bm-card__head">
                    <span class="bm-card__title">{{ __('portal.recent_campaigns') }}</span>
                    <a class="bm-link" href="{{ route('customer.reports.campaigns') }}">{{ __('portal.view_all') }}</a>
                </div>
                <div class="bm-tbl" style="--bm-cols: minmax(0, 2fr) repeat(4, minmax(0, 1fr));">
                    <div class="bm-tbl__head">
                        <span>Campaign</span><span>Type</span><span>Recipients</span><span>Delivered</span><span>Status</span>
                    </div>
                    @forelse($recentCampaigns as $campaign)
                        <div class="bm-tbl__row">
                            <span style="font-weight: 500;">{{ $campaign->campaign_name }}</span>
                            <span class="bm-muted">{{ ucfirst($campaign->sms_type) }}</span>
                            <span class="bm-mono">{{ number_format($campaign->contactCount(true)) }}</span>
                            <span class="bm-mono">{{ $campaign->status === \App\Models\Campaigns::STATUS_DONE ? number_format($campaign->deliveredCount(true)) : '—' }}</span>
                            <x-bm.status :value="$campaign->status" />
                        </div>
                    @empty
                        <div class="bm-tbl__row"><span class="bm-muted" style="grid-column: 1 / -1;">{{ __('portal.no_campaigns') }}</span></div>
                    @endforelse
                </div>
            </div>

            {{-- Other channels the account can use (voice / MMS / WhatsApp / Viber / OTP) --}}
            @foreach($bmExtra as $channel => [$permissions, $labelKey])
                @canany($permissions)
                    <div class="bm-card">
                        <span class="bm-card__title">{{ __('locale.labels.sms_statistics', ['sms_type' => __('locale.labels.' . $labelKey)]) }}</span>
                        <div id="{{ $channel }}_sms_data"></div>
                    </div>
                @endcanany
            @endforeach

        </div>

        @endif
    </section>
    <!-- Dashboard Analytics end -->
@endsection


@section('vendor-script')
    {{--     Vendor js files --}}
    <script src="{{ asset(mix('vendors/js/charts/apexcharts.min.js')) }}"></script>
@endsection


@section('page-script')

    <script>
        $(window).on("load", function () {

            $(".mark_read").on("click", function (e) {
                e.stopPropagation();

                $.ajax({
                    url: "{{ route('user.account.announcement.mark-all-as-read') }}",
                    type: "POST",
                    data: {
                        _token: "{{csrf_token()}}"
                    },
                    success: function (response) {
                        if (response.success) {
                            $('.announcement-card').fadeOut(1000);
                        } else {
                            toastr['warning'](response.message, "{{__('locale.labels.attention')}}", {
                                closeButton: true,
                                positionClass: 'toast-top-right',
                                progressBar: true,
                                newestOnTop: true,
                                rtl: isRtl
                            });
                        }
                    },
                    error: function (error) {
                        toastr['warning'](error.responseText, "{{__('locale.labels.attention')}}", {
                            closeButton: true,
                            positionClass: 'toast-top-right',
                            progressBar: true,
                            newestOnTop: true,
                            rtl: isRtl
                        });
                    }
                });

            });

            // Flat line chart for the secondary channel cards (zinc grid, indigo accent)
            function createChartOptions(height, xAxis, dataSet) {
                const muted = '#71717a';
                return {
                    chart: {height: height, toolbar: {show: false}, zoom: {enabled: false}, type: 'line', fontFamily: 'inherit'},
                    stroke: {curve: 'smooth', width: 2},
                    grid: {borderColor: '#f4f4f5'},
                    legend: {show: true, position: 'top', horizontalAlign: 'right', labels: {colors: muted}},
                    colors: ['#4f46e5', '#c7d2fe', '#a1a1aa'],
                    markers: {size: 0, hover: {size: 4}},
                    xaxis: {
                        labels: {style: {colors: muted, fontSize: '12px'}},
                        axisTicks: {show: false},
                        axisBorder: {show: false},
                        categories: xAxis
                    },
                    yaxis: {
                        tickAmount: 5,
                        labels: {
                            style: {colors: muted, fontSize: '12px'},
                            formatter: function (val) {
                                return val > 999 ? (val / 1000).toFixed(0) + 'k' : val;
                            }
                        }
                    },
                    tooltip: {x: {show: false}},
                    series: dataSet
                };
            }

            const extraCharts = {
                @foreach($bmExtra as $channel => $cfg)
                    '{{ $channel }}': {x: {!! $charts[$channel]->xAxis() !!}, s: {!! $charts[$channel]->dataSet() !!}},
                @endforeach
            };

            Object.keys(extraCharts).forEach(function (key) {
                const el = document.querySelector('#' + key + '_sms_data');
                if (!el) return; // channel not enabled for this account
                new ApexCharts(el, createChartOptions(240, extraCharts[key].x, extraCharts[key].s)).render();
            });

        });

    </script>

@endsection
