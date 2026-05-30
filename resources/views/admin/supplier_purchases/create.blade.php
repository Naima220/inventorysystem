@extends('layouts.admin_master')
@section('content')
<div class="container-fluid py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="fas fa-cart-plus mr-2 text-primary"></i>Buy Existing Product</h3>
        <a href="{{ route('supplier-purchases.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Back to Purchases
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger shadow-sm">
            <h5 class="alert-heading"><i class="fas fa-exclamation-triangle mr-2"></i>Please fix these errors:</h5>
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-header bg-white">
            <h5 class="mb-0">Purchase Form</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('supplier-purchases.store') }}" method="POST">
                @csrf

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="font-weight-bold">Supplier Name <span class="text-danger">*</span></label>
                        <select name="supplier_name" class="form-control" required>
                            <option value="">-- Select Supplier --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->supplier_name }}">{{ $supplier->supplier_name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">If your supplier is not here, <a href="{{ route('add.supplier') }}">Add New Supplier</a> first.</small>
                    </div>
                </div>

                <div class="card bg-light mb-4 border-0">
                    <div class="card-body">
                        <h5 class="mb-3">Items Purchased</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-white bg-white" id="itemsTable">
                                <thead class="thead-dark text-center">
                                    <tr>
                                        <th style="width: 40%">Product</th>
                                        <th style="width: 20%">Cost Price ($)</th>
                                        <th style="width: 15%">Quantity</th>
                                        <th style="width: 15%">Total ($)</th>
                                        <th style="width: 10%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- rows dynamically added here --}}
                                </tbody>
                            </table>
                        </div>
                        <div class="text-right">
                            <button type="button" class="btn btn-sm btn-info font-weight-bold px-3" id="addRow">
                                <i class="fas fa-plus mr-1"></i> Add Another Item
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Discount Received ($)</label>
                            <input type="number" step="0.01" name="discount" class="form-control" value="0" min="0">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Amount Paid Now ($) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="paid" class="form-control" value="0" min="0" required>
                            <small class="text-muted">Any remaining balance will be recorded as Debt.</small>
                        </div>
                    </div>
                </div>

                <hr>
                <div class="text-right">
                    <button type="submit" class="btn btn-success btn-lg px-5">
                        <i class="fas fa-save mr-1"></i> Save Purchase & Update Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const tbody = document.querySelector('#itemsTable tbody');
    const addRowBtn = document.getElementById('addRow');
    
    // IMPORTANT: Counter used to generate unique index for PHP associative arrays
    let rowIndex = 0; 

    function addRow() {
        const tr = document.createElement('tr');
        tr.className = "text-center";
        tr.innerHTML = `
    <td class="text-left">
        <select name="items[${rowIndex}][product_id]" class="form-control" required>
            <option value="">-- Select Product --</option>
            @foreach(App\Models\Product::all() as $product)
                <option value="{{ $product->id }}">{{ $product->name }} (In Stock: {{ $product->stock }})</option>
            @endforeach
        </select>
    </td>
    <td><input type="number" step="0.01" min="0" name="items[${rowIndex}][cost_price]" class="form-control cost text-center" placeholder="0.00" required></td>
    <td><input type="number" min="1" name="items[${rowIndex}][qty]" class="form-control qty text-center" placeholder="1" required></td>
    <td><input type="text" class="form-control total text-center font-weight-bold text-success" value="0.00" readonly></td>
    <td>
        <button type="button" class="btn btn-sm btn-danger removeRow" title="Remove Item">
            <i class="fas fa-times"></i>
        </button>
    </td>
`;

        tbody.appendChild(tr);
        rowIndex++; // Increment the index for the next row!

        // Remove Row Event
        tr.querySelector('.removeRow').addEventListener('click', () => {
            // Prevent removing if it's the only row
            if (tbody.children.length > 1) {
                tr.remove();
            } else {
                alert("You must have at least one item in the purchase.");
            }
        });

        // Auto calculate total Event
        tr.querySelectorAll('.cost, .qty').forEach(inp => {
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