@extends('layouts.admin_master')

@section('content')

<style>
/* ========= MOBILE RESPONSIVE SUPPLIER FORM ========= */
@media (max-width: 768px) {

    .card {
        margin-top: 20px !important;
        border-radius: 12px;
    }

    .card-header h1 {
        font-size: 20px;
        text-align: center;
        line-height: 1.4;
    }

    .card-header a {
        display: block;
        width: 100%;
        margin-top: 10px;
        text-align: center;
    }

    .form-row .col-md-6 {
        width: 100%;
        max-width: 100%;
    }

    .form-control {
        padding: 10px !important;
        font-size: 15px;
    }

    label.small {
        font-size: 14px;
        font-weight: 600;
    }

    .btn-block {
        padding: 12px;
        font-size: 16px;
        border-radius: 8px;
    }
}
</style>

<main>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card shadow-lg border-0 rounded-lg mt-5">

                    <div class="card-header">
                        <h1 class="text-center font-weight-light my-4">
                            <b>Add New Supplier</b>
                        </h1>

                        <div class="text-right">
                            <a href="{{ url('/all-suppliers') }}"
                               class="btn btn-sm btn-primary"
                               style="padding: 5px; margin: 5px;">
                                <i class="fas fa-arrow-left"></i>
                                Back to Suppliers
                                <i class="fas fa-truck"></i>
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ url('/insert-supplier') }}">
                            @csrf

                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small mb-1">Supplier Name</label>
                                        <input class="form-control py-4"
                                               name="supplier_name"
                                               type="text" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small mb-1">Supplier Email</label>
                                        <input class="form-control py-4"
                                               name="email"
                                               type="email" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small mb-1">Gender</label>
                                        <select class="form-control py-4"
                                                name="gender" required>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small mb-1">Address</label>
                                        <input class="form-control py-4"
                                               name="address"
                                               type="text" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small mb-1">Phone</label>
                                        <input class="form-control py-4"
                                               name="phone"
                                               type="text" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-4 mb-0">
                                <button class="btn btn-primary btn-block">
                                    Submit
                                </button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</main>

@endsection
