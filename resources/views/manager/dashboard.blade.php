@extends('adminlte::page')

@section('title', 'List of Active Project Site')

@section('content_header')
    <h1>List of Active Project Site</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">List of Active Project Site</h3>
            <div class="card-tools">
                <a href="{{ route('projects.create') }}" class="btn btn-success create-btn">Create Project</a>
            </div>
        </div>
        <div class="card-body">
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
            <!-- <form method="GET" action="{{ route('manager.home') }}">
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
                <table class="table table-bordered" id="projects-table">
                    <thead>
                        <tr>
                            <th>Project ID</th>
                            <th>Project Site Name</th>
                            <th>Location</th>
                            <th>Start Date</th>
                            <th>Project Balance</th>
                            <th>Status</th>
                            <th>Supervisors</th>
                            <th>Note</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($projects as $project)
                            <tr>
                                <td>{{ $project->id }}</td>
                                <td>{{ $project->name }}</td>
                                <td>{{ $project->location }}</td>
                                <td>{{ \Carbon\Carbon::parse($project->start_date)->format('d F Y') }}</td>
                                <td>{{ $project->supervisorBalance  }}</td>
                                <td>{{ $project->status }}</td>
                                <td>
                                    @if ($project->supervisors && $project->supervisors->count() > 0)
                                        @foreach ($project->supervisors as $supervisor)
                                            {{ $supervisor->name }}<br>
                                        @endforeach
                                    @else
                                        No supervisors assigned
                                    @endif
                                </td>
                                <td>{{ $project->note }}</td>
                                <td>
                                    <a href="{{ route('manager.projects.show', $project->id) }}" class="btn btn-sm btn-primary full-width-view">View</a>
                                    <a href="{{ route('projects.challans.index', $project->id) }}" class="btn btn-sm btn-success full-width-approve">Challan</a>
                                    <form action="{{ route('projects.destroy', $project->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger full-width-cancel d-none" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div id="list-view1" class="d-none">
                <ul class="list-group" id="projects-list"></ul>
            </div>
        </div>
    </div>
@stop

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script>
    $(document).ready(function() {
            var table = $('#projects-table').DataTable({
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
            toastr.options = {
            positionClass: 'toast-bottom-right',
            hideDuration: 5000,
            timeOut: 5000,
            extendedTimeOut: 1000,
        };
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
