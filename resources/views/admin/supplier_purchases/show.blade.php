@extends('layouts.admin_master')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>📦 Purchase Details #{{ $purchase->id }}</h3>
        <a href="{{ route('supplier-purchases.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Back to All Purchases
        </a>
    </div>

    <div class="row">
        <!-- Purchase Summary Card -->
        <div class="col-md-5 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle mr-2"></i>Purchase Summary</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted font-weight-bold" style="width:45%">Supplier</td>
                            <td><strong>{{ $purchase->supplier_name }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Total Cost</td>
                            <td><strong>${{ number_format($purchase->t_cost, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Discount</td>
                            <td>
                                @if($purchase->discount > 0)
                                    <span class="text-success">-${{ number_format($purchase->discount, 2) }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Supplier Cost</td>
                            <td><strong>${{ number_format($purchase->sup_cost, 2) }}</strong></td>
                        </tr>
                        <tr class="border-top">
                            <td class="text-muted font-weight-bold">Paid</td>
                            <td><span class="badge badge-success px-2 py-1">${{ number_format($purchase->paid, 2) }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Balance (Deyn)</td>
                            <td>
                                @if($purchase->balance > 0)
                                    <span class="badge badge-danger px-2 py-1">${{ number_format($purchase->balance, 2) }}</span>
                                @else
                                    <span class="badge badge-success px-2 py-1">Paid in Full</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Date</td>
                            <td>{{ $purchase->created_at->format('d M Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="col-md-7 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-boxes mr-2"></i>Items Purchased</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered mb-0">
                            <thead class="thead-light">
                                <tr class="text-center">
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Cost Price</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchase->items as $i => $item)
                                <tr class="text-center">
                                    <td>{{ $i + 1 }}</td>
                                    <td class="text-left font-weight-bold">{{ $item->product->name ?? '—' }}</td>
                                    <td>${{ number_format($item->cost_price, 2) }}</td>
                                    <td><span class="badge badge-info">{{ $item->qty }}</span></td>
                                    <td class="text-success font-weight-bold">${{ number_format($item->total_price, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="thead-light">
                                <tr class="text-center font-weight-bold">
                                    <td colspan="4" class="text-right">Grand Total:</td>
                                    <td class="text-danger">${{ number_format($purchase->t_cost, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="d-flex gap-2">
        <a href="{{ route('supplier-purchases.edit', $purchase->id) }}" class="btn btn-warning">
            <i class="fas fa-edit mr-1"></i> Edit Purchase
        </a>
        <form action="{{ route('supplier-purchases.destroy', $purchase->id) }}" method="POST" class="d-inline">
            @csrf @method('DELETE')
            <button class="btn btn-danger" onclick="return confirm('Are you sure?')">
                <i class="fas fa-trash mr-1"></i> Delete
            </button>
        </form>
    </div>

</div>
@endsection
