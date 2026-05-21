@extends('layouts.admin_master')

@section('content')
<div class="container py-4">
    <h4 class="mb-4">All Invoices</h4>

    @foreach($invoices as $invoice)
        <div class="card mb-4 shadow-sm">

            {{-- Header --}}
            <div class="card-header d-flex justify-content-between align-items-start align-items-md-center flex-column flex-md-row">
                <div class="mb-2 mb-md-0">
                    <strong>Invoice #{{ $invoice->id }}</strong><br class="d-md-none">
                    <span class="text-muted">
                        Customer: {{ $invoice->customer->customer_name }} |
                        Date: {{ $invoice->date }}
                    </span>
                </div>

                {{-- Buttons (waxba lagama badalin) --}}
                <div class="d-flex flex-wrap gap-1">
                    <a href="{{ route('invoices.edit', $invoice->id) }}" class="btn btn-sm btn-primary">Edit</a>

                    <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>

                    <a href="{{ route('invoices.downloadPdf', $invoice->id) }}"
                       class="btn btn-sm btn-secondary">Download PDF</a>

                    <a href="{{ route('payments.create', $invoice->id) }}"
                       class="btn btn-sm btn-success">Make Payment</a>
                </div>
            </div>

            {{-- Body --}}
            <div class="card-body">

                {{-- Table Responsive --}}
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-3">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Sale Price</th>
                                <th>Qty</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->invoiceItems as $item)
                                <tr>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->product->category }}</td>
                                    <td>{{ $item->sale_price }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ $item->total_price }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Totals --}}
                <div class="row">
                    <div class="col-md-6 col-sm-12 mb-2">
                        <strong>Total Invoice Amount:</strong><br class="d-md-none">
                        <span class="fw-bold">{{ $invoice->total_amount }}</span>
                    </div>

                    
                </div>

            </div>
        </div>
    @endforeach
</div>
@endsection
