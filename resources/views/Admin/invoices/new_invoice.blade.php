@extends('layouts.admin_master')

@section('content')

<style>
/* ============ MOBILE RESPONSIVE ============ */
@media (max-width: 768px) {

    #invoiceItemsTable thead {
        display: none;
    }

    #invoiceItemsTable,
    #invoiceItemsTable tbody,
    #invoiceItemsTable tr,
    #invoiceItemsTable td {
        display: block;
        width: 100%;
    }

    #invoiceItemsTable tr {
        background: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 15px;
    }

    #invoiceItemsTable td {
        border: none;
        padding: 6px 0;
    }

    #invoiceItemsTable td::before {
        font-weight: bold;
        display: block;
        color: #555;
        margin-bottom: 2px;
    }

    #invoiceItemsTable td:nth-of-type(1)::before { content: "Product"; }
    #invoiceItemsTable td:nth-of-type(2)::before { content: "Category"; }
    #invoiceItemsTable td:nth-of-type(3)::before { content: "Sale Price"; }
    #invoiceItemsTable td:nth-of-type(4)::before { content: "Quantity"; }
    #invoiceItemsTable td:nth-of-type(5)::before { content: "Total"; }
    #invoiceItemsTable td:nth-of-type(6)::before { content: "Action"; }

    .add-row-btn {
        width: 100%;
    }
}
</style>

<div class="container mt-4">
    <h4 class="mb-4">Create New Invoice</h4>

    <form action="{{ route('invoice.store') }}" method="POST">
        @csrf

        <!-- Customer -->
        <div class="mb-3">
            <label>Customer</label>
            <select name="customer_id" class="form-control" required>
                <option value="">Select Customer</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">
                        {{ $customer->customer_name }} - {{ $customer->phone }}
                    </option>
                @endforeach
            </select>
        </div>

        <input type="hidden" name="total_amount" id="total_amount" value="0">

        <!-- TABLE -->
        <div class="table-responsive">
            <table class="table table-bordered" id="invoiceItemsTable">
                <thead class="thead-dark">
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Sale Price</th>
                        <th>Qty</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>
                            <select name="products[0][product_id]" class="form-control product-select" required>
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </td>

                        <td>
                            <input type="text" name="products[0][category]"
                                   class="form-control category-field"
                                   placeholder="Category" readonly>
                        </td>

                        <td>
                            <input type="number" name="products[0][sales_unit_price]"
                                   class="form-control price-field"
                                   placeholder="Price" readonly>
                        </td>

                        <td>
                            <input type="number" name="products[0][qty]"
                                   class="form-control qty-field"
                                   placeholder="Quantity" min="1" value="1">
                        </td>

                        <td>
                            <input type="text" name="products[0][total_price]"
                                   class="form-control total-field"
                                   placeholder="Total" readonly>
                        </td>

                        <td>
                            <button type="button" class="btn btn-danger btn-sm removeRowBtn">
                                Remove
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- ADD ROW BUTTON (NOW VISIBLE ON MOBILE) -->
        <button type="button" id="addRowBtn"
                class="btn btn-success btn-sm add-row-btn mb-3">
            + Add Row
        </button>

        <!-- TOTAL -->
        <div class="mb-3">
            <label>Total Amount</label>
            <input type="text" id="display_total" class="form-control" readonly value="0.00">
        </div>

        <!-- DEBT -->
        <div class="mb-3">
            <label>Debt</label>
            <input type="number" step="0.01" name="debt" class="form-control" value="0">
        </div>

        <button class="btn btn-primary btn-block">
            Create Invoice
        </button>
    </form>
</div>
@endsection

@section('scripts')
<script>
let rowCount = 1;

/* ================= ADD ROW ================= */
document.getElementById('addRowBtn').addEventListener('click', function () {

    const tbody = document.querySelector('#invoiceItemsTable tbody');
    const newRow = tbody.rows[0].cloneNode(true);

    newRow.querySelectorAll('input').forEach(i => i.value = '');
    newRow.querySelector('.qty-field').value = 1;
    newRow.querySelector('select').selectedIndex = 0;

    newRow.querySelectorAll('input, select').forEach(el => {
        el.name = el.name.replace(/\[\d+\]/, `[${rowCount}]`);
    });

    tbody.appendChild(newRow);
    rowCount++;
});

/* ================= REMOVE ROW ================= */
document.addEventListener('click', function (e) {

    if (e.target.classList.contains('removeRowBtn')) {

        const rows =
            document.querySelectorAll('#invoiceItemsTable tbody tr');

        if (rows.length > 1) {
            e.target.closest('tr').remove();
            updateTotal();
        }
    }
});

/* ================= PRODUCT CHANGE ================= */
document.addEventListener('change', function (e) {

    if (e.target.classList.contains('product-select')) {

        if (!e.target.value) return;

        const row = e.target.closest('tr');

       fetch("{{ route('get.product.info') }}?id=" + e.target.value)
        .then(res => res.json())
        .then(data => {

            row.querySelector('.category-field').value =
                data.product.category ?? '';

            row.querySelector('.price-field').value =
                data.product.sales_unit_price ?? 0;

            updateRow(row);
        });
    }
});

/* ================= QTY CHANGE ================= */
document.addEventListener('input', function (e) {

    if (e.target.classList.contains('qty-field')) {
        updateRow(e.target.closest('tr'));
    }

});

/* ================= ROW TOTAL ================= */
function updateRow(row) {

    const price =
        parseFloat(row.querySelector('.price-field').value) || 0;

    const qty =
        parseFloat(row.querySelector('.qty-field').value) || 0;

    row.querySelector('.total-field').value =
        (price * qty).toFixed(2);

    updateTotal();
}

/* ================= GRAND TOTAL ================= */
function updateTotal() {

    let total = 0;

    document.querySelectorAll('.total-field')
        .forEach(f => {
            total += parseFloat(f.value) || 0;
        });

    document.getElementById('total_amount').value =
        total.toFixed(2);

    document.getElementById('display_total').value =
        total.toFixed(2);
}

</script>
@endsection