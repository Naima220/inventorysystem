@extends('layouts.admin_master')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-12">

            <div class="card shadow rounded-3">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-users"></i> Users List
                    </h4>
                </div>

                <div class="card-body">

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td class="text-break">{{ $user->email }}</td>

                                        {{-- Role Badge --}}
                                        <td class="text-center">
                                            @php
                                                $roleName = $user->roles->pluck('name')->first();
                                                $badgeClass = match($roleName) {
                                                    'super_admin' => 'badge-danger',
                                                    'Admin'       => 'badge-success',
                                                    'User'        => 'badge-secondary',
                                                    default       => 'badge-dark',
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">
                                                {{ ucfirst($roleName) }}
                                            </span>
                                        </td>

                                        <td class="text-center">{{ $user->created_at->format('d-m-Y') }}</td>

                                        {{-- Actions --}}
                                        {{-- Actions --}}
                                        <td class="text-center">
                                            <div class="d-flex flex-column flex-md-row justify-content-center gap-2">
                                                @php
                                                    $currentUser = auth()->user();
                                                    $targetRole = strtolower($user->roles->pluck('name')->first() ?? '');
                                                    
                                                    // Admins can ONLY be edited
                                                    // Users can be edited AND deleted
                                                    $isTargetAdmin = ($targetRole === 'admin' || $targetRole === 'super_admin');
                                                    $isTargetUser = ($targetRole === 'user');
                                                    
                                                    $canEdit = $currentUser->hasAnyRole(['super_admin', 'Admin', 'admin']);
                                                    $canDelete = ($canEdit && $isTargetUser);
                                                @endphp

                                                @if($canEdit)
                                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                @endif

                                                @if($canDelete)
                                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure?');" class="d-inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            No users found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection