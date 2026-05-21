@extends('layouts.admin_master')

@section('content')
<div class="container mt-4">
    <h3>Add Payment for Invoice #{{ $invoice->id }}</h3>
    <p><strong>Customer:</strong> {{ $invoice->customer->customer_name }}</p>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product</th>
                <th>Sale Price</th>
                <th>Qty</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($invoice->invoiceItems as $item)
                @php $lineTotal = $item->quantity * $item->product->sales_unit_price; @endphp
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ number_format($item->product->sales_unit_price,2) }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($lineTotal,2) }}</td>
                </tr>
                @php $total += $lineTotal; @endphp
            @endforeach
        </tbody>
    </table>

    <form action="{{ route('payments.store') }}" method="POST" id="paymentForm">
        @csrf
        <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
        <input type="hidden" name="customer_id" value="{{ $invoice->customer_id }}">

        <div class="form-group">
            <label>Total Amount</label>
            <input type="number" class="form-control" id="total" value="{{ $total }}" readonly>
        </div>

        <div class="form-group">
            <label>Discount</label>
            <input type="number" class="form-control" name="discount" id="discount" value="0" min="0" step="0.01">
        </div>

        <div class="form-group">
            <label>Grand Total (after discount)</label>
            <input type="number" class="form-control" id="grandTotal" readonly>
        </div>

        <div class="form-group">
            <label>Paid</label>
            <input type="number" class="form-control" name="paid" id="paid" required step="0.01" min="0">
        </div>

        <div class="form-group">
            <label>Debt</label>
            <input type="number" class="form-control" name="debt" id="debt" readonly>
        </div>

        <button type="submit" class="btn btn-success">Save Payment</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const totalInput = document.getElementById('total');
    const discountInput = document.getElementById('discount');
    const grandTotalInput = document.getElementById('grandTotal');
    const paidInput = document.getElementById('paid');
    const debtInput = document.getElementById('debt');
    const form = document.getElementById('paymentForm');

    function recalc() {
        let total = parseFloat(totalInput.value) || 0;
        let discount = parseFloat(discountInput.value) || 0;
        if (discount > total) discount = total;
        let grandTotal = total - discount;
        grandTotalInput.value = grandTotal.toFixed(2);

        let paid = parseFloat(paidInput.value) || 0;
        if (paid > grandTotal) {
            paid = grandTotal;
            paidInput.value = grandTotal.toFixed(2);
            alert('⚠️ Paid cannot be more than grand total!');
        }

        debtInput.value = (grandTotal - paid).toFixed(2);
    }

    discountInput.addEventListener('input', recalc);
    paidInput.addEventListener('input', recalc);
    recalc();

    form.addEventListener('submit', function(e){
        let grandTotal = parseFloat(grandTotalInput.value) || 0;
        let paid = parseFloat(paidInput.value) || 0;
        if (paid > grandTotal) {
            e.preventDefault();
            alert('❌ Paid amount cannot exceed grand total!');
        }
    });
});
</script>
@endsection