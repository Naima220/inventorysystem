@extends('layouts.admin_master')

@section('content')
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-table mr-1"></i>
        Available Products
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th>Serial</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Stock</th>
                        <th>Unit Price</th>
                        <th>Sales Unit Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                
                <tbody>
                    @foreach($products as $row)
                    <tr>
                        <td>{{ $row->product_sn }}</td>
                        <td>{{ $row->name }}</td>
                        <td>{{ $row->category }}</td>
                        
                        @if($row->stock > 0)
                            <td>{{ $row->stock }}</td>
                        @else
                            <td>Not Available</td>
                        @endif

                        <td>{{ $row->unit_price }}</td>
                        <td>{{ $row->sales_unit_price }}</td>
                        <td>
                            <a href="{{ route('new.order') }}" class="btn btn-sm btn-info btn-block mb-1">Order</a>
                        </td>
                    </tr>
                    @endforeach
                    
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end flex-wrap mb-4">
    <a href="{{url('products/multiple/create')}}" class="btn btn-sm btn-primary m-2">
        <i class="fas fa-box"></i> New Product <i class="fas fa-plus"></i>
    </a>
    <a href="{{url('products')}}" class="btn btn-sm btn-info m-2">
        <i class="fas fa-cubes"></i> Go to Stock <i class="fas fa-shopping-cart"></i>
    </a>
</div>
@endsection

@section('scripts')
<link href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap4.min.css" rel="stylesheet" />
<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        responsive: true,
        columnDefs: [
            { orderable: false, targets: 6 } // Action column
        ]
    });
});
</script>
@endsection
