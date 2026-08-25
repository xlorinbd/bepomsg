@extends('layouts/contentLayoutMaster')

@section('title', 'SMS Credit Purchase Requests')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
@endsection

@section('content')

    {{-- Stats Row --}}
    <div class="row match-height">
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="fw-bolder mb-0">{{ $purchases->total() }}</h2>
                        <p class="card-text">Total Requests</p>
                    </div>
                    <div>
                        <i class="font-large-3 text-primary" data-feather="shopping-bag"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="fw-bolder mb-0">{{ $purchases->where('status', 'pending')->count() }}</h2>
                        <p class="card-text">Pending</p>
                    </div>
                    <div>
                        <i class="font-large-3 text-warning" data-feather="clock"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="fw-bolder mb-0">{{ $purchases->where('status', 'completed')->count() }}</h2>
                        <p class="card-text">Completed</p>
                    </div>
                    <div>
                        <i class="font-large-3 text-success" data-feather="check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="fw-bolder mb-0">{{ $purchases->where('status', 'rejected')->count() }}</h2>
                        <p class="card-text">Rejected</p>
                    </div>
                    <div>
                        <i class="font-large-3 text-danger" data-feather="x-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter + Table --}}
    <section id="datatables-basic">
        <div class="mb-3 mt-2">
            <div class="btn-group">
                <a href="{{ route('admin.sms_credits.tiers.index') }}"
                    class="btn btn-primary waves-light waves-effect fw-bold">
                    <i data-feather="layers"></i> Manage Pricing Tiers
                </a>
            </div>
        </div>

        {{-- Filter bar --}}
        <div class="card mb-1">
            <div class="card-body py-1">
                <form method="GET" class="row g-1 align-items-end">
                    <div class="col-md-3 col-12">
                        <label class="form-label mb-25 small fw-bold">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed
                            </option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-5 col-12">
                        <label class="form-label mb-25 small fw-bold">Search</label>
                        <input type="text" name="search" class="form-control form-control-sm"
                            placeholder="Customer name, email or Transaction ID" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2 col-12">
                        <button type="submit" class="btn btn-primary btn-sm w-100 waves-effect">
                            <i data-feather="search"></i> Filter
                        </button>
                    </div>
                    <div class="col-md-2 col-12">
                        <a href="{{ route('admin.sms_credits.purchases.index') }}"
                            class="btn btn-outline-secondary btn-sm w-100 waves-effect">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Purchase Requests</h4>
                        <span class="badge bg-primary">{{ $purchases->total() }} total</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>SMS Qty</th>
                                    <th>Rate</th>
                                    <th>Total (BDT)</th>
                                    <th>Txn ID</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th>{{ __('locale.labels.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchases as $purchase)
                                    <tr id="purchase-row-{{ $purchase->uid }}">
                                        <td class="text-muted small">{{ $purchase->id }}</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold">{{ optional($purchase->user)->name }}</span>
                                                <small class="text-muted">{{ optional($purchase->user)->email }}</small>
                                            </div>
                                        </td>
                                        <td class="small">
                                            {{ $purchase->created_at->format('d M Y') }}<br>
                                            <small class="text-muted">{{ $purchase->created_at->format('h:i A') }}</small>
                                        </td>
                                        <td><strong>{{ number_format($purchase->sms_quantity) }}</strong></td>
                                        <td class="small">৳{{ $purchase->rate }}</td>
                                        <td><strong>৳{{ number_format($purchase->total_price, 2) }}</strong></td>
                                        <td><code
                                                class="small">{{ Str::limit($purchase->transaction_id ?? '—', 18, '…') }}</code>
                                        </td>
                                        <td>
                                            <span class="badge bg-light-secondary text-secondary text-uppercase">
                                                {{ $purchase->payment_method ?? 'offline' }}
                                            </span>
                                        </td>
                                        <td id="status-cell-{{ $purchase->uid }}">
                                            @if($purchase->status === 'completed')
                                                <span class="badge bg-light-success text-success">Completed</span>
                                            @elseif($purchase->status === 'pending')
                                                <span class="badge bg-light-warning text-warning">Pending</span>
                                            @elseif($purchase->status === 'approved')
                                                <span class="badge bg-light-info text-info">Approved</span>
                                            @else
                                                <span
                                                    class="badge bg-light-danger text-danger">{{ ucfirst($purchase->status) }}</span>
                                            @endif
                                        </td>
                                        <td id="action-cell-{{ $purchase->uid }}">
                                            @if($purchase->status === 'pending')
                                                <span class="action-approve text-success cursor-pointer me-1"
                                                    data-bs-toggle="tooltip" title="Approve"
                                                    onclick="approvePurchase('{{ $purchase->uid }}', {{ $purchase->sms_quantity }})">
                                                    <i data-feather="check-circle" class="font-medium-4"></i>
                                                </span>
                                                <span class="action-reject text-danger cursor-pointer" data-bs-toggle="tooltip"
                                                    title="Reject" onclick="rejectPurchase('{{ $purchase->uid }}')">
                                                    <i data-feather="x-circle" class="font-medium-4"></i>
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-3 text-muted">
                                            <i data-feather="inbox"></i> No purchase requests found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($purchases->hasPages())
                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Showing {{ $purchases->firstItem() }} to {{ $purchases->lastItem() }} of
                                {{ $purchases->total() }} entries
                            </small>
                            {{ $purchases->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Approve Modal --}}
    <div class="modal fade text-left" id="approveModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Approve Purchase Request</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="approve-purchase-id">
                    <div class="form-group mb-1">
                        <label for="approve-credits" class="form-label required">Credits to Add</label>
                        <input type="number" id="approve-credits" class="form-control">
                        <small class="text-muted">Default is the purchased quantity. You can add bonus credits here.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirm-approve-btn" onclick="confirmApprove()">
                        <i data-feather="check-circle"></i> Confirm Approve
                    </button>
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
        $(document).ready(function () {
            "use strict";
            feather.replace();
        });

        function showResponseMessage(data) {
            if (data.status === 'success') {
                toastr['success'](data.message, '{{ __("locale.labels.success") }}!!', {
                    closeButton: true, positionClass: 'toast-top-right', progressBar: true, rtl: isRtl
                });
            } else {
                toastr['error'](data.message, '{{ __("locale.labels.opps") }}!', {
                    closeButton: true, positionClass: 'toast-top-right', progressBar: true, rtl: isRtl
                });
            }
        }

        function approvePurchase(id, defaultQty) {
            $('#approve-purchase-id').val(id);
            $('#approve-credits').val(defaultQty);
            new bootstrap.Modal(document.getElementById('approveModal')).show();
        }

        function confirmApprove() {
            const id = $('#approve-purchase-id').val();
            const credits = $('#approve-credits').val();
            const $btn = $('#confirm-approve-btn');

            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Processing...');

            $.ajax({
                url: '/{{ config("app.admin_path", "admin") }}/sms-credits/purchases/' + id + '/approve',
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', credits_added: credits },
                success: function (data) {
                    showResponseMessage(data);
                    if (data.status === 'success') {
                        $('#status-cell-' + id).html('<span class="badge bg-light-success text-success">Completed</span>');
                        $('#action-cell-' + id).html('<span class="text-muted">—</span>');
                        bootstrap.Modal.getInstance(document.getElementById('approveModal')).hide();
                    }
                    $btn.prop('disabled', false).html('<i data-feather="check-circle"></i> Confirm Approve');
                    feather.replace();
                },
                error: function (reject) {
                    if (reject.responseJSON && reject.responseJSON.message) {
                        toastr['error'](reject.responseJSON.message, '{{ __("locale.labels.opps") }}!', {
                            closeButton: true, positionClass: 'toast-top-right', progressBar: true, rtl: isRtl
                        });
                    }
                    $btn.prop('disabled', false).html('<i data-feather="check-circle"></i> Confirm Approve');
                    feather.replace();
                }
            });
        }

        function rejectPurchase(id) {
            Swal.fire({
                title: '{{ __("locale.labels.are_you_sure") }}',
                text: 'This purchase request will be rejected.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Reject!',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ms-1'
                },
                buttonsStyling: false,
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        url: '/{{ config("app.admin_path", "admin") }}/sms-credits/purchases/' + id + '/reject',
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function (data) {
                            showResponseMessage(data);
                            if (data.status === 'success') {
                                $('#status-cell-' + id).html('<span class="badge bg-light-danger text-danger">Rejected</span>');
                                $('#action-cell-' + id).html('<span class="text-muted">—</span>');
                            }
                        }
                    });
                }
            });
        }
    </script>
@endsection