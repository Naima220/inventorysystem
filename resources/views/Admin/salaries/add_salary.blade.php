@extends('layouts.admin_master')

@section('content')

<main>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card shadow-lg border-0 rounded-lg mt-5">
                    <div class="card-header">
                        <h1 class="text-center font-weight-light my-4"><b>Add New Salary</b></h1>
                        <div style="text-align: right;">
                            <a href="{{ route('all.salaries') }}" class="btn btn-sm btn-primary" style="padding: 5px; margin: 5px;">
                                <i class="fas fa-arrow-left"></i> Back to Salaries <i class="fas fa-money-bill"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('salaries.store') }}">
                            @csrf

                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label for="employee_id">Select Employee</label>
                                    <select class="form-control" name="employee_id" required>
                                        <option value="">-- Select Employee --</option>
                                        @foreach($employees as $emp)
                                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="month">Month</label>
                                    <input class="form-control" type="month" name="month" value="{{ old('month') }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="amount">Amount</label>
                                    <input class="form-control" type="number" step="0.01" name="amount" id="amount" readonly required>

                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="payment_date">Payment Date</label>
                                    <input class="form-control" type="date" name="payment_date" value="{{ old('payment_date') }}" required>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="note">Note (optional)</label>
                                    <textarea class="form-control" name="note" rows="3">{{ old('note') }}</textarea>
                                </div>
                            </div>

                            <div class="form-group mt-4 mb-0">
                                <button class="btn btn-primary btn-block" type="submit">Save Salary</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@endsection
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const employeeSelect = document.querySelector('[name="employee_id"]');
        const amountInput = document.querySelector('[name="amount"]');

        employeeSelect.addEventListener('change', function () {
            fetch("{{ url('/api/get-employee-salary') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ id: this.value })
            })
            .then(res => res.json())
            .then(data => {
                amountInput.value = data.salary || 0;
            });
        });
    });
</script>
@endsection
