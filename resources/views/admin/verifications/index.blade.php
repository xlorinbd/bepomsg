@extends('layouts/contentLayoutMaster')

@section('title', 'Verification Requests')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h4 class="card-title text-uppercase">Verification Requests ({{ ucfirst($status) }})</h4>
                    <div class="btn-group">
                        <a href="{{ route('admin.verifications.index', ['status' => 'pending']) }}" class="btn btn-outline-warning btn-sm {{ $status == 'pending' ? 'active' : '' }}">Pending</a>
                        <a href="{{ route('admin.verifications.index', ['status' => 'approved']) }}" class="btn btn-outline-success btn-sm {{ $status == 'approved' ? 'active' : '' }}">Approved</a>
                        <a href="{{ route('admin.verifications.index', ['status' => 'rejected']) }}" class="btn btn-outline-danger btn-sm {{ $status == 'rejected' ? 'active' : '' }}">Rejected</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>User</th>
                                <th>Contact</th>
                                <th>Date Submitted</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex justify-content-left align-items-center">
                                            <div class="avatar bg-light-primary me-1">
                                                <span class="avatar-content">{{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}</span>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bolder">{{ $user->displayName() }}</span>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->phone }}</td>
                                    <td>{{ $user->latestVerification ? $user->latestVerification->created_at->format('M d, Y h:i A') : 'N/A' }}</td>
                                    <td>
                                        @if($user->verification_status == 'pending')
                                            <span class="badge rounded-pill badge-light-warning">Pending</span>
                                        @elseif($user->verification_status == 'approved')
                                            <span class="badge rounded-pill badge-light-success">Approved</span>
                                        @else
                                            <span class="badge rounded-pill badge-light-danger">Rejected</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center">
                                            <a href="{{ route('admin.verifications.show', $user->uid) }}" class="btn btn-sm btn-primary shadow-sm me-50">
                                                <i data-feather="eye" class="me-25"></i>
                                                <span>Details</span>
                                            </a>
                                            <div class="dropdown">
                                                <button type="button" class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i data-feather="more-vertical" class="font-medium-3"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end" style="min-width: 155px;">
                                                    <a class="dropdown-item" href="{{ route('admin.verifications.show', $user->uid) }}">
                                                        <i data-feather="eye" class="me-50"></i>
                                                        <span>View Details</span>
                                                    </a>
                                                    @if($user->verification_status == 'pending')
                                                        <a class="dropdown-item text-success" href="#" onclick="event.preventDefault(); if(confirm('Are you sure you want to approve this user?')) document.getElementById('approve-form-{{ $user->id }}').submit();">
                                                            <i data-feather="check-circle" class="me-50"></i>
                                                            <span>Approve</span>
                                                        </a>
                                                        <form id="approve-form-{{ $user->id }}" action="{{ route('admin.verifications.approve', $user->uid) }}" method="POST" class="d-none">@csrf</form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center p-3 text-muted">No verification requests found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer px-2">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
