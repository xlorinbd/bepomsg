
<!-- notifications -->

<div class="card">
    <div class="card-header border-bottom">
        <h4 class="card-title">Email Notifications Log</h4>
    </div>
    <div class="table-responsive">
        <table class="table text-nowrap">
            <thead>
            <tr>
                <th>Subject</th>
                <th>Type</th>
                <th>Status</th>
                <th>Error Message</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse(\App\Models\EmailLog::where('user_id', $customer->id)->orderBy('created_at', 'desc')->get() as $log)
                <tr>
                    <td>{{ $log->subject }}</td>
                    <td>{{ ucfirst($log->type) }}</td>
                    <td>
                        @if($log->status == 'sent')
                            <span class="badge badge-light-success">Sent</span>
                        @else
                            <span class="badge badge-light-danger">Failed</span>
                        @endif
                    </td>
                    <td>
                        <small class="text-danger">{{ $log->error_message }}</small>
                    </td>
                    <td>{{ $log->created_at->diffForHumans() }}</td>
                    <td>
                        @if($log->status == 'failed')
                            <button class="btn btn-sm btn-primary resend-email" data-id="{{ $log->id }}">
                                <i data-feather="rotate-cw" class="me-25"></i> Resend
                            </button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No email logs found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('page-script')
    <script>
        $(document).ready(function () {
            $('.resend-email').on('click', function () {
                let id = $(this).data('id');
                let $btn = $(this);
                $btn.prop('disabled', true);

                $.ajax({
                    url: "{{ route('admin.customers.resend-email', $customer->uid) }}",
                    type: "POST",
                    data: {
                        _token: "{{csrf_token()}}",
                        log_id: id
                    },
                    success: function (data) {
                        if (data.status === 'success') {
                            toastr['success'](data.message, 'Success!!');
                            location.reload();
                        } else {
                            toastr['error'](data.message, 'Error!!');
                            $btn.prop('disabled', false);
                        }
                    },
                    error: function (reject) {
                        toastr['error']('Something went wrong', 'Error!!');
                        $btn.prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endpush

<!--/ notifications -->
