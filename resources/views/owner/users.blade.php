@extends('adminlte::page')

@section('title', 'User List')

@section('content_header')
    <h1>User List</h1>
@stop

@section('content')
    <div class="card">
        <!-- <div class="card-header">
            <h3 class="card-title">Managers and Supervisors</h3>
        </div> -->
        <div class="card-body">
            <!-- <form method="GET" action="{{ route('owner.user_list') }}">
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
                            <th>Role</th>
                            <th>Balance</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->roles->pluck('name')->join(', ') }}</td>
                                <td>₹{{ formatIndianCurrency($user->balance) }}</td>
                                <td>
                                    <a href="{{ route('owner.user_projects', $user->id) }}" class="btn btn-sm btn-primary full-width-view">View</a>
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
@stop

@section('js')
<script>
    $(document).ready(function() {
            var table = $('#users-table').DataTable({
                responsive: {
                    details: {
                        display: $.fn.dataTable.Responsive.display.childRowImmediate,
                        type: 'inline',
                        }
                },
                autoWidth: false,
                order: [[0, 'desc']],
        });

        table.rows().every(function(rowIdx, tableLoop, rowLoop) {
                var row = this;
                if (!row.child.isShown()) {
                    row.child.show();
                    $(row.node()).addClass('shown');  
                }
            });
    

        toastr.options = {
            positionClass: 'toast-bottom-right',
            hideDuration: 5000,
            timeOut: 5000,
            extendedTimeOut: 1000,
        };

        @if(session('success'))
            toastr.success('{{ session('success') }}');
        @endif
        @if(session('error'))
            toastr.error('{{ session('error') }}');
        @endif
    });
</script>
@stop
