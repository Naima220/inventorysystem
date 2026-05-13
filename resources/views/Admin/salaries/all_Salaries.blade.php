@extends('layouts.admin_master')

@section('content')

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-money-bill-wave mr-1"></i>
        All Salaries
    </div>
    <div class="card-body">
        <div style="text-align: right;">
            <a href="{{ route('add.salary') }}" class="btn btn-sm btn-primary" style="padding: 5px; margin: 5px;">
                <i class="fas fa-plus"></i> New Salary
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                     <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
                        <th>Month</th>
                        <th>Amount</th>
                        <th>Payment Date</th>
                        <th>Note</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salaries as $salary)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $salary->employee ? $salary->employee->name : 'N/A' }}</td>
                         <td>{{ $salary->month }}</td>
                        <td>${{ $salary->amount }}</td>
                        <td>{{ $salary->payment_date }}</td>
                        <td>{{ $salary->note }}</td>
                        <td>
                            <a href="{{ route('salaries.edit', $salary->id) }}" class="btn btn-sm btn-info">Edit</a>

                            <form method="POST" action="{{ route('salaries.destroy', $salary->id) }}" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" type="submit" onclick="return confirm('Are you sure?')">
                                    Delete
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
<!-- Buttons CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.1/css/buttons.bootstrap4.min.css" />

<!-- Buttons JS -->
<script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.colVis.min.js"></script>

<!-- Excel/PDF support -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'excel', 'pdf', 'colvis'
            ],
            columnDefs: [
                { orderable: false, targets: [6] }
            ]
        });
    });
</script>
@endsection