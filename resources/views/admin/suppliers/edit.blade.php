@extends('layouts.admin_master')

@section('content')

<style>
/* ========= MOBILE RESPONSIVE EDIT FORM ========= */
@media (max-width: 768px) {

    .card {
        margin-top: 20px;
        border-radius: 12px;
    }

    .card-header h1 {
        font-size: 20px;
        line-height: 1.4;
        text-align: center;
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
        font-size: 15px;
        padding: 10px;
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
                        <h1 class="font-weight-light my-4">
                            <b>Edit Supplier</b>
                        </h1>

                        <div class="text-right">
                            <a href="{{ route('all.suppliers') }}"
                               class="btn btn-sm btn-primary">
                                <i class="fas fa-arrow-left"></i>
                                Back to Suppliers
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <form method="POST"
                              action="{{ route('suppliers.update', $supplier->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small mb-1">Supplier Name</label>
                                        <input class="form-control py-4"
                                               name="supplier_name"
                                               value="{{ old('supplier_name', $supplier->supplier_name) }}"
                                               required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small mb-1">Supplier Email</label>
                                        <input class="form-control py-4"
                                               name="email"
                                               type="email"
                                               value="{{ old('email', $supplier->email) }}"
                                               required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small mb-1">Gender</label>
                                        <select class="form-control py-4"
                                                name="gender" required>
                                            <option value="Male" {{ $supplier->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                            <option value="Female" {{ $supplier->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small mb-1">Address</label>
                                        <input class="form-control py-4"
                                               name="address"
                                               value="{{ old('address', $supplier->address) }}"
                                               required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small mb-1">Phone</label>
                                        <input class="form-control py-4"
                                               name="phone"
                                               value="{{ old('phone', $supplier->phone) }}"
                                               required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-4 mb-0">
                                <button class="btn btn-primary btn-block">
                                    Update Supplier
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
