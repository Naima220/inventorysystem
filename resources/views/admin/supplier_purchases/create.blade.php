@extends('layouts.admin_master')
@section('content')
<div class="container py-4">
    <h3>Add Purchase</h3>
    <form action="{{ route('supplier-purchases.store') }}" method="POST">
        @csrf
        {{-- ✅ Supplier-ka list ahaan --}}
        <div class="form-group">
            <label>Supplier Name</label>
            <select name="supplier_name" class="form-control" required>
                <option value="">-- Select Supplier --</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->supplier_name }}">{{ $supplier->supplier_name }}</option>

                @endforeach
            </select>
        </div>

        {{-- ✅ Items Table --}}
        <table class="table table-bordered" id="itemsTable">
            <thead class="thead-dark">
                <tr>
                    <th>Product</th>
                    <th>Cost Price</th>
                    <th>Qty</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                {{-- rows dynamically added here --}}
            </tbody>
        </table>

        <div class="text-right mb-3">
            <button type="button" class="btn btn-sm btn-success" id="addRow">
                <i class="fa fa-plus"></i> Add Item
            </button>
        </div>

        <div class="form-group">
            <label>Discount</label>
            <input type="number" step="0.01" name="discount" class="form-control">
        </div>
        <div class="form-group">
            <label>Paid</label>
            <input type="number" step="0.01" name="paid" class="form-control" required>
        </div>

        <button class="btn btn-success">Save Purchase</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const tbody = document.querySelector('#itemsTable tbody');
    const addRowBtn = document.getElementById('addRow');

    function addRow() {
        const tr = document.createElement('tr');
        tr.innerHTML = `
    <td>
        <select name="items[][product_id]" class="form-control" required>
            <option value="">-- Select Product --</option>
            @foreach(App\Models\Product::all() as $product)
                <option value="{{ $product->id }}">{{ $product->name }}</option>
            @endforeach
        </select>
    </td>
    <td><input type="number" step="0.01" name="items[][cost_price]" class="form-control cost"></td>
    <td><input type="number" name="items[][qty]" class="form-control qty"></td>
    <td><input type="text" class="form-control total" readonly></td>
    <td><button type="button" class="btn btn-sm btn-danger removeRow">X</button></td>
`;

        tbody.appendChild(tr);

        // Remove Row
        tr.querySelector('.removeRow').addEventListener('click', () => {
            tr.remove();
        });

        // Auto calculate total
        tr.querySelectorAll('.cost,.qty').forEach(inp => {
            inp.addEventListener('input', function(){
                const cost = parseFloat(tr.querySelector('.cost').value) || 0;
                const qty = parseInt(tr.querySelector('.qty').value) || 0;
                tr.querySelector('.total').value = (cost * qty).toFixed(2);
            });
        });
    }

    addRowBtn.addEventListener('click', addRow);

    // Add initial row on load
    addRow();
});
</script>
@endsection