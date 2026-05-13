@extends('layouts.admin_master')

@section('content')

<div class="container-fluid px-2 px-md-4">

    <!-- TOP BAR -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">

        <!-- Back Button -->
        <a href="{{ url()->previous() }}" class="btn btn-secondary mb-2">
            <i class="fas fa-arrow-left"></i> Back
        </a>

        <!-- Add Debt -->
        <a href="{{ route('debt.create') }}" class="btn btn-dark mb-2">
            <i class="fas fa-plus"></i> Add Debt
        </a>

    </div>

    <!-- CARD -->
    <div class="card shadow border-0 rounded-3">
        <div class="card-body">

            <!-- RESPONSIVE TABLE -->
            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle text-center">

                    <thead class="thead-dark">
                        <tr>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Remaining</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($debts as $debt)
                        <tr>
                            <td>{{ $debt->customer->customer_name ?? 'N/A' }}</td>
                            <td>{{ $debt->amount }}</td>
                            <td>{{ $debt->total_paid }}</td>
                            <td>{{ $debt->remaining }}</td>

                            <td>{{ $debt->created_at->format('d-m-Y') }}</td>

                            <td>
                                @if($debt->status == 'paid')
                                    <span class="badge badge-success px-3 py-2">Paid</span>
                                @elseif($debt->status == 'partial')
                                    <span class="badge badge-warning px-3 py-2">Partial</span>
                                @else
                                    <span class="badge badge-danger px-3 py-2">Unpaid</span>
                                @endif
                            </td>

                            <td>
                                <!-- Pay Button -->
                                <a href="{{ route('debt.pay.form', $debt->id) }}" class="btn btn-success btn-sm mb-1">
                                    Pay
                                </a>

                                <!-- Delete Button -->
                                <form action="{{ route('debt.delete', $debt->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Ma hubtaa inaad delete gareyso?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>

            </div>

        </div>
    </div>

</div>

@endsection