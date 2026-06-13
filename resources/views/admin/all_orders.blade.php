@extends('layouts.admin_master')

@section('content')



<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-list-alt mr-1"></i> All Orders
    </div>

    <div class="card-body">

        <div class="text-right mb-3">
            <a href="{{ route('invoice.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> New Invoice
            </a>
            <a href="{{ url('pending-orders') }}" class="btn btn-sm btn-info">
                <i class="fas fa-clock"></i> Go to Pending
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Customer Name</th>
                        <th>Customer Phone</th>
                        <th>Order Items</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        <td>{{ $order->customer_name ?? 'N/A' }}</td>

                        <td>{{ $order->customer_phone ?? 'N/A' }}</td>

                        <td>
                            <ul style="margin:0;">
                                @foreach($order->orderItems as $item)
                                    <li>
                                        {{ $item->product->name ?? 'N/A' }}
                                        (SN: {{ $item->product_sn ?? 'N/A' }}) <br>
                                        Qty: {{ $item->quantity }} |
                                        Price: ${{ number_format($item->sale_price,2) }} |
                                        Total: ${{ number_format($item->total_price,2) }}
                                    </li>
                                @endforeach
                            </ul>
                        </td>

                        <td>
                            @if($order->order_status == '0')
                                <span class="badge badge-warning">Pending</span>
                            @else
                                <span class="badge badge-success">Delivered</span>
                            @endif
                        </td>

                        <td>
                            {{-- Create Invoice --}}
                            @if($order->order_status == '0')
                                <a href="{{ route('add.invoice', $order->id) }}"
                                   class="btn btn-sm btn-info mb-1">
                                    Create Invoice
                                </a>
                            @else
                                <button class="btn btn-sm btn-secondary mb-1" disabled>
                                    Invoiced
                                </button>
                            @endif

                            {{-- Edit (text kaliya) --}}
                            <a href="{{ route('orders.edit', $order->id) }}"
                               class="btn btn-sm btn-warning mb-1">
                                Edit
                            </a>

                            {{-- Delete (icon kaliya) --}}
                            <form action="{{ route('orders.destroy', $order->id) }}"
                                  method="POST"
                                  style="display:inline-block;"
                                  onsubmit="return confirm('Are you sure you want to delete this order?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css"/>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.1/css/buttons.bootstrap4.min.css"/>

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>

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
            { orderable: false, targets: [5] }
        ]
    });
});
</script>
@endsection
