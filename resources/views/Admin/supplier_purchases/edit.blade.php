@extends('layouts.admin_master')
@section('content')
<div class="container py-4">
<h3>Edit Purchase #{{ $purchase->id }}</h3>
<form action="{{ route('supplier-purchases.update',$purchase->id) }}" method="POST">
@csrf @method('PUT')
<div class="form-group">
<label>Supplier Name</label>
<input type="text" name="supplier_name" class="form-control" value="{{ $purchase->supplier_name }}" required>
</div>
<div class="form-group">
<label>Discount</label>
<input type="number" step="0.01" name="discount" class="form-control" value="{{ $purchase->discount }}">
</div>
<div class="form-group">
<label>Paid</label>
<input type="number" step="0.01" name="paid" class="form-control" value="{{ $purchase->paid }}">
</div>
<button class="btn btn-success">Update Purchase</button>
</form>
</div>
@endsection