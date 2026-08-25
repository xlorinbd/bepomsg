@extends('layouts/contentLayoutMaster')

@section('title', 'Verification Details - ' . $user->displayName())

@section('page-style')
    <style>
        .doc-preview {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
            border: 1px solid #ddd;
            padding: 5px;
        }
        .doc-iframe {
            width: 100%;
            height: 500px;
            border: none;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <!-- User Info -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-header border-bottom">
                    <h4 class="card-title">User Information</h4>
                </div>
                <div class="card-body pt-2">
                    <div class="mb-1">
                        <label class="form-label fw-bolder">Full Name:</label>
                        <p>{{ $user->displayName() }}</p>
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-bolder">Email:</label>
                        <p>{{ $user->email }}</p>
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-bolder">Phone:</label>
                        <p>{{ $user->phone }}</p>
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-bolder">Registration Date:</label>
                        <p>{{ $user->created_at->format('M d, Y') }}</p>
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-bolder">Current Status:</label>
                        <p>
                            @if($user->verification_status == 'pending')
                                <span class="badge rounded-pill badge-light-warning">Pending</span>
                            @elseif($user->verification_status == 'approved')
                                <span class="badge rounded-pill badge-light-success">Approved</span>
                            @else
                                <span class="badge rounded-pill badge-light-danger">Rejected</span>
                            @endif
                        </p>
                    </div>

                    @if($user->verification_status == 'pending')
                        <hr>
                        <div class="d-grid gap-2">
                            <form action="{{ route('admin.verifications.approve', $user->uid) }}" method="POST">
                                @csrf
                                <div class="mb-1">
                                    <label class="form-label fw-bolder" for="sending_server">Assign Sending Server</label>
                                    <select name="sending_server" id="sending_server" class="form-select select2">
                                        <option value="">-- Select Server --</option>
                                        @foreach($sending_servers as $server)
                                            <option value="{{ $server->id }}">{{ $server->name }} ({{ $server->type }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label fw-bolder" for="sender_id">Assign Sender ID / Number</label>
                                    <select name="sender_id" id="sender_id" class="form-select select2">
                                        <option value="">-- Select Sender ID --</option>
                                        @foreach($sender_ids as $sid)
                                            @php
                                                $serverNames = $sid->sendingServers->pluck('name')->implode(', ');
                                            @endphp
                                            <option value="{{ $sid->id }}">{{ $sid->sender_id }} ({{ ucfirst($sid->allocation_type) }}) {{ $serverNames ? '('.$serverNames.')' : '' }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-success w-100 mb-1" onclick="return confirm('Are you sure you want to approve this verification?')">
                                    <i data-feather="check"></i> Approve Verification
                                </button>
                            </form>
                            <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i data-feather="x"></i> Reject Verification
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Documents -->
        <div class="col-md-7">
            <div class="card">
                <div class="card-header border-bottom">
                    <h4 class="card-title">Submitted Documents</h4>
                </div>
                <div class="card-body pt-2">
                    @if($user->latestVerification)
                        <div class="mb-3">
                            <h5 class="fw-bolder">National ID (NID)</h5>
                            @php $nid = $user->latestVerification->nid_document; @endphp
                            @if($nid)
                                @if(Str::endsWith($nid, ['.pdf']))
                                    <iframe class="doc-iframe" src="{{ route('admin.verifications.view_document', ['path' => $nid]) }}"></iframe>
                                @else
                                    <img src="{{ route('admin.verifications.view_document', ['path' => $nid]) }}" class="doc-preview" alt="NID">
                                @endif
                                <div class="mt-1">
                                    <a href="{{ route('admin.verifications.view_document', ['path' => $nid]) }}" target="_blank" class="btn btn-sm btn-outline-primary">View Full Screen</a>
                                </div>
                            @else
                                <p class="text-danger">NID document missing.</p>
                            @endif
                        </div>

                        <hr>

                        <div class="mb-3">
                            <h5 class="fw-bolder">Trade License</h5>
                            @php $trade = $user->latestVerification->trade_license_document; @endphp
                            @if($trade)
                                @if(Str::endsWith($trade, ['.pdf']))
                                    <iframe class="doc-iframe" src="{{ route('admin.verifications.view_document', ['path' => $trade]) }}"></iframe>
                                @else
                                    <img src="{{ route('admin.verifications.view_document', ['path' => $trade]) }}" class="doc-preview" alt="Trade License">
                                @endif
                                <div class="mt-1">
                                    <a href="{{ route('admin.verifications.view_document', ['path' => $trade]) }}" target="_blank" class="btn btn-sm btn-outline-primary">View Full Screen</a>
                                </div>
                            @else
                                <p class="text-muted">No trade license submitted.</p>
                            @endif
                        </div>
                    @else
                        <p class="text-center p-3">No verification records found for this user.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white">Reject Verification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.verifications.reject', $user->uid) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-1">
                            <label class="form-label" for="reason">Reason for Rejection</label>
                            <textarea name="reason" id="reason" class="form-control" rows="4" required placeholder="Enter the reason why you are rejecting this document..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
