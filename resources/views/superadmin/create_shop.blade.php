@extends('layouts.admin_master')

@section('content')

<div class="container mt-5" style="max-width:600px;">

    <h2>Add New Shop</h2>

    <form action="{{ route('superadmin.storeShop') }}" method="POST">
        @csrf

        <div class="form-group mb-3">
            <label>Shop Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="form-group mb-3">
            <label>Subdomain (e.g. 'shop1')</label>
            <input type="text" name="id" class="form-control" placeholder="Unique identifier" required>
        </div>

        <div class="form-group mb-3">
            <label>Owner Name</label>
            <input type="text" name="owner_name" class="form-control" required>
        </div>

        <div class="form-group mb-3">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control" required>
        </div>

        <div class="form-group mb-3">
            <label>Admin Email</label>
            <input type="email" name="admin_email" class="form-control" placeholder="Email for the shop admin" required>
        </div>

        <div class="form-group mb-3">
            <label>Admin Password</label>
            <input type="password" name="admin_password" class="form-control" placeholder="Password for the shop admin" required>
        </div>

        <div class="form-group mb-3">
            <label>Subscription Days</label>
            <input type="number" name="subscription_days" class="form-control" value="30" required>
        </div>

        <button type="submit" class="btn btn-success w-100">
            Create Shop
        </button>

    </form>

</div>

@endsection