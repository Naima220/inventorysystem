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

    #dataTable td:nth-of-type(1)::before { content: "#"; }
    #dataTable td:nth-of-type(2)::before { content: "Customer Name"; }
    #dataTable td:nth-of-type(3)::before { content: "Customer Phone"; }
    #dataTable td:nth-of-type(4)::before { content: "Order Items"; }
    #dataTable td:nth-of-type(5)::before { content: "Status"; }
    #dataTable td:nth-of-type(6)::before { content: "Action"; }

    #dataTable td ul {
        padding-left: 15px;
    }

    #dataTable td .btn,
    #dataTable td form button {
        width: 100%;
        margin-bottom: 6px;
    }
}
</style>

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-clock mr-1"></i> Pending Orders
    </div>

    <div class="card-body">

        <div class="text-right mb-3">
            <a href="{{ url('all-orders') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-list"></i> Back to All Orders
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
                    @if($order->order_status == 0)
                    <tr>
                        <td>{{ $order->id }}</td>

                        <td>{{ $order->customer_name ?? $order->customer->customer_name }}</td>

                        <td>{{ $order->customer_phone ?? $order->customer->phone }}</td>

                        <td>
                            <ul style="margin:0;">
                                @foreach($order->orderItems as $item)
                                    <li>
                                        {{ $item->product->name ?? 'N/A' }}
                                        (SN: {{ $item->product_sn ?? 'N/A' }})
                                        <br>
                                        Qty: {{ $item->quantity }}
                                    </li>
                                @endforeach
                            </ul>
                        </td>

                        <td>
                            <form action="{{ route('update.order.status', $order->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="order_status" value="1">
                                <button type="submit" class="btn btn-sm btn-warning">
                                    Status Pending
                                </button>
                            </form>
                        </td>

                        <td>
                            <a href="{{ url('add-invoice/'.$order->id) }}"
                               class="btn btn-sm btn-info">
                                Create Invoice
                            </a>
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>
</div>
@endsection
