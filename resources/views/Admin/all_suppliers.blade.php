@extends('layouts.admin_master')
@section('content')

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-table mr-1"></i>
       All Suppliers
    </div>
    <div class="card-body">
        <div style="text-align: right;">
            <a href="{{ url('add-supplier') }}" class="btn btn-sm btn-primary" style="padding: 5px; margin: 5px;">
                <i class="fas fa-truck"></i> New Supplier <i class="fas fa-plus"></i>
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Gender</th>
                        <th>Address</th>
                        <th>Phone</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($suppliers as $row)
                    <tr>
                        <td>{{ $row->supplier_name }}</td>
                        <td>{{ $row->email }}</td>
                        <td>{{ $row->gender }}</td>
                        <td>{{ $row->address }}</td>
                        <td>{{ $row->phone }}</td>
                        <td>
                            <a href="{{ url('edit_supplier/'.$row->id) }}" class="btn btn-sm btn-info">Edit</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />

<script>
    $('#dataTable').DataTable({
        columnDefs: [
            { bSortable: false, targets: [5] }
        ],
        dom: 'lBfrtip',
        buttons: [
            {
                extend: 'copyHtml5',
                exportOptions: {
                    modifier: { page: 'current' },
                    columns: [0, ':visible']
                }
            },
            {
                extend: 'excelHtml5',
                exportOptions: {
                    modifier: { page: 'current' },
                    columns: [0, ':visible']
                }
            },
            {
                extend: 'pdfHtml5',
                exportOptions: {
                    modifier: { page: 'current' },
                    columns: [0, 1, 2, 5]
                }
            },
            'colvis'
        ],
    });
</script>
@endsection
