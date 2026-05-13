@extends('layouts.admin_master')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Backup Management</h1>
        <form action="{{ route('superadmin.backups.global') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary shadow-sm">
                <i class="fas fa-download fa-sm text-white-50 mr-2"></i> Run Global Backup
            </button>
        </form>
    </div>

    <div class="card shadow mb-4 content-card">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Shop Databases</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Shop ID</th>
                            <th>Shop Name</th>
                            <th>Database</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($shops as $shop)
                        <tr>
                            <td>{{ $shop->id }}</td>
                            <td>{{ $shop->name }}</td>
                            <td><code>{{ $shop->database()->getName() }}</code></td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('superadmin.backups.download', $shop->id) }}" class="btn btn-success btn-sm mr-2">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#restoreModal{{ $shop->id }}">
                                        <i class="fas fa-upload"></i> Restore
                                    </button>
                                </div>

                                <!-- Restore Modal -->
                                <div class="modal fade" id="restoreModal{{ $shop->id }}" tabindex="-1" role="dialog" aria-labelledby="restoreModalLabel{{ $shop->id }}" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="{{ route('superadmin.backups.restore', $shop->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="restoreModalLabel{{ $shop->id }}">Restore Database: {{ $shop->name }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="alert alert-danger">
                                                        <i class="fas fa-exclamation-triangle"></i> <strong>Warning!</strong> Restoring a backup will overwrite the current database. This action cannot be undone.
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="backup_file">Select SQL Backup File</label>
                                                        <input type="file" name="backup_file" class="form-control-file" accept=".sql" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-warning">Start Restore</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
</script>
@endsection
