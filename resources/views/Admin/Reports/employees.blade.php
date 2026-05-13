@extends('layouts.admin_master')

@section('content')
<div class="container-fluid mt-4">

    <h2 class="text-center mb-4">👩‍💼 Employees Report</h2>

<a href="{{ route('reports.employees', ['type'=>'daily']) }}" class="btn btn-primary">Daily</a>
<a href="{{ route('reports.employees', ['type'=>'weekly']) }}" class="btn btn-success">Weekly</a>
<a href="{{ route('reports.employees', ['type'=>'monthly']) }}" class="btn btn-warning">Monthly</a>

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('reports.employees') }}" class="row gx-3 gy-2 align-items-end mb-4">

        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <label class="small fw-bold">From Hire Date</label>
            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
        </div>

        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <label class="small fw-bold">To Hire Date</label>
            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
        </div>

        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
            <button class="btn btn-primary w-100">Filter</button>
        </div>

    </form>

    {{-- Employees Table --}}
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th class="text-center">ID</th>
                    <th>Name</th>
                    <th class="d-none d-sm-table-cell">Phone</th>
                    <th class="text-center">Hire Date</th>
                    <th class="text-end">Salary</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $emp)
                <tr>
                    <td class="text-center">{{ $emp->id }}</td>
                    <td>{{ $emp->name }}</td>
                    <td class="d-none d-sm-table-cell">{{ $emp->phone }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($emp->hire_date)->format('Y-m-d') }}</td>
                    <td class="text-end">${{ number_format($emp->salary, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center p-3">No employees found for this range.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
