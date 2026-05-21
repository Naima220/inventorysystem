@extends('admin.admin_master')

@section('admin')
<div class="container">

    <h4>Invoice #{{ $invoice->id }}</h4>

    <p><strong>Date:</strong>
        {{ $invoice->created_at ? $invoice->created_at->format('d-m-Y H:i') : 'No Date' }}
    </p>

    <p><strong>Customer:</strong> {{ $invoice->customer_name }}</p>
    <p><strong>Phone:</strong> {{ $invoice->customer_phone }}</p>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Sale Price</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($invoice->invoiceItems as $item)
                <tr>
                    <td>{{ $item->product->name ?? 'N/A' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ number_format($item->sale_price, 2) }}</td>
                    <td>${{ number_format($item->total_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p><strong>Total Amount:</strong>
        ${{ number_format($invoice->total_amount, 2) }}
    </p>

    <p><strong>Debt:</strong>
        ${{ number_format($invoice->debt, 2) }}
    </p>

</div>
@endsection