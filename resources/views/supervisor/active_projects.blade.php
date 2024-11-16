@extends('adminlte::page')

@section('title', 'Supervisor Dashboard')

@section('content_header')
    <h1>Projects</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            
            <h3 class="card-title">Assigned Projects</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('supervisor.activeProjects') }}">
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
            </form>
            <!-- <div class="form-group">
                <label>Avaiable Balance:</label>
                <p id="overall-balance">{{ $overallBalance }}</p>
            </div> -->
            <div id="table-view1">
                <table class="table table-bordered" id="projects-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Start Date</th>
                            <th>Status</th>
                            <th>Project Balance</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($projects as $project)
                            <tr>
                                <td>{{ $project->id }}</td>
                                <td>{{ $project->name }}</td>
                                <td>{{ $project->location }}</td>
                                <td>{{ \Carbon\Carbon::parse($project->start_date)->format('h:i A d/m/Y') }}</td>
                                <td>{{ $project->status }}</td>
                                <td>₹{{ formatIndianCurrency($project->supervisorBalance)  }}</td>
                                <td>
                                    <a href="{{ route('supervisor.projects.show', $project->id) }}" class="btn btn-sm btn-primary full-width-view">View</a>
                                    <a href="{{ route('projects.challans.index', $project->id) }}" class="btn btn-sm btn-success full-width-approve">Challan</a>
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

        @if(session('success'))
            toastr.success('{{ session('success') }}');
        @endif
        @if(session('error'))
            toastr.error('{{ session('error') }}');
        @endif
    });
</script>
@stop
