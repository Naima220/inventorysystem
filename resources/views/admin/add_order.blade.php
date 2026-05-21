@extends('layouts.admin_master')

@section('content')

<style>
/* ===== Mobile App-like Responsive Table ===== */
@media (max-width: 768px) {

    .order-table thead {
        display: none;
    }

    .order-table,
    .order-table tbody,
    .order-table tr,
    .order-table td {
        display: block;
        width: 100%;
    }

    .order-table tr {
        margin-bottom: 15px;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        padding: 10px;
        background: #fff;
    }

    .order-table td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 5px;
        border: none;
        border-bottom: 1px solid #eee;
    }

    .order-table td:last-child {
        border-bottom: none;
    }

    .order-table td::before {
        content: attr(data-label);
        font-weight: 600;
        color: #555;
        width: 45%;
    }

    .order-table td input,
    .order-table td select {
        width: 50%;
    }

    .removeRow {
        width: 100%;
        margin-top: 5px;
    }
}
</style>

<main class="container mt-4">
    <div class="card shadow border-0">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Create New Order</h4>
            <a href="{{ url('all-orders') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <div class="card-body">
            <form action="{{ url('/insert-order') }}" method="POST">
                @csrf

                <!-- Customer -->
                <div class="form-group">
                    <label>Customer</label>
                    <select name="customer_id" class="form-control" required>
                        <option value="">-- Select Customer --</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">
                                {{ $customer->customer_name }} - {{ $customer->phone }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <hr>
                <h5 class="mb-3">Order Items</h5>

                <div class="table-responsive">
                    <table class="table table-bordered order-table" id="order-items">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Stock</th>
                                <th>Product SN</th>
                                <th>Sale Price</th>
                                <th>Qty</th>
                                <th>Total</th>
                                <th>
                                    <button type="button" class="btn btn-sm btn-success" id="addRow">+</button>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td data-label="Product">
                                    <select name="items[0][product_id]" class="form-control product-select" required>
                                        <option value="">-- Choose --</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}"
                                                data-stock="{{ $product->stock }}"
                                                data-sn="{{ $product->product_sn }}"
                                                data-price="{{ $product->sales_unit_price }}">
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                <td data-label="Stock">
                                    <input type="text" class="form-control stock" readonly>
                                </td>

                                <td data-label="Product SN">
                                    <input type="text" name="items[0][product_sn]" class="form-control product_sn" readonly>
                                </td>

                                <td data-label="Sale Price">
                                    <input type="number" name="items[0][sale_price]" class="form-control sale_price" readonly>
                                </td>

                                <td data-label="Quantity">
                                    <input type="number" name="items[0][quantity]" class="form-control quantity" min="1" required>
                                </td>

                                <td data-label="Total">
                                    <input type="text" class="form-control total" readonly>
                                </td>

                                <td data-label="Action">
                                    <button type="button" class="btn btn-danger btn-sm removeRow">Remove</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button type="submit" class="btn btn-primary btn-block mt-3">
                    Submit Order
                </button>
            </form>
        </div>
    </div>
</main>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let rowIdx = 1;

$(document).on('change', '.product-select', function () {
    let row = $(this).closest('tr');
    let selected = $(this).find(':selected');

    row.find('.stock').val(selected.data('stock'));
    row.find('.sale_price').val(selected.data('price'));
    row.find('.product_sn').val(selected.data('sn'));
});

$(document).on('input', '.quantity', function () {
    let row = $(this).closest('tr');
    let qty = parseFloat($(this).val());
    let price = parseFloat(row.find('.sale_price').val());

    row.find('.total').val(
        (!isNaN(qty) && !isNaN(price)) ? (qty * price).toFixed(2) : ''
    );
});

$('#addRow').on('click', function () {
    let newRow = $('#order-items tbody tr:first').clone();

    newRow.find('input, select').each(function () {
        let name = $(this).attr('name');
        if (name) {
            $(this).attr('name', name.replace(/\d+/, rowIdx));
        }
        $(this).val('');
    });

    $('#order-items tbody').append(newRow);
    rowIdx++;
});

$(document).on('click', '.removeRow', function () {
    if ($('#order-items tbody tr').length > 1) {
        $(this).closest('tr').remove();
    }
});
</script>
@endsection
