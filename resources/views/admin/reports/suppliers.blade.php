@extends('layouts.admin_master')

@section('content')
<div class="container-fluid mt-4">

    <h2 class="text-center mb-4">📊 Suppliers Report</h2>

<a href="{{ route('reports.suppliers', ['type'=>'daily']) }}" class="btn btn-primary">Daily</a>
<a href="{{ route('reports.suppliers', ['type'=>'weekly']) }}" class="btn btn-success">Weekly</a>
<a href="{{ route('reports.suppliers', ['type'=>'monthly']) }}" class="btn btn-warning">Monthly</a>

    {{-- Filter Form --}}
    <form method="GET" class="row gx-3 gy-2 align-items-end mb-4">

        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <label class="small fw-bold">From Date</label>
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control">
        </div>

        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <label class="small fw-bold">To Date</label>
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control">
        </div>

        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
            <button class="btn btn-primary w-100">Filter</button>
        </div>

    </form>

    {{-- Suppliers Table --}}
    <div class="table-responsive">
        <table id="suppliersTable" class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th class="text-center">#</th>
                    <th>Supplier Name</th>
                    <th class="d-none d-sm-table-cell">Email</th>
                    <th class="d-none d-md-table-cell">Gender</th>
                    <th class="text-end">Phone</th>
                    <th class="d-none d-sm-table-cell">Address</th>
                    <th class="text-center">Created At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $s)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $s->supplier_name }}</td>
                    <td class="d-none d-sm-table-cell">{{ $s->email }}</td>
                    <td class="d-none d-md-table-cell">{{ $s->gender }}</td>
                    <td class="text-end">{{ $s->phone }}</td>
                    <td class="d-none d-sm-table-cell">{{ $s->address }}</td>
                    <td class="text-center">{{ $s->created_at->format('Y-m-d') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted p-3">
                        No suppliers found in this date range.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#suppliersTable').DataTable({
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copyHtml5',
                text: '<i class="fas fa-copy"></i> Copy',
                className: 'btn btn-sm btn-secondary'
            },
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-sm btn-success'
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-sm btn-danger'
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Print',
                className: 'btn btn-sm btn-info'
            }
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "🔍 Live Search..."
        }
    });
});
</script>
@endsection
