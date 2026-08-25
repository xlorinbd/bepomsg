@extends('layouts/contentLayoutMaster')

@section('title', 'Manual Balance Adjustment')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="mb-3 mt-2">
                <h4 class="mb-2"><i data-feather="sliders" class="me-1 text-primary"></i> Manual Balance Adjustment</h4>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6 col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Adjust User SMS Credits</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <p class="text-muted mb-2">
                            Search for a user by their name, email, phone number, or ID. Then specify the number of credits to add or deduct. Use a negative sign (-) to deduct credits.
                        </p>

                        <form id="adjust-balance-form">
                            {{-- User Search Field --}}
                            <div class="mb-2 position-relative">
                                <label for="adj-user-search" class="form-label required fw-bold">Search User</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i data-feather="search"></i></span>
                                    <input type="text" id="adj-user-search" class="form-control"
                                        placeholder="Search by email, phone, name or ID..." autocomplete="off">
                                </div>
                                <input type="hidden" id="adj-user-id">
                                <div id="user-search-spinner" class="position-absolute" style="right:15px; top:42px; display:none; z-index: 5;">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                                {{-- Search Results Dropdown --}}
                                <div id="user-search-results" class="border rounded shadow-lg bg-white position-absolute w-100" style="z-index:1050; max-height:350px; overflow-y:auto; display:none; margin-top: 2px;">
                                </div>
                            </div>

                            {{-- Selected User Info Card --}}
                            <div id="selected-user-info" class="mb-2" style="display:none;">
                                <div class="border rounded p-1 bg-light-primary border-primary">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-light-primary p-50 me-1">
                                                <div class="avatar-content">
                                                    <i data-feather="user" class="font-medium-5 text-primary"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bolder" id="sel-user-name"></h6>
                                                <small class="text-muted d-block" id="sel-user-email"></small>
                                                <small class="text-muted d-block" id="sel-user-phone"></small>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="mb-50">
                                                <span class="badge bg-light-primary text-primary" id="sel-user-balance-badge">
                                                    Balance: <span id="sel-user-balance"></span>
                                                </span>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-flat-danger" onclick="clearSelectedUser()">
                                                <i data-feather="x"></i> Clear
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label for="adj-amount" class="form-label required fw-bold">Credits Amount</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i data-feather="plus-circle"></i></span>
                                        <input type="number" id="adj-amount" class="form-control"
                                            placeholder="e.g. 500" required>
                                    </div>
                                    <small class="text-muted">Use negative values (e.g., -100) to deduct</small>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="adj-note" class="form-label fw-bold">Note / Reason</label>
                                    <input type="text" id="adj-note" class="form-control" placeholder="Optional adjustment note">
                                </div>
                            </div>

                            <div class="d-grid mt-1">
                                <button type="submit" class="btn btn-primary btn-lg waves-effect waves-light shadow">
                                    <i data-feather="check-circle" class="me-50"></i> Update SMS Balance
                                </button>
                            </div>
                        </form>

                        <div id="adj-result" class="mt-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        let searchTimer = null;
        let selectedUserId = null;

        $(document).ready(function () {
            "use strict";
            feather.replace();

            // Manual balance form submit
            $('#adjust-balance-form').on('submit', function (e) {
                e.preventDefault();
                adjustBalance();
            });

            // Live search on input
            $('#adj-user-search').on('input', function () {
                const query = $(this).val().trim();

                // Clear previous timer
                if (searchTimer) clearTimeout(searchTimer);

                if (query.length < 1) {
                    $('#user-search-results').hide().empty();
                    return;
                }

                // Debounce: wait 300ms after user stops typing
                searchTimer = setTimeout(function () {
                    searchUsers(query);
                }, 300);
            });

            // Hide dropdown when clicking outside
            $(document).on('click', function (e) {
                if (!$(e.target).closest('#adj-user-search, #user-search-results').length) {
                    $('#user-search-results').hide();
                }
            });

            // Show results again on focus if there are results
            $('#adj-user-search').on('focus', function () {
                if ($('#user-search-results').children().length > 0 && !selectedUserId) {
                    $('#user-search-results').show();
                }
            });
        });

        function searchUsers(query) {
            $('#user-search-spinner').show();

            $.ajax({
                url: '/{{ config("app.admin_path", "admin") }}/sms-credits/search-users',
                type: 'GET',
                data: { q: query },
                success: function (data) {
                    $('#user-search-spinner').hide();
                    renderSearchResults(data);
                },
                error: function () {
                    $('#user-search-spinner').hide();
                    $('#user-search-results').html(
                        '<div class="p-2 text-danger text-center"><i data-feather="alert-circle" class="me-50"></i> Search failed. Please try again.</div>'
                    ).show();
                    feather.replace();
                }
            });
        }

        function renderSearchResults(users) {
            const $results = $('#user-search-results');
            $results.empty();

            if (users.length === 0) {
                $results.html(
                    '<div class="p-2 text-muted text-center">' +
                    '<i data-feather="frown" class="me-50"></i> No users found with matching criteria.' +
                    '</div>'
                ).show();
                feather.replace();
                return;
            }

            users.forEach(function (user) {
                const statusBadge = user.status === 'Active'
                    ? '<span class="badge bg-light-success text-success badge-sm ms-50">Active</span>'
                    : '<span class="badge bg-light-danger text-danger badge-sm ms-50">Inactive</span>';

                const item = $(
                    '<div class="px-1 py-1 border-bottom cursor-pointer user-search-item" ' +
                    'style="transition: background 0.1s ease-in-out;" ' +
                    'data-id="' + user.id + '" ' +
                    'data-name="' + escapeHtml(user.name) + '" ' +
                    'data-email="' + escapeHtml(user.email) + '" ' +
                    'data-phone="' + escapeHtml(user.phone) + '" ' +
                    'data-balance="' + user.sms_balance + '">' +
                    '<div class="d-flex justify-content-between align-items-center">' +
                        '<div>' +
                            '<strong class="d-block text-primary">' + escapeHtml(user.name) + '</strong>' +
                            '<span class="text-muted font-small-3"><i data-feather="mail" class="font-small-2 me-25"></i>' + escapeHtml(user.email) + '</span>' +
                            '<div class="mt-25 font-small-2 text-muted">' +
                                '<i data-feather="phone" class="font-small-2 me-25"></i>' + escapeHtml(user.phone) +
                                ' <span class="mx-50">|</span> <span>Balance: <strong>' + user.sms_balance + '</strong></span>' +
                            '</div>' +
                        '</div>' +
                        '<div class="text-end">' +
                            '<div class="font-small-3 text-secondary mb-25">ID: ' + user.id + '</div>' +
                            statusBadge +
                        '</div>' +
                    '</div>' +
                    '</div>'
                );

                item.on('mouseenter', function () { $(this).css('background', '#f8f9fb'); });
                item.on('mouseleave', function () { $(this).css('background', ''); });
                item.on('click', function () { selectUser($(this)); });

                $results.append(item);
            });

            $results.show();
            feather.replace();
        }

        function selectUser($item) {
            const id = $item.data('id');
            const name = $item.data('name');
            const email = $item.data('email');
            const phone = $item.data('phone');
            const balance = $item.data('balance');

            selectedUserId = id;
            $('#adj-user-id').val(id);
            $('#adj-user-search').val(name + ' (ID: ' + id + ')').prop('readonly', true);
            $('#user-search-results').hide().empty();

            // Show selected user info card
            $('#sel-user-name').text(name + ' (ID: ' + id + ')');
            $('#sel-user-email').text(email);
            $('#sel-user-phone').text(phone);
            $('#sel-user-balance').text(balance);
            $('#selected-user-info').slideDown(300);

            feather.replace();
        }

        function clearSelectedUser() {
            selectedUserId = null;
            $('#adj-user-id').val('');
            $('#adj-user-search').val('').prop('readonly', false).focus();
            $('#selected-user-info').slideUp(300);
            $('#user-search-results').hide().empty();
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.appendChild(document.createTextNode(text));
            return div.innerHTML;
        }

        function adjustBalance() {
            const userId = $('#adj-user-id').val();
            const amount = $('#adj-amount').val();
            const note = $('#adj-note').val();
            const $result = $('#adj-result');

            if (!userId) {
                toastr['warning']('Please search and select a user first.', 'Whoops!', {
                    closeButton: true, positionClass: 'toast-top-right', progressBar: true, rtl: isRtl
                });
                return;
            }

            // Disable submit button during processing
            const $btn = $('#adjust-balance-form button[type="submit"]');
            const originalBtnContent = $btn.html();
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');

            $.ajax({
                url: '/{{ config("app.admin_path", "admin") }}/sms-credits/adjust-balance',
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', user_id: userId, amount: amount, note: note },
                success: function (data) {
                    $btn.prop('disabled', false).html(originalBtnContent);

                    if (data.status === 'success') {
                        $result.html('<div class="alert alert-success"><div class="alert-body d-flex align-items-center"><i data-feather="check-circle" class="me-50"></i>' + data.message + '</div></div>');
                        toastr['success'](data.message, 'Success!', {
                            closeButton: true, positionClass: 'toast-top-right', progressBar: true, rtl: isRtl
                        });
                        $('#adjust-balance-form')[0].reset();
                        clearSelectedUser();
                        feather.replace();
                    } else {
                        $result.html('<div class="alert alert-danger"><div class="alert-body d-flex align-items-center"><i data-feather="alert-circle" class="me-50"></i>' + data.message + '</div></div>');
                        toastr['error'](data.message, 'Opps!', {
                            closeButton: true, positionClass: 'toast-top-right', progressBar: true, rtl: isRtl
                        });
                        feather.replace();
                    }
                },
                error: function (reject) {
                    $btn.prop('disabled', false).html(originalBtnContent);

                    if (reject.status === 422) {
                        $.each(reject.responseJSON.errors, function (key, value) {
                            toastr['warning'](value[0], 'Attention', {
                                closeButton: true, positionClass: 'toast-top-right', progressBar: true, rtl: isRtl
                            });
                        });
                    } else {
                        const errMsg = (reject.responseJSON && reject.responseJSON.message) ? reject.responseJSON.message : 'An error occurred. Please try again.';
                        $result.html('<div class="alert alert-danger"><div class="alert-body d-flex align-items-center"><i data-feather="alert-circle" class="me-50"></i>' + errMsg + '</div></div>');
                        feather.replace();
                    }
                }
            });
        }
    </script>
@endsection
