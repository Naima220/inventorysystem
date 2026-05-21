@extends('layouts.admin_master')

@section('content')

<form method="GET" action="{{ route('activity.logs') }}" class="mb-3">
    <div style="display:flex; gap:10px;">

        {{-- Search --}}
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search logs..."
               class="form-control">

        <button type="submit" class="btn btn-primary">Filter</button>
    </div>
</form>

<div class="container">
    <h3 class="mb-3">Activity Logs</h3>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>User</th>
                <th>Action</th>
                <th>Description</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
            <tr>
                <td>{{ $log->user->name ?? 'N/A' }}</td>
                <td>{{ $log->action }}</td>
                <td>{{ $log->description }}</td>
                <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">No activity logs found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination bannaanka table-ka --}}
    <div class="d-flex justify-content-center">
        {{ $logs->links() }}
    </div>

</div>

@endsection