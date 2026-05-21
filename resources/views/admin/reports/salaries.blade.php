@extends('layouts.admin_master')

@section('content')
<div class="container-fluid mt-4">

    <h2 class="text-center mb-4">💰 Salaries Report</h2>
    
<a href="{{ route('reports.salaries', ['type'=>'daily']) }}" class="btn btn-primary">Daily</a>
<a href="{{ route('reports.salaries', ['type'=>'weekly']) }}" class="btn btn-success">Weekly</a>
<a href="{{ route('reports.salaries', ['type'=>'monthly']) }}" class="btn btn-warning">Monthly</a>
    {{-- Filter Form --}}
    <form method="GET" action="{{ route('reports.salaries') }}" class="row gx-3 gy-2 align-items-end mb-4">

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

    {{-- Total Salaries Paid --}}
    <div class="alert alert-success text-center fw-bold mb-3">
        💵 Total Salaries Paid: ${{ number_format($totalPaid, 2) }}
    </div>

    {{-- Salaries Table --}}
    <div class="table-responsive">
        <table id="salariesTable" class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th class="text-center">ID</th>
                    <th>Employee</th>
                    <th class="text-end">Amount ($)</th>
                    <th class="d-none d-sm-table-cell">Month</th>
                    <th class="text-center">Date Paid</th>
                </tr>
            </thead>
            <tbody>
                @forelse($salaries as $sal)
                <tr>
                    <td class="text-center">{{ $sal->id }}</td>
                    <td>{{ $sal->employee->name ?? 'N/A' }}</td>
                    <td class="text-end">${{ number_format($sal->amount, 2) }}</td>
                    <td class="d-none d-sm-table-cell">{{ $sal->month ?? '-' }}</td>
                    <td class="text-center">{{ $sal->created_at->format('Y-m-d') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted p-3">
                        No salaries found for the selected range.
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
    $('#salariesTable').DataTable({
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
