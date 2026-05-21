@extends('layouts.admin_master')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-12">

            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <strong>Create Invoice for Order #{{ $order->id }}</strong>
                    <a href="{{ route('invoice.index') }}" class="btn btn-sm btn-primary mt-2 mt-md-0">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>

                <div class="card-body">

                    {{-- Errors --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('invoice.store') }}" method="POST">
                        @csrf

                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <input type="hidden" name="customer_id" value="{{ $order->customer->id }}">

                        {{-- Customer Info --}}
                        <div class="row mb-3">
                            <div class="col-md-6 col-sm-12">
                                <label class="form-label fw-bold">Customer</label>
                                <input type="text" class="form-control"
                                       value="{{ $order->customer->customer_name }} ({{ $order->customer->phone }})"
                                       readonly>
                            </div>
                        </div>

                        <h5 class="mt-4 mb-3">Order Items</h5>

                        {{-- Responsive Table --}}
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Product</th>
                                        <th>Serial No</th>
                                        <th>Sale Price</th>
                                        <th>Qty</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $totalAmount = 0; @endphp

                                    @foreach($order->orderItems as $index => $item)
                                        @php
                                            $lineTotal = $item->quantity * $item->product->sales_unit_price;
                                            $totalAmount += $lineTotal;
                                        @endphp
                                        <tr>
                                            <td>
                                                {{ $item->product->name ?? 'N/A' }}
                                                <input type="hidden"
                                                       name="products[{{ $index }}][product_id]"
                                                       value="{{ $item->product_id }}">
                                            </td>

                                            <td>{{ $item->product->product_sn ?? 'N/A' }}</td>

                                            <td>
                                                <input type="number"
                                                       class="form-control form-control-sm"
                                                       name="products[{{ $index }}][sales_unit_price]"
                                                       value="{{ $item->product->sales_unit_price }}"
                                                       readonly>
                                            </td>

                                            <td>
                                                <input type="number"
                                                       class="form-control form-control-sm"
                                                       name="products[{{ $index }}][qty]"
                                                       value="{{ $item->quantity }}"
                                                       readonly>
                                            </td>

                                            <td>
                                                <input type="text"
                                                       class="form-control form-control-sm"
                                                       value="{{ number_format($lineTotal, 2) }}"
                                                       readonly>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Total --}}
                        <div class="row mt-3">
                            <div class="col-md-4 offset-md-8 col-sm-12">
                                <label class="form-label fw-bold">Total Amount</label>
                                <input type="text" class="form-control fw-bold"
                                       value="{{ number_format($totalAmount, 2) }}" readonly>
                                <input type="hidden" name="total_amount" value="{{ $totalAmount }}">
                            </div>
                        </div>

                        {{-- Debt --}}
                        <div class="row mt-3">
                            <div class="col-md-4 col-sm-12">
                                <label class="form-label">Debt (if any)</label>
                                <input type="number"
                                       step="0.01"
                                       name="debt"
                                       class="form-control"
                                       placeholder="Enter debt amount"
                                       value="0">
                            </div>
                        </div>

                        {{-- Submit --}}
                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-success px-4">
                                <i class="fas fa-save"></i> Save Invoice
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
