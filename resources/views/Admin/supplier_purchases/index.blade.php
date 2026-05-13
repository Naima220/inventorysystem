@extends('layouts.admin_master')

@section('content')
<div class="container py-4">

    {{-- ✅ Success message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>📦 All Supplier Purchases</h3>
        <div>
            <a href="{{ route('supplier-purchases.create') }}" class="btn btn-primary">
                ➕ Add Purchase
            </a>
            <a href="{{ route('supplier-purchases.create-new') }}" class="btn btn-success">
                ➕ Add New Product & Purchase
            </a>
        </div>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Supplier</th>
                <th>Total Cost</th>
                <th>Discount</th>
                <th>Supplier Cost</th>
                <th>Paid</th>
                <th>Balance</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchases as $p)
                <tr>
                    <td>{{ $p->id }}</td>
                    <td>{{ $p->supplier_name }}</td>
                    <td>${{ number_format($p->t_cost, 2) }}</td>
                    <td>
                        @if($p->discount > 0)
                            ${{ number_format($p->discount, 2) }}
                        @else
                            -
                        @endif
                    </td>
                    <td>${{ number_format($p->sup_cost, 2) }}</td>
                    <td>${{ number_format($p->paid, 2) }}</td>
                    <td>
                        @if($p->balance > 0)
                            <span class="text-danger">${{ number_format($p->balance, 2) }}</span>
                        @else
                            <span class="text-success">0.00</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('supplier-purchases.show', $p->id) }}" class="btn btn-sm btn-secondary">
                            View
                        </a>
                        <a href="{{ route('supplier-purchases.edit', $p->id) }}" class="btn btn-sm btn-info">
                            Edit
                        </a>
                        <form action="{{ route('supplier-purchases.destroy', $p->id) }}" method="POST" class="d-inline-block">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure to delete?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">No purchases found yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection