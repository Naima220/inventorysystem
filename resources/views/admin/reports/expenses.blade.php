@extends('layouts.admin_master')

@section('content')
<div class="container-fluid mt-4">

    <h2 class="text-center mb-4">💸 Expenses Report</h2>
    
<a href="{{ route('reports.expenses', ['type'=>'daily']) }}" class="btn btn-primary">Daily</a>
<a href="{{ route('reports.expenses', ['type'=>'weekly']) }}" class="btn btn-success">Weekly</a>
<a href="{{ route('reports.expenses', ['type'=>'monthly']) }}" class="btn btn-warning">Monthly</a>
    {{-- Filter Form --}}
    <form method="GET" action="{{ route('reports.expenses') }}" class="row gx-3 gy-2 align-items-end mb-4">

        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <label class="small fw-bold">From Date</label>
            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
        </div>

        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <label class="small fw-bold">To Date</label>
            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
        </div>

        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
            <button class="btn btn-primary w-100">Filter</button>
        </div>

    </form>

    {{-- Total Expenses --}}
    <div class="alert alert-info text-center fw-bold">
        💰 Total Expenses: ${{ number_format($totalExpenses, 2) }}
    </div>

    {{-- Expenses Table --}}
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th class="text-center">ID</th>
                    <th>Expense Name</th>
                    <th class="d-none d-sm-table-cell">Category</th>
                    <th class="text-end">Amount ($)</th>
                    <th class="text-center">Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $exp)
                <tr>
                    <td class="text-center">{{ $exp->id }}</td>
                    <td>{{ $exp->expense_name ?? 'N/A' }}</td>
                    <td class="d-none d-sm-table-cell">{{ $exp->category ?? 'N/A' }}</td>
                    <td class="text-end">${{ number_format($exp->amount, 2) }}</td>
                    <td class="text-center">{{ $exp->created_at->format('Y-m-d') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center p-3">No expenses found for the selected range.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
