@extends('layouts.admin_master')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">All Shop Users</h1>
    </div>

    <div class="card shadow mb-4 content-card">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Registered Users Across All Shops</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Shop</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allUsers as $user)
                        <tr>
                            <td>{{ $user['shop_name'] }} <small class="text-muted">({{ $user['shop_id'] }})</small></td>
                            <td>{{ $user['name'] }}</td>
                            <td>{{ $user['email'] }}</td>
                            <td>
                                @php
                                    // Note: roles are stored in tenant DB, we'd need to handle this differently 
                                    // if we wanted to show roles properly here, but for now we show name/email.
                                @endphp
                                <span class="badge badge-info">User</span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#passwordModal{{ $user['shop_id'] }}{{ $user['id'] }}">
                                    <i class="fas fa-key"></i> Change Password
                                </button>

                                <!-- Password Modal -->
                                <div class="modal fade" id="passwordModal{{ $user['shop_id'] }}{{ $user['id'] }}" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="{{ route('superadmin.users.changePassword') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="shop_id" value="{{ $user['shop_id'] }}">
                                                <input type="hidden" name="user_id" value="{{ $user['id'] }}">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Change Password: {{ $user['email'] }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>New Password</label>
                                                        <input type="password" name="new_password" class="form-control" required minlength="6">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Update Password</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
</script>
@endsection
