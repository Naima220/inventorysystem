@extends('layouts.admin_master')

@section('content')

<main>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0 rounded-lg mt-5">
                    <div class="card-header">
                        <h1 class="text-center font-weight-light my-4"><b>Edit Salary</b>
                            <div style="text-align: right;">
                                <a href="{{ route('all.salaries') }}" class="btn btn-sm btn-primary" style="padding: 5px; margin: 5px;">
                                    <i class="fas fa-arrow-left"></i> Back to Salaries
                                </a>
                            </div>
                        </h1>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('salaries.update', $salary->id) }}">
                            @csrf

                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small mb-1">Employee</label>
                                        <select class="form-control" name="employee_id" required>
                                            @foreach($employees as $emp)
                                                <option value="{{ $emp->id }}" {{ $emp->id == $salary->employee_id ? 'selected' : '' }}>
                                                    {{ $emp->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small mb-1">Month</label>
                                        <input type="month" class="form-control" name="month" value="{{ $salary->month }}" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small mb-1">Amount</label>
                                        <input type="number" step="0.01" class="form-control" name="amount" value="{{ $salary->amount }}" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small mb-1">Payment Date</label>
                                        <input type="date" class="form-control" name="payment_date" value="{{ $salary->payment_date }}" required>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="small mb-1">Note</label>
                                        <textarea class="form-control" name="note">{{ $salary->note }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-4 mb-0">
                                <button class="btn btn-primary btn-block">Update Salary</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@endsection