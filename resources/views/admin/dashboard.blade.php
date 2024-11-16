@extends('adminlte::page')

@section('title', 'Admin Dashboard')

@section('content_header')
    <!-- <h1>Admin Dashboard</h1> -->
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            <h3 class="card-title">User List</h3>
            <div class="card-tools">
                <a href="{{ route('admin.users.create') }}" class="btn btn-success create-btn">Add User</a>
            </div>
        </div>
        <div class="card-body">
            <!-- <form method="GET" action="{{ route('admin.dashboard') }}">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ old('end_date', \Carbon\Carbon::parse($endDate)->format('Y-m-d')) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group d-flex justify-content-start align-items-end">
                            <button type="submit" class="btn small-width-view">Filter</button>
                        </div>
                    </div>
                </div>
            </form> -->
            <div id="table-view1">
            <table class="table table-bordered" id="users-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Mobile No.</th>
                                <th>User ID</th>
                                <th>Role</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $index => $user)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->mobile_no }}</td>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->roles->pluck('name')->join(', ') }}</td>
                                    <td>
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-primary full-width-view">Edit</a>
                                        <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger full-width-cancel" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                        <a href="{{ route('admin.users.change_password_form', ['id' => $user->id]) }}" class="btn btn-sm btn-warning full-width-approve">
                                            Change Password
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
            </div>
            <div id="list-view1" class="d-none">
                <ul class="list-group" id="users-list"></ul>
            </div>
        </div>
    </div>

    <!-- Password Change Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="changePasswordForm" method="POST" action="">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="new_password_confirmation">Confirm New Password</label>
                            <input type="password" name="new_password_confirmation" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary full-width-approve">Change Password</button>
                        <button type="button" class="btn btn-secondary full-width-cancel" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script> -->
<script>
    $(document).ready(function() {
        var table = $('#users-table').DataTable({
                responsive: {
                    details: {
                        display: $.fn.dataTable.Responsive.display.childRowImmediate,
                        type: 'inline',
                        }
                },
                autoWidth: false,order: [[0, 'desc']],
            });

            table.rows().every(function(rowIdx, tableLoop, rowLoop) {
                var row = this;
                if (!row.child.isShown()) {
                    row.child.show();
                    $(row.node()).addClass('shown');  
                }
            });

        $(document).on('click', '.change-password-btn', function() {
            var userId = $(this).data('id');
            var formAction = '{{ url('/admin/users/change-password') }}/' + userId;
            $('#changePasswordForm').attr('action', formAction);
        });
    });
</script>
@stop
