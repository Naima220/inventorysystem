@extends('layouts.admin_master')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-sm-12">
            <div class="card shadow border-0 mt-4">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <h5 class="mb-0">Edit Employee</h5>
                    <a href="{{ route('all.employees') }}" class="btn btn-sm btn-primary mt-2 mt-md-0">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>

                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('employees.update',$employee->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control"
                                       placeholder="Employee Name"
                                       value="{{ old('name',$employee->name) }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control"
                                       placeholder="example@email.com"
                                       value="{{ old('email',$employee->email) }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Phone</label>
                                <input type="text" name="phone" class="form-control"
                                       placeholder="2526xxxxxxx"
                                       value="{{ old('phone',$employee->phone) }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Address</label>
                                <input type="text" name="address" class="form-control"
                                       placeholder="Employee Address"
                                       value="{{ old('address',$employee->address) }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Position</label>
                                <input type="text" name="position" class="form-control"
                                       placeholder="Job Position"
                                       value="{{ old('position',$employee->position) }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Hire Date</label>
                                <input type="date" name="hire_date" class="form-control"
                                       value="{{ old('hire_date',$employee->hire_date) }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Salary</label>
                                <input type="number" name="salary" step="0.01" class="form-control"
                                       placeholder="Salary Amount"
                                       value="{{ old('salary',$employee->salary) }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="Active" {{ $employee->status=='Active'?'selected':'' }}>Active</option>
                                    <option value="Inactive" {{ $employee->status=='Inactive'?'selected':'' }}>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <button class="btn btn-success btn-block mt-3">
                            Update Employee
                        </button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
