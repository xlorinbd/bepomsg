@extends('layouts/contentLayoutMaster')

@section('title', 'Server Balance')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
@endsection

@section('page-style')
    <style>
        .balance-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        .balance-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1);
        }
        .balance-good { border-left-color: #28c76f !important; }
        .balance-low { border-left-color: #ff9f43 !important; }
        .balance-critical { border-left-color: #ea5455 !important; }
        .balance-na { border-left-color: #82868b !important; }

        .balance-value {
            font-size: 1.5rem;
            font-weight: 700;
        }
        .balance-value.good { color: #28c76f; }
        .balance-value.low { color: #ff9f43; }
        .balance-value.critical { color: #ea5455; }
        .balance-value.na { color: #82868b; }

        .spinner-check {
            display: none;
        }
        .checking .spinner-check {
            display: inline-block;
        }
        .checking .btn-text {
            display: none;
        }

        #balance-results-table td {
            vertical-align: middle;
        }

        .refresh-single {
            cursor: pointer;
            transition: transform 0.3s;
        }
        .refresh-single:hover {
            transform: rotate(180deg);
        }
        .refresh-single.spinning {
            animation: spin 1s linear infinite;
        }
        @@keyframes spin {
            100% { transform: rotate(360deg); }
        }

        .summary-card .card-body {
            padding: 1.25rem;
        }
        .summary-number {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
        }
    </style>
@endsection

@section('content')
    <section>
        {{-- Summary Cards --}}
        <div class="row match-height" id="summary-cards" style="display: none;">
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="card summary-card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="card-text mb-25">Total Checked</p>
                            <span class="summary-number text-primary" id="summary-total">0</span>
                        </div>
                        <div class="avatar bg-light-primary p-50">
                            <div class="avatar-content"><i data-feather="server" class="font-medium-5"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="card summary-card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="card-text mb-25">Success</p>
                            <span class="summary-number text-success" id="summary-success">0</span>
                        </div>
                        <div class="avatar bg-light-success p-50">
                            <div class="avatar-content"><i data-feather="check-circle" class="font-medium-5"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="card summary-card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="card-text mb-25">Skipped</p>
                            <span class="summary-number text-warning" id="summary-skipped">0</span>
                        </div>
                        <div class="avatar bg-light-warning p-50">
                            <div class="avatar-content"><i data-feather="skip-forward" class="font-medium-5"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="card summary-card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="card-text mb-25">Errors</p>
                            <span class="summary-number text-danger" id="summary-errors">0</span>
                        </div>
                        <div class="avatar bg-light-danger p-50">
                            <div class="avatar-content"><i data-feather="alert-triangle" class="font-medium-5"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="mb-3 mt-2">
            <button type="button" class="btn btn-primary fw-bold" id="btn-check-all">
                <span class="spinner-check">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Checking...
                </span>
                <span class="btn-text">
                    <i data-feather="refresh-cw" class="me-25"></i> Check All Server Balance
                </span>
            </button>

            <a href="{{ route('admin.sending-servers.index') }}" class="btn btn-outline-secondary fw-bold ms-1">
                <i data-feather="arrow-left" class="me-25"></i> Back to Servers
            </a>
        </div>

        {{-- Balance Results Table --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i data-feather="database" class="me-25"></i>
                            Active Server Balances
                        </h4>
                        <small class="text-muted" id="last-checked">Click "Check All Server Balance" to start</small>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="balance-results-table">
                                <thead>
                                <tr>
                                    <th>Server Name</th>
                                    <th>Type</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                    <th>Last Checked</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody id="balance-tbody">
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">
                                        <i data-feather="info" class="me-25"></i>
                                        Click "Check All Server Balance" button to fetch balance from all active servers
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('vendor-script')
    <script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.bootstrap5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/responsive.bootstrap5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
@endsection

@section('page-script')
    <script>
        $(document).ready(function () {
            "use strict";

            let balanceData = [];

            function getBalanceClass(balance, status) {
                if (status !== 'success' || balance === null) return 'na';
                if (balance >= 1000) return 'good';
                if (balance >= 100) return 'low';
                return 'critical';
            }

            function formatBalance(balance, currency, status) {
                if (status === 'skipped') return '<span class="text-muted"><i>Skipped</i></span>';
                if (status === 'unsupported') return '<span class="text-muted"><i>N/A</i></span>';
                if (status === 'error') return '<span class="text-danger"><i>Error</i></span>';
                if (balance === null) return '<span class="text-muted">—</span>';

                let cls = getBalanceClass(balance, status);
                let formatted = parseFloat(balance).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 4});
                return '<span class="balance-value ' + cls + '">' + formatted + '</span> <small class="text-muted">' + currency + '</small>';
            }

            function getStatusBadge(status) {
                switch (status) {
                    case 'success':
                        return '<span class="badge bg-success">Success</span>';
                    case 'skipped':
                        return '<span class="badge bg-warning">Skipped</span>';
                    case 'unsupported':
                        return '<span class="badge bg-secondary">Unsupported</span>';
                    case 'error':
                        return '<span class="badge bg-danger">Error</span>';
                    default:
                        return '<span class="badge bg-light-secondary">' + status + '</span>';
                }
            }

            function renderTable(data) {
                let tbody = $('#balance-tbody');
                tbody.empty();

                if (data.length === 0) {
                    tbody.html('<tr><td colspan="6" class="text-center text-muted py-3">No active servers found</td></tr>');
                    return;
                }

                data.forEach(function (item) {
                    let balClass = getBalanceClass(item.balance, item.status);
                    let row = '<tr class="balance-card balance-' + balClass + '">' +
                        '<td><strong>' + item.name + '</strong></td>' +
                        '<td><span class="badge bg-primary text-uppercase">' + item.type + '</span></td>' +
                        '<td>' + formatBalance(item.balance, item.currency, item.status) + '</td>' +
                        '<td>' + getStatusBadge(item.status) +
                        (item.raw_response && item.status !== 'success' ?
                            ' <i class="text-muted cursor-pointer" data-bs-toggle="tooltip" title="' +
                            $('<div/>').text(item.raw_response.substring(0, 200)).html() + '" data-feather="info"></i>' : '') +
                        '</td>' +
                        '<td><small class="text-muted">' + (item.checked_at || '—') + '</small></td>' +
                        '<td>' +
                        '<span class="refresh-single cursor-pointer text-primary" data-uid="' + item.uid + '" title="Refresh">' +
                        '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>' +
                        '</span>' +
                        '</td>' +
                        '</tr>';
                    tbody.append(row);
                });

                // Re-init feather icons
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }

                // Init tooltips
                $('[data-bs-toggle="tooltip"]').tooltip();
            }

            function updateSummary(response) {
                $('#summary-total').text(response.total || 0);
                $('#summary-success').text(response.checked || 0);
                $('#summary-skipped').text(response.skipped || 0);
                $('#summary-errors').text(response.errors || 0);
                $('#summary-cards').fadeIn();
            }

            // Check All Balance
            $('#btn-check-all').on('click', function () {
                let btn = $(this);
                btn.addClass('checking').prop('disabled', true);

                $.ajax({
                    url: "{{ route('admin.server-balance.check-all') }}",
                    type: "POST",
                    data: {_token: "{{ csrf_token() }}"},
                    timeout: 120000, // 2 minutes timeout for all servers
                    success: function (response) {
                        try {
                            if (response.status === 'success') {
                                balanceData = response.data;
                                renderTable(balanceData);
                                updateSummary(response);
                                $('#last-checked').text('Last checked: ' + new Date().toLocaleString());

                                toastr['success'](
                                    'Checked ' + response.total + ' servers. ' +
                                    response.checked + ' success, ' + response.skipped + ' skipped, ' + response.errors + ' errors.',
                                    'Balance Check Complete',
                                    {closeButton: true, positionClass: 'toast-top-right', progressBar: true, newestOnTop: true}
                                );
                            } else {
                                toastr['error'](response.message || 'Unknown error occurred', 'Error');
                            }
                        } catch (e) {
                            console.error(e);
                        }
                    },
                    error: function (xhr) {
                        toastr['error'](
                            'Failed to check server balances. Please try again.',
                            'Error',
                            {closeButton: true, positionClass: 'toast-top-right', progressBar: true, newestOnTop: true}
                        );
                    },
                    complete: function () {
                        btn.removeClass('checking').prop('disabled', false);
                    }
                });
            });

            // Refresh Single Server
            $(document).on('click', '.refresh-single', function () {
                let icon = $(this);
                let uid = icon.data('uid');
                let row = icon.closest('tr');

                icon.addClass('spinning');

                $.ajax({
                    url: "{{ url(config('app.admin_path') . '/server-balance/check') }}/" + uid,
                    type: "POST",
                    data: {_token: "{{ csrf_token() }}"},
                    timeout: 30000,
                    success: function (response) {
                        if (response.status === 'success') {
                            let item = response.data;
                            // Update the local data
                            let idx = balanceData.findIndex(d => d.uid === uid);
                            if (idx !== -1) {
                                balanceData[idx].balance = item.balance;
                                balanceData[idx].currency = item.currency;
                                balanceData[idx].status = item.check_status;
                                balanceData[idx].raw_response = item.raw_response;
                                balanceData[idx].checked_at = item.checked_at;
                            }
                            renderTable(balanceData);

                            toastr['success'](
                                item.name + ': ' + (item.balance !== null ? item.balance + ' ' + item.currency : item.check_status),
                                'Balance Updated',
                                {closeButton: true, positionClass: 'toast-top-right', progressBar: true, newestOnTop: true, timeOut: 3000}
                            );
                        }
                    },
                    error: function () {
                        toastr['error']('Failed to check balance for this server.', 'Error',
                            {closeButton: true, positionClass: 'toast-top-right', progressBar: true});
                    },
                    complete: function () {
                        icon.removeClass('spinning');
                    }
                });
            });

            // Trigger Check All on page load
            $('#btn-check-all').trigger('click');
        });
    </script>
@endsection
