@extends('layouts.admin_master')

@section('content')
<div class="container-fluid mt-4">

    <h2 class="text-center mb-4">📦 Orders Report</h2>
    <div class="mb-3">
    <a href="{{ route('reports.orders', ['type' => 'daily']) }}" class="btn btn-primary">Daily</a>
    <a href="{{ route('reports.orders', ['type' => 'weekly']) }}" class="btn btn-success">Weekly</a>
    <a href="{{ route('reports.orders', ['type' => 'monthly']) }}" class="btn btn-warning">Monthly</a>
</div>

    {{-- Filter Form --}}
    <form method="GET" class="row gx-3 gy-2 align-items-end mb-3">
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <label class="small fw-bold">From Date</label>
            <input type="date" name="from_date" class="form-control" value="{{ $from }}">
        </div>

        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <label class="small fw-bold">To Date</label>
            <input type="date" name="to_date" class="form-control" value="{{ $to }}">
        </div>

        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
            <button class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    {{-- Grand Total --}}
    <div class="alert alert-info text-center fw-bold">
        📊 Grand Total Amount: ${{ number_format($grandTotal, 2) }}
    </div>

    {{-- Orders Table --}}
    <div class="table-responsive">
        <table id="ordersTable" class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th class="text-center">ID</th>
                    <th>Customer</th>
                     <th class="d-none d-sm-table-cell">Phone</th>
                    <th class="d-none d-sm-table-cell">Status</th>
                    <th class="text-center">Date</th>
                    <th class="text-end">Order Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td class="text-center">{{ $order->id }}</td>
                    <td>{{ $order->customer->name ?? $order->customer_name }}</td>
                    <td class="d-none d-sm-table-cell">{{ $order->customer->phone ?? $order->customer_phone }}</td>
                    <td class="d-none d-sm-table-cell">
                         
                        @if($order->order_status == 1)
                            <span class="badge bg-success">Delivered</span>
                        @else
                            <span class="badge bg-warning text-dark">Pending</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $order->created_at->format('Y-m-d') }}</td>
                    <td class="text-end">${{ number_format($order->calculated_total, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted p-3">No Orders Found</td>
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
    $('#ordersTable').DataTable({
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
