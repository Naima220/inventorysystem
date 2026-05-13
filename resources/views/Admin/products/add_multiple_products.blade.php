@extends('layouts.admin_master')

@section('content')
<div class="container mt-5">
    <div class="card shadow border-0">
        <div class="card-header">
            <h4>Add Multiple Products</h4>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('products.multiple.store') }}" method="POST">
                @csrf
                <div id="productsContainer">
                    <div class="product-card border rounded p-3 mb-3">
                        <div class="row g-2 align-items-center">

                            <div class="col-12 col-md-2">
                                <input type="text" name="products[0][product_sn]" class="form-control" placeholder="Product SN" required>
                            </div>

                            <div class="col-12 col-md-2">
                                <input type="text" name="products[0][name]" class="form-control" placeholder="Name" required>
                            </div>

                            <div class="col-12 col-md-2">
                                <input type="text" name="products[0][category]" class="form-control" placeholder="Category" required>
                            </div>

                            <!-- ✅ STOCK: md-2 si uu u shaqeeyo tablet -->
                            <div class="col-12 col-md-2">
                                <input type="number" name="products[0][stock]" class="form-control" placeholder="Stock" min="0" required>
                            </div>

                            <div class="col-12 col-md-2">
                                <input type="text" name="products[0][unit_price]" class="form-control" placeholder="Unit Price" required>
                            </div>

                            <div class="col-12 col-md-2">
                                <input type="text" name="products[0][sales_unit_price]" class="form-control" placeholder="Sales Price" required>
                            </div>

                            <!-- ✅ REMOVE BUTTON: md-1 + nowrap -->
                            <div class="col-12 col-md-2">
                                <button type="button"
                                    class="btn btn-danger remove-row w-100 text-nowrap">
                                    Remove
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-success mb-3" id="addRow">+ Add Product</button>
                <button type="submit" class="btn btn-primary btn-block">Submit Products</button>
            </form>
        </div>
    </div>
</div>

<script>
let rowIdx = 1;

document.getElementById('addRow').addEventListener('click', function () {
    let container = document.getElementById('productsContainer');
    let newCard = container.querySelector('.product-card').cloneNode(true);
    let inputs = newCard.querySelectorAll('input');

    inputs.forEach(function (input) {
        let name = input.getAttribute('name');
        name = name.replace(/\d+/, rowIdx);
        input.setAttribute('name', name);
        input.value = '';
    });

    container.appendChild(newCard);
    rowIdx++;
});

document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-row')) {
        let cards = document.querySelectorAll('.product-card');
        if (cards.length > 1) {
            e.target.closest('.product-card').remove();
        }
    }
});
</script>
@endsection
