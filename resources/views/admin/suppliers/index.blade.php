@extends('layouts.admin_master')

@section('content')

<style>
/* ========= MOBILE RESPONSIVE TABLE ========= */
@media (max-width: 768px) {

    table thead {
        display: none;
    }

    table,
    table tbody,
    table tr,
    table td {
        display: block;
        width: 100%;
    }

    table tr {
        background: #ffffff;
        border: 1px solid #ddd;
        border-radius: 10px;
        margin-bottom: 15px;
        padding: 12px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }

    table td {
        border: none;
        padding: 6px 0;
        font-size: 14px;
    }

    table td::before {
        font-weight: bold;
        color: #555;
        display: block;
        margin-bottom: 2px;
    }

    table td:nth-of-type(1)::before { content: "ID"; }
    table td:nth-of-type(2)::before { content: "Supplier Name"; }
    table td:nth-of-type(3)::before { content: "Email"; }
    table td:nth-of-type(4)::before { content: "Gender"; }
    table td:nth-of-type(5)::before { content: "Address"; }
    table td:nth-of-type(6)::before { content: "Phone"; }
    table td:nth-of-type(7)::before { content: "Actions"; }

    table td:last-child a,
    table td:last-child button {
        width: 100%;
        margin-bottom: 6px;
    }
}
</style>

<div class="container mt-4">
    <h2 class="mb-3">Suppliers List</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered mt-3">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Supplier Name</th>
                    <th>Email</th>
                    <th>Gender</th>
                    <th>Address</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($suppliers as $supplier)
                <tr>
                    <td>{{ $supplier->id }}</td>
                    <td>{{ $supplier->supplier_name }}</td>
                    <td>{{ $supplier->email }}</td>
                    <td>{{ $supplier->gender }}</td>
                    <td>{{ $supplier->address }}</td>
                    <td>{{ $supplier->phone }}</td>
                    <td>
                        <a href="{{ route('suppliers.edit', $supplier->id) }}"
                           class="btn btn-sm btn-info">
                            Edit
                        </a>

                        <form action="{{ route('suppliers.destroy', $supplier->id) }}"
                              method="POST"
                              class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    onclick="return confirm('Are you sure?')"
                                    class="btn btn-sm btn-danger">
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
@endsection
