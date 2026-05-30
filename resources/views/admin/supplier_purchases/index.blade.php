@extends('layouts.admin_master')

@section('content')
<div class="container-fluid py-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="fas fa-shopping-basket mr-2 text-primary"></i>Supplier Purchases</h3>
        <div>
            <a href="{{ route('supplier-purchases.create') }}" class="btn btn-primary mr-2">
                <i class="fas fa-plus mr-1"></i> Buy Existing Product
            </a>
            <a href="{{ route('supplier-purchases.create-new') }}" class="btn btn-success">
                <i class="fas fa-plus-circle mr-1"></i> Add New Product & Purchase
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white shadow text-center">
                <div class="card-body py-3">
                    <div class="text-uppercase small font-weight-bold mb-1">Total Purchases</div>
                    <div class="h4 mb-0">{{ $purchases->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white shadow text-center">
                <div class="card-body py-3">
                    <div class="text-uppercase small font-weight-bold mb-1">Total Paid</div>
                    <div class="h4 mb-0">${{ number_format($purchases->sum('paid'), 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white shadow text-center">
                <div class="card-body py-3">
                    <div class="text-uppercase small font-weight-bold mb-1">Total Balance (Deyn)</div>
                    <div class="h4 mb-0">${{ number_format($purchases->sum('balance'), 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark text-white shadow text-center">
                <div class="card-body py-3">
                    <div class="text-uppercase small font-weight-bold mb-1">Total Cost</div>
                    <div class="h4 mb-0">${{ number_format($purchases->sum('sup_cost'), 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card shadow">
        <div class="card-header bg-white">
            <h5 class="mb-0">📋 All Purchases List</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center align-middle mb-0" id="purchasesTable">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Supplier</th>
                            <th>Total Cost</th>
                            <th>Discount</th>
                            <th>Supplier Cost</th>
                            <th>Paid</th>
                            <th>Balance (Deyn)</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases as $p)
                        <tr>
                            <td>{{ $p->id }}</td>
                            <td class="font-weight-bold text-left">
                                <i class="fas fa-store text-primary mr-1"></i>{{ $p->supplier_name }}
                            </td>
                            <td>${{ number_format($p->t_cost, 2) }}</td>
                            <td>
                                @if($p->discount > 0)
                                    <span class="text-success">-${{ number_format($p->discount, 2) }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="font-weight-bold">${{ number_format($p->sup_cost, 2) }}</td>
                            <td><span class="badge badge-success">${{ number_format($p->paid, 2) }}</span></td>
                            <td>
                                @if($p->balance > 0)
                                    <span class="badge badge-danger">${{ number_format($p->balance, 2) }}</span>
                                @else
                                    <span class="badge badge-success">✔ Paid</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $p->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('supplier-purchases.show', $p->id) }}" class="btn btn-sm btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('supplier-purchases.edit', $p->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('supplier-purchases.destroy', $p->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                No purchases found yet.
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