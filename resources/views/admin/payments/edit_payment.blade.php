@extends('layouts.admin_master')

@section('content')
<div class="container mt-4">
    <h3>Edit Payment #{{ $payment->id }}</h3>
    <p><strong>Customer:</strong> {{ $payment->customer->customer_name }}</p>
    <p><strong>Date:</strong> {{ $payment->date }}</p>

    <form action="{{ route('payments.update', $payment->id) }}" method="POST" id="editPaymentForm">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Total Before Discount</label>
            <input type="number" class="form-control" id="total" value="{{ $payment->invoice->invoiceItems->sum(function($item){ return $item->quantity * $item->product->sales_unit_price; }) }}" readonly>
        </div>

        <div class="form-group">
            <label>Discount</label>
            <input type="number" class="form-control" name="discount" id="discount"
                   value="{{ $payment->discount > 0 ? $payment->discount : '' }}" step="0.01" min="0">
        </div>

        <div class="form-group">
            <label>Total After Discount</label>
            <input type="number" class="form-control" id="total_after_discount" readonly>
        </div>

        <div class="form-group">
            <label>Paid</label>
            <input type="number" class="form-control" name="paid" id="paid"
                   value="{{ $payment->paid }}" required step="0.01" min="0">
        </div>

        <div class="form-group">
            <label>Debt</label>
            <input type="number" class="form-control" name="debt" id="debt"
                   value="{{ $payment->debt }}" readonly>
        </div>

        <button type="submit" class="btn btn-success">Update Payment</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const totalInput = document.getElementById('total');
    const discountInput = document.getElementById('discount');
    const totalAfterDiscountInput = document.getElementById('total_after_discount');
    const paidInput = document.getElementById('paid');
    const debtInput = document.getElementById('debt');

    function calculateAll() {
        let total = parseFloat(totalInput.value) || 0;
        let discount = parseFloat(discountInput.value) || 0;
        if (discount > total) discount = total;
        let totalAfterDiscount = total - discount;
        totalAfterDiscountInput.value = totalAfterDiscount.toFixed(2);

        let paid = parseFloat(paidInput.value) || 0;
        if (paid > totalAfterDiscount) {
            alert('⚠️ Paid amount cannot be more than total after discount!');
            paid = totalAfterDiscount;
            paidInput.value = totalAfterDiscount.toFixed(2);
        }
        let debt = totalAfterDiscount - paid;
        debtInput.value = debt.toFixed(2);
    }

    discountInput.addEventListener('input', calculateAll);
    paidInput.addEventListener('input', calculateAll);
    calculateAll();
});
</script>
@endsection