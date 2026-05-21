@extends('layouts.admin_master')
@section('content')
<div class="container py-4">
    <h3>Add New Product & Purchase</h3>
    <form action="{{ route('supplier-purchases.storeNew') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Supplier Name</label>
            <input type="text" name="supplier_name" class="form-control" required>
        </div>

        <h5 class="mt-4">New Product Details</h5>
        <div class="form-group">
            <label>Product Name</label>
            <input type="text" name="product_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Sales Price</label>
            <input type="number" step="0.01" name="sales_price" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Cost Price</label>
            <input type="number" step="0.01" name="cost_price" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Quantity</label>
            <input type="number" name="qty" class="form-control" required>
        </div>

        <h5 class="mt-4">Purchase Details</h5>
        <div class="form-group">
            <label>Discount</label>
            <input type="number" step="0.01" name="discount" class="form-control">
        </div>
        <div class="form-group">
            <label>Paid</label>
            <input type="number" step="0.01" name="paid" class="form-control" required>
        </div>

        <button class="btn btn-success">Save New Purchase</button>
    </form>
</div>
@endsection