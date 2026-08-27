@extends('layouts/contentLayoutMaster')

@section('title', 'Buy SMS')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
@endsection

@section('page-style')
    <style>
        /* Core-aligned Stats Cards */
        .stat-card {
            border: none;
            box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1);
            transition: all 0.3s ease-in-out;
        }
        .stat-card .card-body { padding: 1.5rem; }
        .avatar .avatar-content i { width: 20px; height: 20px; }

        /* Pricing Cards for Mobile */
        .tier-card {
            border: 1px solid #ebe9f1;
            border-radius: 0.428rem;
            margin-bottom: 1rem;
            background-color: #fff;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .tier-card:hover { border-color: #4F46E5; box-shadow: 0 4px 12px 0 rgba(115, 103, 240, 0.1); }
        .tier-card.active { border-color: #4F46E5; background-color: #f8f7ff; }
        
        @media (max-width: 768px) {
            .pricing-table-desktop { display: none; }
            .tier-grid { display: block; }
            .stat-card .card-body { padding: 1rem; }
        }
        @media (min-width: 769px) {
            .tier-grid { display: none; }
        }

        .payment-list-item {
            border: 2px solid #ebe9f1 !important;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #fff;
            display: block;
            margin-bottom: 0.75rem;
        }
        .payment-list-item:hover { border-color: #4F46E5 !important; transform: translateX(5px); }
        .custom-option-item-check:checked+.payment-list-item { border-color: #4F46E5 !important; background-color: rgba(115, 103, 240, 0.05); }
    </style>
@endsection

@section('content')
    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-2">
        <div>
            <h4 class="mb-0 fw-bolder"><i data-feather="shopping-cart" class="me-50 text-primary"></i> Buy SMS Credits</h4>
            <p class="text-muted small mb-0">Recharge your balance instantly</p>
        </div>
        <a href="{{ route('customer.buy_sms.history') }}" class="btn btn-sm btn-outline-secondary">
            <i data-feather="list"></i> <span class="d-none d-sm-inline">Order History</span>
        </a>
    </div>

    {{-- Stats Container (Proper Grid) --}}
    <div class="row match-height mb-1">
        <div class="col-lg-4 col-sm-6 col-12 mb-1">
            <div class="card stat-card mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar bg-light-primary p-50 me-1">
                        <div class="avatar-content"><i data-feather="database" class="font-medium-5"></i></div>
                    </div>
                    <div class="my-auto">
                        <h4 class="fw-bolder mb-0">{{ number_format($balance) }}</h4>
                        <p class="card-text text-muted font-small-3">Current Balance</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6 col-12 mb-1">
            <div class="card stat-card mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar bg-light-success p-50 me-1">
                        <div class="avatar-content"><i data-feather="trending-up" class="font-medium-5"></i></div>
                    </div>
                    <div class="my-auto">
                        <h4 class="fw-bolder mb-0">{{ $tiers->count() }}</h4>
                        <p class="card-text text-muted font-small-3">Active Tiers</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6 col-12 mb-1">
            <div class="card stat-card mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar bg-light-info p-50 me-1">
                        <div class="avatar-content"><i data-feather="shopping-bag" class="font-medium-5"></i></div>
                    </div>
                    <div class="my-auto">
                        <h4 class="fw-bolder mb-0">{{ \App\Models\SmsPurchase::where('user_id', $user->id)->count() }}</h4>
                        <p class="card-text text-muted font-small-3">Total Orders</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row match-height">
        {{-- Selection Area --}}
        <div class="col-lg-7 col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="fw-bolder mb-1">Step 1: Select or Enter Quantity</h5>
                    
                    {{-- Desktop Table --}}
                    <div class="table-responsive pricing-table-desktop mb-2">
                        <table class="table table-hover border">
                            <thead class="table-light">
                                <tr>
                                    <th>SMS Range</th>
                                    <th>Rate</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tiers as $tier)
                                    <tr>
                                        <td>{{ number_format($tier->min_qty) }} - {{ $tier->max_qty ? number_format($tier->max_qty) : '∞' }} SMS</td>
                                        <td><span class="badge badge-light-primary fs-6">৳{{ number_format($tier->rate, 2) }}</span></td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-primary tier-pick-btn" data-min="{{ $tier->min_qty }}">Select</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile Grid Cards (Hidden on Desktop) --}}
                    <div class="tier-grid mb-2">
                        @foreach($tiers as $tier)
                            <div class="tier-card p-1 tier-pick-btn" data-min="{{ $tier->min_qty }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bolder text-primary">৳{{ number_format($tier->rate, 2) }}/SMS</div>
                                        <div class="small text-muted">{{ number_format($tier->min_qty) }} - {{ $tier->max_qty ? number_format($tier->max_qty) : '∞' }} SMS</div>
                                    </div>
                                    <i data-feather="chevron-right" class="text-muted"></i>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold">Custom Quantity</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text"><i data-feather="edit-3"></i></span>
                            <input type="number" id="sms-quantity" class="form-control" placeholder="Enter SMS Amount" value="{{ $tiers->first()->min_qty ?? '' }}">
                        </div>
                        <small class="text-primary fw-bold mt-50 d-block"><i data-feather="info" class="me-25" style="width: 14px;"></i> Rate is calculated automatically based on quantity.</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Summary Area --}}
        <div class="col-lg-5 col-12">
            <div class="card bg-light-primary border-0">
                <div class="card-body">
                    <h5 class="fw-bolder mb-2">Step 2: Order Summary</h5>
                    
                    <div class="bg-white rounded p-1 mb-2 shadow-sm">
                        <div class="d-flex justify-content-between mb-75">
                            <span class="text-muted">SMS Quantity</span>
                            <span class="fw-bolder" id="q-qty">0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-75">
                            <span class="text-muted">Rate per SMS</span>
                            <span class="fw-bolder text-info" id="q-rate">৳0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Pricing Tier</span>
                            <span class="badge badge-light-info" id="q-tier">NA</span>
                        </div>
                        <div class="border-top pt-1 d-flex justify-content-between">
                            <h4 class="fw-bolder mb-0">Payable Amount</h4>
                            <h4 class="fw-bolder text-primary mb-0" id="q-total">৳0.00</h4>
                        </div>
                    </div>

                    <button class="btn btn-primary btn-lg w-100 mb-1" id="checkout-btn" disabled onclick="proceedToCheckout()">
                        <i data-feather="zap" class="me-50"></i> Continue to Payment
                    </button>

                    <div class="text-center mb-1"><span class="small text-muted">OR PAY OFFLINE</span></div>

                    <div class="mb-1">
                        <input type="text" id="txn-id" class="form-control text-center" placeholder="Transaction Id (Manual)">
                    </div>
                    <button class="btn btn-outline-success w-100" id="submit-btn" disabled onclick="submitPurchase()">
                        Submit Payment Request
                    </button>
                    
                    <div class="mt-2 text-center">
                        <p class="small text-muted"><i data-feather="shield" class="me-25" style="width: 12px;"></i> Secure & Verified Transaction</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Gateways Modal --}}
    <div class="modal fade" id="paymentGatewaysModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-transparent border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-3 pb-3 pt-0">
                    <div class="text-center mb-2 pt-2">
                        <h3 class="fw-bolder">Choose Payment Method</h3>
                        <p class="text-muted small">Select how you would like to pay for your credits</p>
                    </div>

                    <div class="payment-methods-list">
                        @foreach ($payment_methods as $method)
                            <div class="mb-1">
                                <input class="custom-option-item-check d-none" type="radio" name="payment_gateway_select"
                                    id="gateway-{{ $method->uid }}" value="{{ $method->type }}" />
                                <label class="payment-list-item p-50 mb-0 w-100 d-flex align-items-center justify-content-center position-relative" for="gateway-{{ $method->uid }}"
                                    onclick="selectGateway('{{ $method->type }}')">
                                    <div class="payment-logo-wrapper d-flex align-items-center justify-content-center" style="width: 100%; height: 65px;">
                                        @php
                                            // Dynamic logo path from database option if exists, otherwise fallback
                                            $customLogo = $method->getOption('gateway_logo');
                                            $logoUrl = $customLogo ? asset($customLogo) : asset('images/payments/' . $method->type . '.png');
                                        @endphp
                                        
                                        @if($customLogo && file_exists(public_path($customLogo)))
                                            <img src="{{ $logoUrl }}" alt="{{ $method->name }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                        @elseif($method->type === 'bkash')
                                            <img src="https://www.logo.wine/a/logo/BKash/BKash-Logo.wine.svg" alt="bKash" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                        @elseif(file_exists(public_path('images/payments/' . $method->type . '.png')))
                                            <img src="{{ asset('images/payments/' . $method->type . '.png') }}" alt="{{ $method->name }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                        @else
                                            <h4 class="fw-bolder text-uppercase mb-0 text-primary">{{ $method->name }}</h4>
                                        @endif
                                    </div>
                                    <div class="check-mark position-absolute end-0 me-1 d-none text-success">
                                        <i data-feather="check-circle" style="width: 24px; height: 24px;"></i>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <div id="offline-verification-section" style="display: none;" class="mt-2 p-1 rounded bg-light-warning">
                        <h6 class="fw-bolder text-warning mb-50"><i data-feather="info" class="me-25"></i> Offline Instructions</h6>
                        <p class="small mb-1">Make payment to our account and enter the transaction ID below.</p>
                        <input type="text" id="modal-txn-id" class="form-control" placeholder="Enter Transaction ID">
                    </div>

                    <form id="payment-gateways-form" action="{{ route('user.account.pay') }}" method="POST">
                        @csrf
                        <input type="hidden" name="payment_methods" id="form-payment-method">
                        <input type="hidden" name="sms_unit" id="form-sms-unit">
                        <input type="hidden" name="balance" id="form-balance">
                        <input type="hidden" name="purchase_type" value="sms_credit">

                        <input type="hidden" name="first_name" value="{{ Auth::user()->first_name }}">
                        <input type="hidden" name="last_name" value="{{ Auth::user()->last_name }}">
                        <input type="hidden" name="email" value="{{ Auth::user()->email }}">
                        <input type="hidden" name="phone" value="{{ Auth::user()->customer->phone }}">
                        <input type="hidden" name="address" value="{{ Auth::user()->customer->address }}">
                        <input type="hidden" name="city" value="{{ Auth::user()->customer->city }}">
                        <input type="hidden" name="country" value="{{ Auth::user()->customer->country }}">
                    </form>

                    <div class="d-grid mt-3">
                        <button type="button" class="btn btn-primary btn-lg shadow" id="confirm-payment-btn" disabled>
                            Complete Order & Pay
                        </button>
                        <button type="button" class="btn btn-link text-muted mt-50" data-bs-dismiss="modal">Cancel & Back</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('vendor-script')
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
@endsection

@section('page-script')
    <script>
        let debounceTimer;
        let currentQuote = null;

        $(document).ready(function () {
            "use strict";
            feather.replace();

            // Auto-calculate on input
            $('#sms-quantity').on('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    getQuote();
                }, 400);
            });

            // Tier select button
            $('.tier-pick-btn').on('click', function () {
                $('#sms-quantity').val($(this).data('min')).trigger('input');
            });

            let selectedGateway = null; // Initialize selectedGateway

            window.selectGateway = function(type) {
                selectedGateway = type;
                $('#form-payment-method').val(selectedGateway);
                $('#confirm-payment-btn').prop('disabled', false);

                if (selectedGateway === 'offline_payment') {
                    $('#offline-verification-section').slideDown();
                    $('#confirm-payment-btn').find('span').text('Confirm Purchase');
                } else {
                    $('#offline-verification-section').slideUp();
                    $('#confirm-payment-btn').find('span').text('Pay Now');
                }
            };

            $('#confirm-payment-btn').on('click', function() {
                if (!selectedGateway) return;

                if (selectedGateway === 'offline_payment') {
                    let txnId = $('#modal-txn-id').val();
                    if (!txnId) {
                        toastr.error('Transaction ID is required for offline verification');
                        return;
                    }
                    
                    let qty = $('#form-sms-unit').val();
                    let amount = $('#form-balance').val();

                    $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');

                    $.ajax({
                        url: "{{ route('customer.buy_sms.offline_purchase') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            units: qty,
                            amount: amount,
                            transaction_id: txnId,
                            payment_method: 'Offline/Modal'
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                toastr.success(response.message);
                                setTimeout(() => {
                                    window.location.href = "{{ route('customer.buy_sms.history') }}";
                                }, 2000);
                            } else {
                                toastr.error(response.message);
                                $('#confirm-payment-btn').prop('disabled', false).html('<span class="align-middle">Confirm Purchase</span> <i data-feather="arrow-right" class="ms-50"></i>');
                                feather.replace();
                            }
                        },
                        error: function() {
                            toastr.error('Something went wrong. Please try again.');
                            $('#confirm-payment-btn').prop('disabled', false).html('<span class="align-middle">Confirm Purchase</span> <i data-feather="arrow-right" class="ms-50"></i>');
                            feather.replace();
                        }
                    });
                    return;
                }

                if (selectedGateway === 'bkash') {
                    let form = $('#payment-gateways-form');
                    form.attr('action', '{{ route('customer.bkash.create-payment') }}');
                    form.attr('method', 'GET');
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'amount',
                        value: $('#form-balance').val()
                    }).appendTo(form);
                } else {
                    let form = $('#payment-gateways-form');
                    form.attr('action', '{{ route('user.account.pay') }}');
                    form.attr('method', 'POST');
                }

                $('#payment-gateways-form').submit();
            });

            // Initial calculation
            getQuote();
        });

        function getQuote() {
            const qty = parseInt($('#sms-quantity').val());
            const $loader = $('#calc-loader');
            const $checkoutBtn = $('#checkout-btn');
            const $submitBtn = $('#submit-btn');

            if (!qty || qty < 1) {
                $('#q-qty').text('—');
                $('#q-rate').text('—');
                $('#q-tier').text('—');
                $('#q-total').text('—');
                $checkoutBtn.prop('disabled', true);
                $submitBtn.prop('disabled', true);
                return;
            }

            $loader.show();

            $.ajax({
                url: '{{ route("customer.buy_sms.get_quote") }}',
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', quantity: qty },
                success: function (data) {
                    $loader.hide();
                    if (data.status === 'success') {
                        currentQuote = data;
                        $('#q-qty').text(qty.toLocaleString() + ' SMS');
                        $('#q-rate').text('৳' + parseFloat(data.rate).toFixed(2));
                        $('#q-tier').text(data.tier_label);
                        $('#q-total').text('৳' + parseFloat(data.total_price).toFixed(2));

                        $checkoutBtn.prop('disabled', false);
                        $submitBtn.prop('disabled', false);

                        // Highlight table row
                        $('.tier-row').removeClass('table-primary');
                        // Simple logic for highlighting table rows could go here
                    } else {
                        toastr['error'](data.message);
                    }
                },
                error: function () {
                    $loader.hide();
                }
            });
        }

        function proceedToCheckout() {
            if (!currentQuote) return;

            // Set values for the modal form
            $('#form-sms-unit').val(currentQuote.quantity);
            $('#form-balance').val(currentQuote.total_price);

            // Open modal
            $('#paymentGatewaysModal').modal('show');
        }

        function submitPurchase() {
            if (!currentQuote) return;

            const txnId = $('#txn-id').val().trim();
            if (!txnId) {
                toastr['warning']('Please enter Transaction ID.');
                return;
            }

            Swal.fire({
                title: 'Confirm Purchase',
                text: 'Submit request for ' + currentQuote.quantity.toLocaleString() + ' SMS?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Submit!',
                customClass: { confirmButton: 'btn btn-primary', cancelButton: 'btn btn-outline-danger ms-1' },
                buttonsStyling: false,
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        url: '{{ route("customer.buy_sms.offline_purchase") }}',
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}', quantity: currentQuote.quantity, transaction_id: txnId },
                        success: function (data) {
                            if (data.status === 'success') {
                                Swal.fire({ title: 'Success!', text: data.message, icon: 'success', confirmButtonClass: 'btn btn-success' });
                                $('#txn-id').val('');
                            }
                        }
                    });
                }
            });
        }
    </script>
@endsection