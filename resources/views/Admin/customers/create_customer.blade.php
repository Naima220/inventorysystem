@extends('layouts.admin_master')

@section('content')

<main class="py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">
                <div class="card shadow-sm border-0 rounded-lg mt-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="text-center mb-0">Add New Customer</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ url('/insert-customer') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-row">

                                {{-- Customer Name --}}
                                <div class="col-12 col-md-6 mb-3">
                                    <label class="small mb-1">Customer Name</label>
                                    <input class="form-control" name="customer_name" type="text" required>
                                </div>

                                {{-- Customer Email --}}
                                <div class="col-12 col-md-6 mb-3">
                                    <label class="small mb-1">Customer Email</label>
                                  <input class="form-control" name="email" type="email" >
                                </div>

                                {{-- Gender --}}
                                <div class="col-12 col-md-6 mb-3">
                                    <label class="small mb-1">Gender</label>
                                    <select class="form-control" name="gender" required>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>

                                {{-- Address --}}
                                <div class="col-12 col-md-6 mb-3">
                                    <label class="small mb-1">Address</label>
                                    <input class="form-control" name="address" type="text" required>
                                </div>

                                {{-- Phone --}}
                                <div class="col-12 mb-3">
                                    <label class="small mb-1">Phone</label>
                                    <input class="form-control" name="phone" type="text" required>
                                </div>

                            </div>

                            <button type="submit" class="btn btn-primary btn-block">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@endsection
