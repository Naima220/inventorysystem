@extends('layouts.admin_master')
@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>✏️ Edit Purchase #{{ $purchase->id }}</h3>
        <a href="{{ route('supplier-purchases.show', $purchase->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Back
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-edit mr-2"></i>Update Purchase Info</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('supplier-purchases.update', $purchase->id) }}" method="POST">
                @csrf @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Supplier Name</label>
                            <input type="text" name="supplier_name" class="form-control"
                                   value="{{ $purchase->supplier_name }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="font-weight-bold">Discount ($)</label>
                            <input type="number" step="0.01" name="discount" class="form-control"
                                   value="{{ $purchase->discount }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="font-weight-bold">Paid ($)</label>
                            <input type="number" step="0.01" name="paid" class="form-control"
                                   value="{{ $purchase->paid }}" required>
                        </div>
                    </div>
                </div>

                <!-- Read-only summary -->
                <div class="alert alert-info">
                    <strong>Total Cost (Immutable):</strong> ${{ number_format($purchase->t_cost, 2) }}
                    &nbsp;|&nbsp;
                    <strong>Current Balance:</strong>
                    <span class="{{ $purchase->balance > 0 ? 'text-danger' : 'text-success' }}">
                        ${{ number_format($purchase->balance, 2) }}
                    </span>
                </div>

                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-save mr-1"></i> Update Purchase
                </button>
                <a href="{{ route('supplier-purchases.index') }}" class="btn btn-secondary ml-2">Cancel</a>
            </form>
        </div>
    </div>

</div>
@endsection