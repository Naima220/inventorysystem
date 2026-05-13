@extends('layouts.admin_master')

@section('content')

<main>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0 rounded-lg mt-5">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="text-primary m-0">
                            {{ isset($expense) ? 'Edit Expense' : 'Add New Expense' }}
                        </h3>
                        <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-arrow-left"></i> Back to Expenses
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ isset($expense) ? route('expenses.update', $expense->id) : route('expenses.store') }}" method="POST">
                            @csrf
                            @if(isset($expense)) @method('PUT') @endif

                            <div class="form-row">
                                <div class="col-md-6 mb-3">
                                    <label for="expense_name">Expense Name</label>
                                    <input type="text" name="expense_name" class="form-control py-3" value="{{ $expense->expense_name ?? old('expense_name') }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="amount">Amount</label>
                                    <input type="number" step="0.01" name="amount" class="form-control py-3" value="{{ $expense->amount ?? old('amount') }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="category">Category</label>
                                    <input type="text" name="category" class="form-control py-3" value="{{ $expense->category ?? old('category') }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="expense_date">Date</label>
                                    <input type="date" name="expense_date" class="form-control py-3" value="{{ $expense->expense_date ?? old('expense_date') }}" required>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="notes">Notes</label>
                                    <textarea name="notes" class="form-control" rows="4">{{ $expense->notes ?? old('notes') }}</textarea>
                                </div>
                            </div>

                            <div class="form-group mt-4 mb-0">
                                <button class="btn btn-sm btn-primary btn-block py-2">
                                    <i class="fas fa-save"></i> {{ isset($expense) ? 'Update Expense' : 'Save Expense' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@endsection
