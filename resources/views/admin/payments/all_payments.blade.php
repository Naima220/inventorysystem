@extends('layouts.admin_master')

@section('content')

<style>
/* ========= MOBILE RESPONSIVE PAYMENTS TABLE ========= */
@media (max-width: 768px) {

    table thead {
        display: none;
    }

    table,
    table tbody,
    table tr,
    table td {
        display: block;
        width: 100%;
    }

    table tr {
        background: #ffffff;
        border: 1px solid #ddd;
        border-radius: 12px;
        margin-bottom: 18px;
        padding: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }

    table td {
        border: none;
        padding: 6px 0;
        font-size: 14px;
    }

    table td::before {
        content: attr(data-label);
        font-weight: 600;
        color: #555;
        display: block;
        margin-bottom: 3px;
    }

    table td:last-child {
        margin-top: 10px;
    }

    table td:last-child a,
    table td:last-child button {
        width: 100%;
        margin-bottom: 6px;
    }

    /* Collapsed items table */
    .collapse table {
        margin-top: 10px;
    }
}
</style>

<div class="container py-4">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <h4 class="mb-4">All Payments</h4>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Payment #</th>
                    <th>Invoice #</th>
                    <th>Customer</th>
                    <th>Paid</th>
                    <th>Debt</th>
                    <th>Discount</th>
                    <th>Total After Discount</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
            @foreach($payments as $payment)
                <tr>
                    <td data-label="Payment #">{{ $payment->id }}</td>
                    <td data-label="Invoice #">{{ $payment->invoice_id }}</td>
                    <td data-label="Customer">{{ $payment->customer->customer_name }}</td>
                    <td data-label="Paid">{{ number_format($payment->paid, 2) }}</td>
                    <td data-label="Debt">{{ number_format($payment->debt, 2) }}</td>
                    <td data-label="Discount">{{ number_format($payment->discount, 2) }}</td>
                    <td data-label="Total">{{ number_format($payment->total_payment, 2) }}</td>
                    <td data-label="Date">{{ $payment->date }}</td>

                    <td data-label="Action">
                        <button class="btn btn-info btn-sm"
                                data-toggle="collapse"
                                data-target="#items-{{ $payment->id }}">
                            <i class="fas fa-eye"></i> View
                        </button>

                        <a href="{{ route('payments.edit', $payment->id) }}"
                           class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i> Edit
                        </a>

                        <form action="{{ route('payments.destroy', $payment->id) }}"
                              method="POST"
                              class="d-inline"
                              onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="btn btn-sm btn-danger">
                                <i class="fas fa-trash-alt"></i> Delete
                            </button>
                        </form>

                        <a href="{{ route('payments.downloadPdf', $payment->id) }}"
                           class="btn btn-sm btn-secondary">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>
                        <a href="{{ route('payments.print', $payment->id) }}"
                    target="_blank"
              class="btn btn-sm btn-success">
              <i class="fas fa-print"></i> Print
             </a>

                    </td>
                </tr>

                <!-- COLLAPSE ITEMS -->
                <tr id="items-{{ $payment->id }}" class="collapse">
                    <td colspan="9">
                        <h6 class="mt-2">Items for Invoice #{{ $payment->invoice_id }}</h6>

                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Sale Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payment->invoice->invoiceItems as $item)
                                    <tr>
                                        <td>{{ $item->product->name }}</td>
                                        <td>{{ number_format($item->product->sales_unit_price, 2) }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ number_format($item->total_price, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Bootstrap JS --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
@endsection
