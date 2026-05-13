@extends('layouts.admin_master')

@section('content')

<main>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card shadow-lg border-0 rounded-lg mt-5">
                    <div class="card-header"><h3 class="text-center font-weight-light my-4">Purchase Existing Product
                        <div style="text-align: right;">
                            <a href="{{url('all-product')}}" class="btn btn-sm btn-primary" style="padding: 6px; margin: 5px;">
                                <i class="fas fa-arrow-left"></i> Back to Stock <i class="fas fa-box"></i>
                            </a>
                        </div>
                    </h3></div>
                    <div class="card-body">
                        <!-- Display Validation Errors -->
                        <!-- @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif -->
                        <form method="POST" action="{{ url('/insert-purchase-products') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small mb-1" for="inputCode">Product Serial</label>
                                        <input class="form-control py-4" id="inputSN" name="sn" type="text" value="{{ $product->product_sn }}" readonly />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small mb-1" for="inputName">Product Name</label>
                                        <input class="form-control py-4" id="inputName" name="name" type="text" value="{{ $product->name }}" readonly />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small mb-1" for="inputCategory">Category</label>
                                        <input class="form-control py-4" id="inputCategory" name="category" type="text" value="{{ $product->category }}" readonly />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small mb-1" for="inputStock">Products in Stock</label>
                                        <input class="form-control py-4" id="inputStock" name="stock" type="text" value="{{ $product->stock }}" readonly />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small mb-1" for="inputPurchase">Add More Product in Stock</label>
                                        <input class="form-control py-4" id="inputPurchase" name="purchase" type="text" required />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mt-4 mb-0">
                                <button class="btn btn-primary btn-block" type="submit">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@endsection
