@extends('layouts.admin_master')

@section('content')

<style>
@media (max-width: 768px) {

    #orderItemsTable thead {
        display: none;
    }

    #orderItemsTable,
    #orderItemsTable tbody,
    #orderItemsTable tr,
    #orderItemsTable td {
        display: block;
        width: 100%;
    }

    #orderItemsTable tr {
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 10px;
        background: #f9f9f9;
    }

    #orderItemsTable td {
        border: none;
        padding: 6px 0;
    }

    .add-row-btn {
        width: 100%;
        margin-bottom: 10px;
    }

    #orderItemsTable td button {
        width: 100%;
    }
}
</style>

<main class="container mt-5">
    <div class="card shadow border-0 rounded-lg">
        <div class="card-header">
            <h4 class="mb-0">Add New Order (Multiple Items)</h4>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ url('/insert-order') }}">
                @csrf

                <!-- CUSTOMER -->
                <div class="form-group">
                    <label>Customer</label>
                    <select name="customer_id" class="form-control" required>
                        <option selected disabled>Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">
                                {{ $customer->customer_name }} ({{ $customer->phone }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <h5 class="mt-4 d-flex justify-content-between align-items-center">
                    Order Items
                    <button type="button" id="addRow" class="btn btn-sm btn-success add-row-btn">
                        + Add Item
                    </button>
                </h5>

                <div class="table-responsive">
                    <table class="table table-bordered" id="orderItemsTable">
                        <thead class="thead-dark">
                            <tr>
                                <th>Product</th>
                                <th>Stock</th>
                                <th>Product SN</th>
                                <th>Sale Price</th>
                                <th>Qty</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>
                                    <select name="items[0][product_id]" class="form-control product-select" required>
                                        <option selected disabled>Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}"
                                                data-stock="{{ $product->stock }}"
                                                data-price="{{ $product->sales_unit_price }}"
                                                data-sn="{{ $product->product_sn }}">
                                                {{ $product->name }} ({{ $product->product_sn }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                <td>
                                    <input type="text" class="form-control stock"
                                           placeholder="Stock Available" readonly>
                                </td>

                                <td>
                                    <input type="text" class="form-control product_sn"
                                           name="items[0][product_sn]"
                                           placeholder="Product Serial Number" readonly>
                                </td>

                                <td>
                                    <input type="text" class="form-control sale_price"
                                           name="items[0][sale_price]"
                                           placeholder="Sale Price" readonly>
                                </td>

                                <td>
                                    <input type="number" class="form-control qty"
                                           name="items[0][quantity]"
                                           placeholder="Quantity"
                                           min="1" required>
                                </td>

                                <td>
                                    <input type="text" class="form-control total"
                                           placeholder="Total Amount" readonly>
                                </td>

                                <td>
                                    <button type="button" class="btn btn-danger remove-row">
                                        Remove
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button class="btn btn-primary btn-block mt-4" type="submit">
                    Submit Order
                </button>
            </form>
        </div>
    </div>
</main>

@if(session('error'))
    <div class="alert alert-danger mt-3">{{ session('error') }}</div>
@endif
@endsection

@section('scripts')
<script>
let rowIdx = 1;

$(document).on('change', '.product-select', function () {
    let row = $(this).closest('tr');
    let selected = $(this).find(':selected');

    row.find('.stock').val(selected.data('stock'));
    row.find('.sale_price').val(selected.data('price'));
    row.find('.product_sn').val(selected.data('sn'));
});

$(document).on('input', '.qty', function () {
    let row = $(this).closest('tr');
    let qty = parseFloat($(this).val());
    let price = parseFloat(row.find('.sale_price').val());

    row.find('.total').val(
        (!isNaN(qty) && !isNaN(price)) ? (qty * price).toFixed(2) : ''
    );
});

$('#addRow').on('click', function () {
    let newRow = $('#orderItemsTable tbody tr:first').clone();

    newRow.find('input, select').each(function () {
        let name = $(this).attr('name');
        if (name) {
            $(this).attr('name', name.replace(/\d+/, rowIdx));
        }
        $(this).val('');
    });

    $('#orderItemsTable tbody').append(newRow);
    rowIdx++;
});

$(document).on('click', '.remove-row', function () {
    if ($('#orderItemsTable tbody tr').length > 1) {
        $(this).closest('tr').remove();
    }
});
</script>
@endsection
