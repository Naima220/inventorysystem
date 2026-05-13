@extends('layouts.admin_master')

@section('content')

<style>
/* MOBILE RESPONSIVE */
@media (max-width: 768px) {
    .top-header {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 10px;
    }

    .table-responsive {
        overflow-x: auto;
    }
}
</style>

<div class="container-fluid mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3 top-header">
        <h3 class="mb-0">All Customers</h3>

        <a href="{{ route('add.customer') }}" class="btn btn-primary btn-sm px-3">
            <i class="fas fa-user-plus"></i> New Customer
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">

            <!-- RESPONSIVE TABLE WRAPPER -->
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0" id="customersTable">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Gender</th>
                            <th>Address</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $key => $customer)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $customer->customer_name }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->phone }}</td>
                            <td>{{ $customer->gender }}</td>
                            <td>{{ $customer->address }}</td>
                            <td class="text-center">

                                <a href="{{ route('edit.customer', $customer->id) }}"
                                   class="btn btn-info btn-sm mx-1">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('delete.customer', $customer->id) }}" method="POST"
                                      class="d-inline-block"
                                      onsubmit="return confirm('Ma hubtaa inaad tirtirayso?');">
                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-danger btn-sm mx-1">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- END -->

        </div>
    </div>

</div>
@endsection

@section('scripts')

<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.1/css/buttons.bootstrap4.min.css" />

<script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<script>
$(document).ready(function () {
    $('#customersTable').DataTable({
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copy',
                text: '<i class="fas fa-copy"></i>',
                className: 'btn btn-sm btn-secondary'
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i>',
                className: 'btn btn-sm btn-success'
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i>',
                className: 'btn btn-sm btn-danger'
            },
            {
                extend: 'colvis',
                text: '<i class="fas fa-columns"></i>',
                className: 'btn btn-sm btn-info'
            }
        ],
        columnDefs: [
            { orderable: false, targets: -1 }
        ],
        paging: true,
        searching: true,
        autoWidth: false
    });
});
</script>

@endsection