@extends('layouts.admin_master')

@section('content')
<div class="container-fluid mt-4">

    <h2 class="text-center mb-4">💳 Payments Report</h2>

<a href="{{ route('reports.payments', ['type'=>'daily']) }}" class="btn btn-primary">Daily</a>
<a href="{{ route('reports.payments', ['type'=>'weekly']) }}" class="btn btn-success">Weekly</a>
<a href="{{ route('reports.payments', ['type'=>'monthly']) }}" class="btn btn-warning">Monthly</a>
    {{-- Filter Form --}}
    <form method="GET" action="{{ route('reports.payments') }}" class="row gx-3 gy-2 align-items-end mb-4">

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

    {{-- Total Paid --}}
    <div class="alert alert-info text-center fw-bold mb-3">
        💰 Total Payments Received: ${{ number_format($totalPaid, 2) }}
    </div>

    {{-- Payments Table --}}
    <div class="table-responsive">
        <table id="paymentsTable" class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th class="text-center">ID</th>
                    <th>Customer</th>
                     <th class="d-none d-sm-table-cell">Phone</th>
                    <th class="text-end">Paid Amount ($)</th>
                    <th class="d-none d-sm-table-cell">Payment Method</th>
                    <th class="text-center">Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $pay)
                <tr>
                    <td class="text-center">{{ $pay->id }}</td>
                    <td>{{ $pay->customer->customer_name ?? 'N/A' }}</td>
                    <td class="d-none d-sm-table-cell">{{ $pay->customer->phone ?? $pay->customer_phone }}</td>
                    <td class="text-end">${{ number_format($pay->paid, 2) }}</td>
                    <td class="d-none d-sm-table-cell">{{ $pay->method ?? 'Cash' }}</td>
                    <td class="text-center">{{ $pay->created_at->format('Y-m-d') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted p-3">
                        No payments found for the selected range.
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
    $('#paymentsTable').DataTable({
        responsive: true,
        dom: 'Bfrtip', // show buttons & search
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
        columnDefs: [
            { orderable: false, targets: [] }
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Live Search..."
        }
    });
});
</script>
@endsection
