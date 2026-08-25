@extends('layouts/contentLayoutMaster')

@section('title', 'My Orders')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
@endsection

@section('content')

    {{-- Page Title --}}
    <div class="mb-2">
        <h4><i data-feather="clock" class="me-1 text-primary"></i> My Orders</h4>
        <p class="text-muted mb-0">View all your SMS credit order requests and their status.</p>
    </div>

    {{-- Stats Row --}}
    <div class="row match-height">
        <div class="col-lg-4 col-sm-6 col-12">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="fw-bolder mb-0">{{ number_format($balance) }}</h2>
                        <p class="card-text">Current SMS Balance</p>
                    </div>
                    <div>
                        <i class="font-large-3 text-primary" data-feather="message-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6 col-12">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="fw-bolder mb-0">
                            <sup>{{ $purchases->where('status', 'pending')->count() }}</sup>
                            / {{ $purchases->total() }}
                        </h2>
                        <p class="card-text">Pending / Total Orders</p>
                    </div>
                    <div>
                        <i class="font-large-3 text-warning" data-feather="clock"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6 col-12">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="fw-bolder mb-0">{{ $purchases->where('status', 'completed')->count() }}</h2>
                        <p class="card-text">Approved Orders</p>
                    </div>
                    <div>
                        <i class="font-large-3 text-success" data-feather="check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <section id="datatables-basic">
        <div class="mb-3 mt-2">
            <h4 class="mb-2"><i data-feather="shopping-bag" class="me-1 text-primary"></i> SMS Credit Orders</h4>
            <div class="btn-group">
                <a href="{{ route('customer.buy_sms.index') }}"
                    class="btn btn-success waves-light waves-effect fw-bold">
                    <i data-feather="plus-circle"></i> Buy SMS
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">My Orders</h4>
                        <span class="badge bg-primary">{{ $purchases->total() }} total</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>SMS Quantity</th>
                                    <th>Bonus Credits</th>
                                    <th>Rate (BDT)</th>
                                    <th>Total Amount</th>
                                    <th>Txn Reference</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchases as $purchase)
                                    <tr>
                                        <td class="text-muted small">{{ $purchases->firstItem() + $loop->index }}</td>
                                        <td class="small">{{ $purchase->created_at->format('d M Y') }}<br>
                                            <small class="text-muted">{{ $purchase->created_at->format('h:i A') }}</small>
                                        </td>
                                        <td><strong>{{ number_format($purchase->sms_quantity) }}</strong></td>
                                        <td>
                                            @if($purchase->credits_added > 0)
                                                <span class="badge bg-light-success text-success">
                                                    +{{ number_format($purchase->credits_added) }}
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>৳{{ number_format($purchase->rate, 2) }}</td>
                                        <td><strong>৳{{ number_format($purchase->total_price, 2) }}</strong></td>
                                        <td>
                                            <code class="small">{{ $purchase->transaction_id ?? '—' }}</code>
                                        </td>
                                        <td>
                                            <span class="badge bg-light-secondary text-secondary text-uppercase">
                                                {{ $purchase->payment_method ?? 'offline' }}
                                            </span>
                                        </td>
                                        <td>
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
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-3">
                                            <i data-feather="inbox"></i>
                                            No order history found.
                                            <a href="{{ route('customer.buy_sms.index') }}">Buy SMS</a>
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

@endsection

@section('page-script')
    <script>
        $(document).ready(function () {
            feather.replace();
        });
    </script>
@endsection