@extends('layouts.admin_master')

@section('content')
<div class="container-fluid mt-4">

    <h2 class="text-center mb-4">👥 Customers Report</h2>
<a href="{{ route('reports.customers', ['type'=>'daily']) }}" class="btn btn-primary">Daily</a>
<a href="{{ route('reports.customers', ['type'=>'weekly']) }}" class="btn btn-success">Weekly</a>
<a href="{{ route('reports.customers', ['type'=>'monthly']) }}" class="btn btn-warning">Monthly</a>

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('reports.customers') }}" class="row gx-3 gy-2 align-items-end mb-4">

        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <label class="small fw-bold">From Date</label>
            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
        </div>

        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <label class="small fw-bold">To Date</label>
            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
        </div>

        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
            <button class="btn btn-primary w-100">Filter</button>
        </div>

    </form>


    {{-- Customers Table --}}
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th class="text-center">ID</th>
                    <th>Name</th>
                    <th class="d-none d-sm-table-cell">Email</th>
                    <th>Phone</th>
                    <th class="d-none d-md-table-cell">Gender</th>
                    <th class="d-none d-md-table-cell">Address</th>
                    <th class="text-center">Date Added</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr>
                    <td class="text-center">{{ $customer->id }}</td>
                    <td>{{ $customer->customer_name }}</td>
                    <td class="d-none d-sm-table-cell">{{ $customer->email }}</td>
                    <td>{{ $customer->phone }}</td>
                    <td class="d-none d-md-table-cell">{{ ucfirst($customer->gender) }}</td>
                    <td class="d-none d-md-table-cell">{{ $customer->address }}</td>
                    <td class="text-center">{{ $customer->created_at->format('Y-m-d') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center p-3">No customers found for the selected range.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
