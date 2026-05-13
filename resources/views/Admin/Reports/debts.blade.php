@extends('layouts.admin_master')

@section('content')
<div class="container-fluid mt-4">

    <h2 class="text-center text-primary mb-4">💳 Debt Report</h2>
<a href="{{ route('reports.debts', ['type'=>'daily']) }}" class="btn btn-primary">Daily</a>
<a href="{{ route('reports.debts', ['type'=>'weekly']) }}" class="btn btn-success">Weekly</a>
<a href="{{ route('reports.debts', ['type'=>'monthly']) }}" class="btn btn-warning">Monthly</a>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <h5 class="mb-3">Total Remaining Debt: {{ $totalDebt }}</h5>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Remaining</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($debts as $key => $debt)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $debt->customer->customer_name ?? 'N/A' }}</td>
                            <td>{{ $debt->amount }}</td>
                            <td>{{ $debt->paid }}</td>
                            <td class="text-danger font-weight-bold">
                                {{ $debt->remaining }}
                            </td>
                            <td>{{ $debt->created_at->format('Y-m-d') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-success">
                                No debt records found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>
@endsection