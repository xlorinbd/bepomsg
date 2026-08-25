<div class="card">
    <div class="card-header border-bottom">
        <h4 class="card-title">Manage Sender IDs</h4>
        <div class="card-header-actions d-flex align-items-center">
            <form action="{{ route('admin.customers.sender-id.assign', $customer->uid) }}" method="POST" class="d-flex me-1">
                @csrf
                <select name="sender_id" class="form-select form-select-sm me-1 select2" style="min-width: 200px;" required>
                    <option value="">-- Quick Assign --</option>
                    @foreach($all_available_sender_ids as $sid)
                        @php
                            $serverNames = $sid->sendingServers->pluck('name')->implode(', ');
                        @endphp
                        <option value="{{ $sid->id }}">{{ $sid->sender_id }} ({{ ucfirst($sid->allocation_type) }}) {{ $serverNames ? '('.$serverNames.')' : '' }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-sm btn-success">Assign</button>
            </form>
            <a href="{{ route('admin.senderid.create', ['user_id' => $customer->id]) }}" class="btn btn-sm btn-primary me-1">
                <i data-feather="plus"></i> Add New
            </a>
            <a href="{{ route('admin.senderid.index') }}" class="btn btn-sm btn-outline-secondary">
                <i data-feather="list"></i> All IDs
            </a>
        </div>
    </div>
    <div class="card-body pt-1">
        @if($sender_ids->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Sender ID</th>
                            <th>Status</th>
                            <th>Allocation Type</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sender_ids as $sender_id)
                            <tr>
                                <td>{{ $sender_id->sender_id }}</td>
                                <td>
                                    @if($sender_id->status == 'active')
                                        <span class="badge bg-light-success">Active</span>
                                    @else
                                        <span class="badge bg-light-warning">{{ ucfirst($sender_id->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('admin.customers.sender-id.update-allocation', [$customer->uid, $sender_id->uid]) }}" method="POST">
                                        @csrf
                                        <div class="d-flex align-items-center">
                                            <select name="allocation_type" class="form-select form-select-sm me-1" style="width: auto;">
                                                <option value="shared" {{ $sender_id->allocation_type == 'shared' ? 'selected' : '' }}>Shared</option>
                                                <option value="dedicated" {{ $sender_id->allocation_type == 'dedicated' ? 'selected' : '' }}>Dedicated</option>
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                        </div>
                                    </form>
                                </td>
                                <td>
                                    <a href="{{ route('admin.senderid.show', $sender_id->uid) }}" class="btn btn-sm btn-outline-primary">
                                        <i data-feather="edit"></i> View/Edit
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center p-2">
                <h5 class="text-muted">No Sender IDs assigned to this customer.</h5>
            </div>
        @endif
    </div>
</div>
