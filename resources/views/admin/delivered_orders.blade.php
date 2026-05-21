@extends('layouts.admin_master')

@section('content')

<style>
/* ============ MOBILE RESPONSIVE ============ */
@media (max-width: 768px) {

    #dataTable thead {
        display: none;
    }

    #dataTable,
    #dataTable tbody,
    #dataTable tr,
    #dataTable td {
        display: block;
        width: 100%;
    }

    #dataTable tr {
        background: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 15px;
    }

    #dataTable td {
        border: none;
        padding: 6px 0;
        font-size: 14px;
    }

    #dataTable td::before {
        font-weight: bold;
        display: block;
        color: #555;
    }

    #dataTable td:nth-of-type(1)::before { content: "Order ID"; }
    #dataTable td:nth-of-type(2)::before { content: "Customer Name"; }
    #dataTable td:nth-of-type(3)::before { content: "Customer Phone"; }
    #dataTable td:nth-of-type(4)::before { content: "Product Serial"; }
    #dataTable td:nth-of-type(5)::before { content: "Product Name"; }
    #dataTable td:nth-of-type(6)::before { content: "Quantity"; }
    #dataTable td:nth-of-type(7)::before { content: "Status"; }

    #dataTable td .btn {
        width: 100%;
    }
}
</style>

<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <i class="fas fa-check-circle mr-1"></i> Delivered Orders
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%">
                <thead class="thead-dark">
                    <tr>
                        <th>Order ID</th>
                        <th>Customer Name</th>
                        <th>Customer Phone</th>
                        <th>Product Serial</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($orders as $order)
                        @foreach($order->orderItems as $item)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->customer->customer_name ?? 'N/A' }}</td>
                            <td>{{ $order->customer->phone ?? 'N/A' }}</td>
                            <td>{{ $item->product->product_sn ?? 'N/A' }}</td>
                            <td>{{ $item->product->name ?? 'N/A' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>
                                <span class="btn btn-sm btn-success">
                                    Delivered
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.1/css/buttons.bootstrap4.min.css"/>

<script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.colVis.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<script>
$(document).ready(function () {
    $('#dataTable').DataTable({
        dom: 'Bfrtip',
        buttons: ['copy', 'excel', 'pdf', 'print', 'colvis'],
        columnDefs: [
            { orderable: false, targets: [6] }
        ]
    });
});
</script>
@endsection
