@extends('layouts.admin_master')

@section('content')
<div class="container-fluid mt-4">

    <h2 class="text-center mb-4">🧾 Invoices Report</h2>
<a href="{{ route('reports.invoices', ['type'=>'daily']) }}" class="btn btn-primary">Daily</a>
<a href="{{ route('reports.invoices', ['type'=>'weekly']) }}" class="btn btn-success">Weekly</a>
<a href="{{ route('reports.invoices', ['type'=>'monthly']) }}" class="btn btn-warning">Monthly</a>
    {{-- Filter Form --}}
    <form method="GET" class="row gx-3 gy-2 align-items-end mb-4">

        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <label class="small fw-bold">From Date</label>
            <input type="date" name="from_date" class="form-control" value="{{ $from ?? '' }}">
        </div>

        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <label class="small fw-bold">To Date</label>
            <input type="date" name="to_date" class="form-control" value="{{ $to ?? '' }}">
        </div>

        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-filter"></i> Filter
            </button>
        </div>

    </form>
 {{-- Grand Total --}}
    <div class="alert alert-info mt-4 text-center fs-5">
        💵 <strong>Grand Total:</strong> ${{ number_format($grandTotal, 2) }}
    </div>

    {{-- Invoices Table --}}
    <div class="table-responsive">
        <table id="invoicesTable" class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th class="text-center">ID</th>
                    <th>Customer</th>
                    <th class="d-none d-sm-table-cell">Phone</th>
                    <th class="text-end">Total Amount</th>
                    <th class="text-center">Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                <tr>
                    <td class="text-center">{{ $invoice->id }}</td>
                    <td>{{ $invoice->customer->name ?? $invoice->customer_name }}</td>
                    <td class="d-none d-sm-table-cell">{{ $invoice->customer->phone ?? $invoice->customer_phone }}</td>
                    <td class="text-end">${{ number_format($invoice->calculated_total,2) }}</td>
                    <td class="text-center">{{ $invoice->created_at->format('Y-m-d') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center p-4 text-muted">No invoices found in this range.</td>
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
    $('#invoicesTable').DataTable({
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
            searchPlaceholder: "Live Search..."
        }
    });
});
</script>
@endsection
