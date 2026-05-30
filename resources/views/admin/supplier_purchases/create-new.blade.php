@extends('layouts.admin_master')
@section('content')
<div class="container-fluid py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="fas fa-plus-circle mr-2 text-success"></i>Add New Product & Purchase</h3>
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

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow border-0 rounded-lg">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="mb-0 text-primary font-weight-bold">Purchase Form</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('supplier-purchases.storeNew') }}" method="POST">
                        @csrf
                        
                        <!-- Supplier Section -->
                        <div class="bg-light p-3 rounded mb-4 border border-left-primary">
                            <h6 class="font-weight-bold mb-3 text-dark"><i class="fas fa-industry mr-2"></i>Supplier Details</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label class="small font-weight-bold text-muted">Supplier Name <span class="text-danger">*</span></label>
                                        <input type="text" name="supplier_name" class="form-control" placeholder="Enter supplier name" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Product Section -->
                        <div class="bg-light p-3 rounded mb-4 border border-left-success">
                            <h6 class="font-weight-bold mb-3 text-dark"><i class="fas fa-box-open mr-2"></i>New Product Information</h6>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="small font-weight-bold text-muted">Product Name <span class="text-danger">*</span></label>
                                        <input type="text" name="product_name" class="form-control" placeholder="E.g. Men's Leather Shoes" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="small font-weight-bold text-muted">Cost Price (Xaddiga Lagu Keenay) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                                            <input type="number" step="0.01" min="0" name="cost_price" class="form-control" placeholder="0.00" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="small font-weight-bold text-muted">Sales Price (Qiimaha Iibka) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                                            <input type="number" step="0.01" min="0" name="sales_price" class="form-control" placeholder="0.00" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="small font-weight-bold text-muted">Quantity (Tirada) <span class="text-danger">*</span></label>
                                        <input type="number" min="1" name="qty" class="form-control" placeholder="E.g. 10" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Section -->
                        <div class="bg-light p-3 rounded mb-4 border border-left-warning">
                            <h6 class="font-weight-bold mb-3 text-dark"><i class="fas fa-money-bill-wave mr-2"></i>Payment Details</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small font-weight-bold text-muted">Discount Received ($)</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                                            <input type="number" step="0.01" min="0" name="discount" class="form-control" value="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small font-weight-bold text-muted">Amount Paid Now ($) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                                            <input type="number" step="0.01" min="0" name="paid" class="form-control" value="0" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit -->
                        <hr>
                        <div class="text-right">
                            <button type="submit" class="btn btn-success btn-lg px-4 font-weight-bold">
                                <i class="fas fa-save mr-2"></i>Save Product & Purchase
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection