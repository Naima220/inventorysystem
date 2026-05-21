@extends('layouts.admin_master')
@section('content')

<style>
/* MOBILE CARD DESIGN */
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
        background: #fff;
        margin-bottom: 15px;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    #dataTable td {
        text-align: left;
        padding: 8px 5px;
        border: none;
        position: relative;
    }

    #dataTable td::before {
        content: attr(data-label);
        font-weight: bold;
        display: block;
        color: #6b7280;
        margin-bottom: 3px;
    }

    .action-buttons a,
    .action-buttons button {
        width: 100%;
        margin-bottom: 5px;
    }
}
</style>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
        <span><i class="fas fa-table mr-1"></i> Employees List</span>

        <a href="{{ route('add.employee') }}" class="btn btn-sm btn-primary mt-2 mt-md-0">
            <i class="fas fa-users"></i> New Employee <i class="fas fa-plus"></i>
        </a>
    </div>

    <div class="card-body">

        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Position</th>
                    <th>Hire Date</th>
                    <th>Salary</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach($employees as $employee)
                <tr>
                    <td data-label="Name">{{ $employee->name }}</td>
                    <td data-label="Email">{{ $employee->email }}</td>
                    <td data-label="Phone">{{ $employee->phone }}</td>
                    <td data-label="Position">{{ $employee->position }}</td>
                    <td data-label="Hire Date">{{ $employee->hire_date }}</td>
                    <td data-label="Salary">${{ number_format($employee->salary, 2) }}</td>

                    <td data-label="Status">
                        @if($employee->status === 'Active')
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>

                    <td data-label="Action" class="action-buttons">
                        <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-sm btn-info">Edit</a>

                        <form action="{{ route('employees.destroy', $employee->id) }}" method="POST"
                              onsubmit="return confirm('Delete this employee?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger mt-1">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>

@endsection

@section('script')
<link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />

<script>
$('#dataTable').DataTable({
    responsive: false, // IMPORTANT: disable default responsive (we use custom)
    columnDefs: [
        { bSortable: false, targets: [7] }
    ],
    dom: 'lBfrtip',
    buttons: [
        {
            extend: 'copyHtml5',
            exportOptions: { columns: [0,1,2,3,4,5,6] }
        },
        {
            extend: 'excelHtml5',
            exportOptions: { columns: [0,1,2,3,4,5,6] }
        },
        {
            extend: 'pdfHtml5',
            exportOptions: { columns: [0,1,3,5,6] }
        },
        'colvis'
    ],
});
</script>
@endsection