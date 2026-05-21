@extends('layouts.admin_master')

@section('content')

<main class="py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">
                <div class="card shadow-sm border-0 rounded-lg mt-4">

                    <div class="card-header bg-info text-white">
                        <h4 class="text-center mb-0">Edit Customer</h4>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('customers.update', $customer->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="form-row">

                                {{-- Customer Name --}}
                                <div class="col-12 col-md-6 mb-3">
                                    <label class="small mb-1">Customer Name</label>
                                    <input type="text" class="form-control" name="customer_name" value="{{ old('customer_name', $customer->customer_name) }}" required>
                                </div>

                                {{-- Email --}}
                                <div class="col-12 col-md-6 mb-3">
                                    <label class="small mb-1">Email</label>
                                    <input type="email" class="form-control" name="email" value="{{ old('email', $customer->email) }}" required>
                                </div>

                                {{-- Phone --}}
                                <div class="col-12 col-md-6 mb-3">
                                    <label class="small mb-1">Phone</label>
                                    <input type="text" class="form-control" name="phone" value="{{ old('phone', $customer->phone) }}">
                                </div>

                                {{-- Gender --}}
                                <div class="col-12 col-md-6 mb-3">
                                    <label class="small mb-1">Gender</label>
                                    <select name="gender" class="form-control" required>
                                        <option value="Male" {{ (old('gender', $customer->gender) == 'Male') ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ (old('gender', $customer->gender) == 'Female') ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>

                                {{-- Address --}}
                                <div class="col-12 mb-3">
                                    <label class="small mb-1">Address</label>
                                    <textarea class="form-control" name="address" rows="2">{{ old('address', $customer->address) }}</textarea>
                                </div>

                            </div>

                            <button class="btn btn-info btn-block">Update Customer</button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@endsection
