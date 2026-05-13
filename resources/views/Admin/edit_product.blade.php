@extends('layouts.admin_master')

@section('content')

<main>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card shadow-lg border-0 rounded-lg mt-5">
                    <div class="card-header">
                        <h3 class="text-center font-weight-light my-4">Update Product</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('update.product', $product->id) }}">
                            @csrf
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small mb-1">Product Serial</label>
                                        <input class="form-control py-4" name="product_sn" type="text" value="{{ $product->product_sn }}" required />
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small mb-1">Product Name</label>
                                        <input class="form-control py-4" name="name" type="text" value="{{ $product->name }}" required />
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small mb-1">Category</label>
                                        <input class="form-control py-4" name="category" type="text" value="{{ $product->category }}" required />
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small mb-1">Stock</label>
                                        <input class="form-control py-4" name="stock" type="number" value="{{ $product->stock }}" required />
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small mb-1">Buy Price (per Unit)</label>
                                        <input class="form-control py-4" name="unit_price" type="number" step="0.01" value="{{ $product->unit_price }}" required />
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small mb-1">Sale Price (per Unit)</label>
                                        <input class="form-control py-4" name="sales_unit_price" type="number" step="0.01" value="{{ $product->sales_unit_price }}" required />
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-4 mb-0">
                                <button class="btn btn-primary btn-block">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@endsection
