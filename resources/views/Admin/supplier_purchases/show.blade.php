@extends('layouts.admin_master')

@section('content')
<div class="container mt-4">
    <h4>Purchase Details</h4>
    <p><strong>Supplier:</strong> {{ $purchase->supplier_name }}</p>
    <p><strong>Total Cost:</strong> {{ $purchase->t_cost }} $</p>
    <p><strong>Discount:</strong> {{ $purchase->discount }} $</p>
    <p><strong>Paid:</strong> {{ $purchase->paid }} $</p>
    <p><strong>Balance:</strong> {{ $purchase->balance }} $</p>

    <h5>Items</h5>
    <ul>
        @foreach($purchase->items as $item)
            <li>
                {{ $item->product->name ?? 'Product not found' }} - Qty: {{ $item->qty }} - Cost: ${{ $item->cost_price }}
            </li>
        @endforeach
    </ul>
</div>
@endsection
