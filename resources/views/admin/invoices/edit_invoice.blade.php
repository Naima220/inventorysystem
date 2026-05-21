@extends('layouts.admin_master')

@section('content')
<div class="container">
    <h4>Edit Invoice</h4>

    <form action="{{ route('invoice.update', $invoice->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Customer Selection -->
        <div class="form-group mb-3">
            <label for="customer_id">Customer</label>
            <select name="customer_id" class="form-control" required>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}" 
                        {{ $invoice->customer_id == $customer->id ? 'selected' : '' }}>
                        {{ $customer->customer_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Invoice Items Table -->
        <h5 class="mt-4">Invoice Items</h5>
        <table class="table table-bordered" id="invoice-items">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Sale Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->invoiceItems as $index => $item)
                <tr>
                    <td>
                        <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                        <select name="items[{{ $index }}][product_id]" class="form-control" required>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" 
                                    {{ $product->id == $item->product_id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" name="items[{{ $index }}][quantity]" class="form-control qty"
                               value="{{ $item->quantity }}" min="1" required>
                    </td>
                    <td>
                        <input type="number" name="items[{{ $index }}][sale_price]" class="form-control sale_price"
                               value="{{ $item->sale_price }}" step="0.01" required>
                    </td>
                    <td class="total">
                        {{ number_format($item->quantity * $item->sale_price, 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Submit Button -->
        <div class="text-end mt-3">
            <button type="submit" class="btn btn-primary">Update Invoice</button>
        </div>
    </form>
</div>

<!-- JavaScript to Auto Calculate Totals -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('#invoice-items tbody tr');

        rows.forEach(row => {
            const qtyInput = row.querySelector('.qty');
            const priceInput = row.querySelector('.sale_price');
            const totalCell = row.querySelector('.total');

            function updateTotal() {
                const qty = parseFloat(qtyInput.value) || 0;
                const price = parseFloat(priceInput.value) || 0;
                const total = qty * price;
                totalCell.textContent = total.toFixed(2);
            }

            qtyInput.addEventListener('input', updateTotal);
            priceInput.addEventListener('input', updateTotal);
        });
    });
</script>
@endsection
