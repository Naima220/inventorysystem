@extends('layouts.admin_master')

@section('content')

<div class="container mt-4" style="max-width:600px;">

    <h2 class="mb-4">Edit Shop</h2>

    <form action="{{ route('superadmin.updateShop', $shop->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group mb-3">
            <label>Shop Name</label>
            <input type="text" name="name" class="form-control" value="{{ $shop->name }}" required>
        </div>

        <div class="form-group mb-3">
            <label>Owner Name</label>
            <input type="text" name="owner_name" class="form-control" value="{{ $shop->owner_name }}" required>
        </div>

        <div class="form-group mb-3">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control" value="{{ $shop->phone }}" required>
        </div>

        <div class="form-group mb-3">
            <label>Subscription Days</label>
            <input type="number" name="subscription_days" class="form-control" value="" placeholder="Enter new days" required>
        </div>

        <div class="form-group mb-3">
            <label>Admin Email</label>
            <input type="email" name="admin_email" class="form-control" value="{{ $shop->admin_email }}" required>
        </div>

        <div class="form-group mb-3">
            <label>Admin Password</label>
            <input type="password" name="admin_password" class="form-control" placeholder="Leave blank to keep current password">
        </div>

        <button type="submit" class="btn btn-success w-100">
            Update Shop
        </button>

    </form>

</div>

@endsection