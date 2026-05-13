@extends('layouts.admin_master')
@section('content')
<div class="container mt-4">
    <h3>{{ isset($expense) ? 'Edit' : 'Add' }} Expense</h3>

    <form action="{{ isset($expense) ? route('expenses.update', $expense->id) : route('expenses.store') }}" method="POST">
        @csrf
        @if(isset($expense)) @method('PUT') @endif

        <div class="form-group">
            <label>Expense Name</label>
            <input type="text" name="expense_name" class="form-control" value="{{ $expense->expense_name ?? old('expense_name') }}" required>
        </div>

        <div class="form-group">
            <label>Amount</label>
            <input type="number" step="0.01" name="amount" class="form-control" value="{{ $expense->amount ?? old('amount') }}" required>
        </div>

        <div class="form-group">
            <label>Category</label>
            <input type="text" name="category" class="form-control" value="{{ $expense->category ?? old('category') }}">
        </div>

        <div class="form-group">
            <label>Date</label>
            <input type="date" name="expense_date" class="form-control" value="{{ $expense->expense_date ?? old('expense_date') }}" required>
        </div>

        <div class="form-group">
            <label>Notes</label>
            <textarea name="notes" class="form-control">{{ $expense->notes ?? old('notes') }}</textarea>
        </div>

        <button class="btn btn-success">{{ isset($expense) ? 'Update' : 'Save' }}</button>
    </form>
</div>
@endsection
