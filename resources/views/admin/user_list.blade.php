@extends('adminlte::page')

@section('title', 'User List')

@section('content_header')
    <h1>User List</h1>
@stop

@section('content')
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
    </table>
@stop

@section('js')
    <script>
    $(function() {
        $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('admin.get_users') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'name', name: 'name' },
                { data: 'mobile_no', name: 'mobile_no' },
                { data: 'id', name: 'id' },
                { data: 'role', name: 'role' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });
    });
    </script>
@stop
