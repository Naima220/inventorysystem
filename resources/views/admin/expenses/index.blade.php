@extends('layouts.admin_master')

@section('content')
<div class="container-fluid mt-4">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <h3 class="mb-2 mb-md-0">All Expenses</h3>
        <a href="{{ route('expenses.create') }}" class="btn btn-success btn-sm">
            <i class="fas fa-plus"></i> Add Expense
        </a>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Responsive Table --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="thead-dark text-center">
                        <tr>
                            <th>#</th>
                            <th>Expense Name</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $expense->expense_name }}</td>
                                <td>{{ $expense->category ?? '-' }}</td>
                                <td class="text-right">
                                    ${{ number_format($expense->amount, 2) }}
                                </td>
                                <td class="text-center">{{ $expense->expense_date }}</td>
                                <td class="text-center">
                                    <a href="{{ route('expenses.edit', $expense->id) }}"
                                       class="btn btn-sm btn-primary mb-1">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('expenses.destroy', $expense->id) }}"
                                          method="POST"
                                          style="display:inline-block;"
                                          onsubmit="return confirm('Are you sure you want to delete this expense?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger mb-1">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    No expenses found
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
