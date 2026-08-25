@extends('layouts/contentLayoutMaster')

@section('title', 'SMS Pricing Tiers')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
@endsection

@section('content')

    <div class="row">
        {{-- Pricing Tiers Table --}}
        <div class="col-md-8 col-12">
            <div class="mb-3 mt-2">
            <h4 class="mb-2"><i data-feather="message-circle" class="me-1 text-primary"></i> SMS Pricing Tiers</h4>
            <div class="btn-group">
                    <button type="button" class="btn btn-success waves-light waves-effect fw-bold" data-bs-toggle="modal"
                        data-bs-target="#addTierModal">
                        <i data-feather="plus-circle"></i> Add Tier
                    </button>
                </div>
                <div class="btn-group ms-1">
                    <a href="{{ route('admin.sms_credits.purchases.index') }}"
                        class="btn btn-info waves-light waves-effect fw-bold">
                        <i data-feather="shopping-bag"></i> Purchase Requests
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">SMS Pricing Tiers</h4>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Min Quantity</th>
                                <th>Max Quantity</th>
                                <th>Rate (BDT/SMS)</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tiers as $index => $tier)
                                <tr id="tier-row-{{ $tier->id }}">
                                    <td class="text-muted">{{ $tiers->firstItem() + $index }}</td>
                                    <td><strong>{{ number_format($tier->min_qty) }}</strong></td>
                                    <td>{{ $tier->max_qty ? number_format($tier->max_qty) : '∞ (Unlimited)' }}</td>
                                    <td><strong class="text-primary">৳{{ number_format($tier->rate, 2) }}</strong></td>
                                    <td>
                                        @if($tier->status)
                                            <span class="badge bg-light-success text-success">Active</span>
                                        @else
                                            <span class="badge bg-light-danger text-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="action-edit text-primary cursor-pointer me-1" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="Edit"
                                            onclick="editTier({{ $tier->id }}, {{ $tier->min_qty }}, '{{ $tier->max_qty ?? '' }}', {{ $tier->rate }}, {{ $tier->status ? 1 : 0 }})">
                                            <i data-feather="edit" class="font-medium-4"></i>
                                        </span>
                                        <span class="action-toggle text-warning cursor-pointer me-1" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="{{ $tier->status ? 'Deactivate' : 'Activate' }}"
                                            onclick="toggleTier({{ $tier->id }})">
                                            <i data-feather="{{ $tier->status ? 'toggle-right' : 'toggle-left' }}"
                                                class="font-medium-4"></i>
                                        </span>
                                        <span class="action-delete text-danger cursor-pointer" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="Delete" onclick="deleteTier({{ $tier->id }})">
                                            <i data-feather="trash" class="font-medium-4"></i>
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-3 text-muted">
                                        No pricing tiers found.
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#addTierModal">Add one now</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($tiers->hasPages())
                    <div class="card-footer">
                        {{ $tiers->links() }}
                    </div>
                @endif
            </div>
        </div>

        </div>
    </div>

    {{-- Add Tier Modal --}}
    <div class="modal fade text-left" id="addTierModal" tabindex="-1" role="dialog" aria-labelledby="addTierLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="addTierLabel">Add Pricing Tier</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="mb-1">
                                <label for="new-min" class="form-label required">Min Quantity</label>
                                <input type="number" id="new-min" class="form-control" placeholder="e.g. 1">
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="mb-1">
                                <label for="new-max" class="form-label">Max Quantity <span class="text-muted">(blank =
                                        ∞)</span></label>
                                <input type="number" id="new-max" class="form-control" placeholder="e.g. 500">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-1">
                                <label for="new-rate" class="form-label required">Rate (BDT per SMS)</label>
                                <input type="number" step="0.0001" id="new-rate" class="form-control"
                                    placeholder="e.g. 0.40">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch mb-1">
                                <input type="checkbox" class="form-check-input" id="new-status" checked>
                                <label class="form-check-label" for="new-status">Active</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-primary" onclick="storeTier()">
                        <i data-feather="plus-circle"></i> Create Tier
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Tier Modal --}}
    <div class="modal fade text-left" id="editTierModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Pricing Tier</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit-tier-id">
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="mb-1">
                                <label class="form-label required">Min Quantity</label>
                                <input type="number" id="edit-min" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="mb-1">
                                <label class="form-label">Max Quantity <span class="text-muted">(blank = ∞)</span></label>
                                <input type="number" id="edit-max" class="form-control">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-1">
                                <label class="form-label required">Rate (BDT per SMS)</label>
                                <input type="number" step="0.0001" id="edit-rate" class="form-control">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="edit-status">
                                <label class="form-check-label" for="edit-status">Active</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="updateTier()">
                        <i data-feather="save"></i> Save Changes
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
            } else if (data.status === 'error') {
                toastr['error'](data.message, '{{ __("locale.labels.opps") }}!', {
                    closeButton: true, positionClass: 'toast-top-right', progressBar: true, rtl: isRtl
                });
            } else {
                toastr['warning']('{{ __("locale.exceptions.something_went_wrong") }}', '{{ __("locale.labels.warning") }}!', {
                    closeButton: true, positionClass: 'toast-top-right', progressBar: true, rtl: isRtl
                });
            }
        }

        function storeTier() {
            const payload = {
                _token: '{{ csrf_token() }}',
                min_qty: $('#new-min').val(),
                max_qty: $('#new-max').val() || null,
                rate: $('#new-rate').val(),
                status: $('#new-status').is(':checked') ? 1 : 0,
            };

            $.ajax({
                url: '/{{ config("app.admin_path", "admin") }}/sms-credits/tiers',
                type: 'POST',
                data: payload,
                success: function (data) {
                    showResponseMessage(data);
                    if (data.status === 'success') {
                        setTimeout(() => location.reload(), 800);
                    }
                },
                error: function (reject) {
                    if (reject.status === 422) {
                        $.each(reject.responseJSON.errors, function (key, value) {
                            toastr['warning'](value[0], '{{ __("locale.labels.attention") }}', {
                                closeButton: true, positionClass: 'toast-top-right', progressBar: true, rtl: isRtl
                            });
                        });
                    }
                }
            });
        }

        function editTier(id, min, max, rate, status) {
            $('#edit-tier-id').val(id);
            $('#edit-min').val(min);
            $('#edit-max').val(max);
            $('#edit-rate').val(rate);
            $('#edit-status').prop('checked', status == 1);
            new bootstrap.Modal(document.getElementById('editTierModal')).show();
        }

        function updateTier() {
            const id = $('#edit-tier-id').val();
            $.ajax({
                url: '/{{ config("app.admin_path", "admin") }}/sms-credits/tiers/' + id,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'PUT',
                    min_qty: $('#edit-min').val(),
                    max_qty: $('#edit-max').val() || null,
                    rate: $('#edit-rate').val(),
                    status: $('#edit-status').is(':checked') ? 1 : 0,
                },
                success: function (data) {
                    showResponseMessage(data);
                    if (data.status === 'success') {
                        bootstrap.Modal.getInstance(document.getElementById('editTierModal')).hide();
                        setTimeout(() => location.reload(), 600);
                    }
                },
                error: function (reject) {
                    if (reject.status === 422) {
                        $.each(reject.responseJSON.errors, function (key, value) {
                            toastr['warning'](value[0], '{{ __("locale.labels.attention") }}', {
                                closeButton: true, positionClass: 'toast-top-right', progressBar: true, rtl: isRtl
                            });
                        });
                    }
                }
            });
        }

        function toggleTier(id) {
            $.ajax({
                url: '/{{ config("app.admin_path", "admin") }}/sms-credits/tiers/' + id + '/toggle',
                type: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function (data) {
                    showResponseMessage(data);
                    if (data.status === 'success') {
                        setTimeout(() => location.reload(), 600);
                    }
                }
            });
        }

        function deleteTier(id) {
            Swal.fire({
                title: '{{ __("locale.labels.are_you_sure") }}',
                text: 'This pricing tier will be permanently deleted.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __("locale.labels.delete_it") }}',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ms-1'
                },
                buttonsStyling: false,
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        url: '/{{ config("app.admin_path", "admin") }}/sms-credits/tiers/' + id,
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
                        success: function (data) {
                            showResponseMessage(data);
                            if (data.status === 'success') {
                                setTimeout(() => location.reload(), 600);
                            }
                        }
                    });
                }
            });
        }
    </script>
@endsection