@extends('layouts.admin_master')

@section('content')
<div class="container py-4">
    <h3>📊 Income & Outcome Report</h3>
    <div class="card mt-3">
        <div class="card-body">
            <h5>Total Income: <span class="text-success">${{ number_format($totalIncome,2) }}</span></h5>

            <h5>Total Salaries: <span class="text-danger">${{ number_format($totalSalaries,2) }}</span></h5>

            <h5>Total Other Expenses: <span class="text-danger">${{ number_format($totalExpenses,2) }}</span></h5>

            <h5>Total Payments: <span class="text-success">${{ number_format($totalPayments,2) }}</span></h5>

            <h5>Total Outcome: <span class="text-danger">${{ number_format($totalOutcome,2) }}</span></h5>

            <hr>
            <h4>💰 Profit (Income - Outcome): 
                <span class="{{ $profit >= 0 ? 'text-success' : 'text-danger' }}">
                    ${{ number_format($profit,2) }}
                </span>
            </h4>
        </div>
    </div>
</div>
@endsection
