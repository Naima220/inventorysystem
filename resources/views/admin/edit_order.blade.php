@extends('layouts.admin_master')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">✏️ Edit Order</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('orders.update', $order->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Customer Name</label>
                        <input type="text" name="customer_name" class="form-control" value="{{ $order->customer_name }}">
                    </div>
                    <div class="col-md-6">
                        <label>Customer Phone</label>
                        <input type="text" name="customer_phone" class="form-control" value="{{ $order->customer_phone }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label>Status</label>
                    <select name="order_status" class="form-select">
                        <option value="0" {{ $order->order_status == 0 ? 'selected' : '' }}>Pending</option>
                        <option value="1" {{ $order->order_status == 1 ? 'selected' : '' }}>Delivered</option>
                    </select>
                </div>

                <hr>
                <h5 class="mb-3">🛒 Order Items</h5>

                <div id="item-container">
                    @foreach ($order->orderItems as $index => $item)
                        <div class="row mb-3 item-row border p-2 rounded">
                            <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">

                            <div class="col-md-3">
                                <label>Product</label>
                                <select name="items[{{ $index }}][product_id]" class="form-select">
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label>Quantity</label>
                                <input type="number" name="items[{{ $index }}][quantity]" class="form-control qty-input" value="{{ $item->quantity }}">
                            </div>

                            <div class="col-md-2">
                                <label>Sale Price</label>
                                <input type="number" step="0.01" name="items[{{ $index }}][sale_price]" class="form-control price-input" value="{{ $item->sale_price }}">
                            </div>

                            <div class="col-md-2">
                                <label>Total</label>
                                <input type="text" class="form-control total-output" value="{{ $item->quantity * $item->sale_price }}" readonly>
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-danger remove-item w-100">Remove</button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="button" id="add-item" class="btn btn-success mt-2">➕ Add More</button>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">💾 Update Order</button>
                    <a href="{{ route('all.orders') }}" class="btn btn-secondary">↩️ Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Template --}}
<template id="item-template">
    <div class="row mb-3 item-row border p-2 rounded">
        <div class="col-md-3">
            <label>Product</label>
            <select name="items[__index__][product_id]" class="form-select">
                @foreach ($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label>Quantity</label>
            <input type="number" name="items[__index__][quantity]" class="form-control qty-input" value="1">
        </div>

        <div class="col-md-2">
            <label>Sale Price</label>
            <input type="number" step="0.01" name="items[__index__][sale_price]" class="form-control price-input" value="0.00">
        </div>

        <div class="col-md-2">
            <label>Total</label>
            <input type="text" class="form-control total-output" value="0.00" readonly>
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <button type="button" class="btn btn-danger remove-item w-100">Remove</button>
        </div>
    </div>
</template>

<script>
    let itemIndex = {{ count($order->orderItems) }};

    // Add new item
    document.getElementById('add-item').addEventListener('click', function () {
        const template = document.getElementById('item-template').innerHTML;
        const newItem = template.replace(/__index__/g, itemIndex++);
        document.getElementById('item-container').insertAdjacentHTML('beforeend', newItem);
    });

    // Remove item
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-item')) {
            e.target.closest('.item-row').remove();
        }
    });

    // Auto calculate total
    document.addEventListener('input', function (e) {
        if (e.target.classList.contains('quantity') || e.target.classList.contains('sale-price')) {
            const row = e.target.closest('.item-row');
            const qty = parseFloat(row.querySelector('.quantity').value) || 0;
            const price = parseFloat(row.querySelector('.sale-price').value) || 0;
            row.querySelector('.total-price').value = (qty * price).toFixed(2);
        }
    });

    // Load sale price automatically via AJAX
    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('product-select')) {
            const productId = e.target.value;
            const row = e.target.closest('.item-row');
            if (productId) {
                fetch(`/get-product-price/${productId}`)
                    .then(response => response.json())
                    .then(data => {
                        row.querySelector('.sale-price').value = data.sale_price;
                        const qty = parseFloat(row.querySelector('.quantity').value) || 0;
                        row.querySelector('.total-price').value = (qty * data.sale_price).toFixed(2);
                    });
            }
        }
    });
    function updateTotal(row) {
    // Qaado qiimaha quantity iyo sale_price
    const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
    const price = parseFloat(row.querySelector('.price-input').value) || 0;

    // Xisaabi totalka
    const total = (qty * price).toFixed(2);

    // Ku qor total input-ka total-output
    row.querySelector('.total-output').value = total;
}

// Markasta oo qty ama sale_price input la beddelo, wac function-ka updateTotal
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('qty-input') || e.target.classList.contains('price-input')) {
        const row = e.target.closest('.item-row');
        updateTotal(row);
    }
});

</script>

@endsection
