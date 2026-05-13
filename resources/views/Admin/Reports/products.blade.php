@extends('layouts.admin_master')

@section('content')
<div class="container-fluid mt-4">

    <h2 class="text-center mb-4">📦 Products Report</h2>
<a href="{{ route('reports.products', ['type'=>'daily']) }}" class="btn btn-primary">Daily</a>
<a href="{{ route('reports.products', ['type'=>'weekly']) }}" class="btn btn-success">Weekly</a>
<a href="{{ route('reports.products', ['type'=>'monthly']) }}" class="btn btn-warning">Monthly</a>

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('reports.products') }}" class="row gx-3 gy-2 align-items-end mb-4">
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

    {{-- Products Table --}}
    <div class="table-responsive">
        <table id="productsTable" class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Serial</th>
                    <th>Name</th>
                    <th class="d-none d-sm-table-cell">Category</th>
                    <th class="text-end">Stock</th>
                    <th class="text-end">Unit Price ($)</th>
                    <th class="text-end">Sale Price ($)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $p)
                <tr>
                    <td>{{ $p->product_sn }}</td>
                    <td>{{ $p->name }}</td>
                    <td class="d-none d-sm-table-cell">{{ $p->category }}</td>
                    <td class="text-end">{{ $p->stock }}</td>
                    <td class="text-end">${{ number_format($p->unit_price, 2) }}</td>
                    <td class="text-end">${{ number_format($p->sales_unit_price, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center p-3">No products found.</td>
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
    $('#productsTable').DataTable({
        responsive: true,
        dom: 'Bfrtip', // show buttons + search
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
            searchPlaceholder: "Live Search..."
        },
        columnDefs: [
            { targets: [2], className: 'd-none d-sm-table-cell' } // Category hide on xs
        ]
    });
});
</script>
@endsection
